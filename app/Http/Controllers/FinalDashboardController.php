<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\PartnerFacility;
use App\Models\AgeDashboard;
use App\Models\Indicator;
use App\Models\ETLAppointment;
use App\Models\ETLClient;
use App\Models\Txcurr;
use Auth;
use DB;
use Carbon\Carbon;

class FinalDashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
        }
    }
    public function appointment()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_tx_cur.mfl_code', Auth::user()->facility_id)
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('marital')
                ->get();
            $client_list = DB::table('etl_client_detail')->select(
                'etl_client_detail.upi_no',
                'etl_client_detail.ccc_number',
                'etl_client_detail.dob',
                'etl_client_detail.consented',
                'etl_client_detail.client_status',
                DB::raw('COUNT(etl_appointment_detail.app_kept) AS kept_app '),
                DB::raw('SUM(etl_appointment_detail.app_not_kept) AS not_kept_app ')
            )
                ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                ->groupBy('etl_appointment_detail.client_id')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('facility')
                ->get();
            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
                DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->get();
            $client_app_list = DB::table('etl_client_detail')->select(
                'etl_client_detail.upi_no',
                'etl_client_detail.ccc_number',
                'etl_client_detail.dob',
                'etl_client_detail.consented',
                'etl_client_detail.client_status',
                'etl_appointment_detail.days_defaulted',
                'etl_appointment_detail.final_outcome'
            )
                ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('etl_appointment_detail.final_outcome')
                ->groupBy('etl_appointment_detail.client_id')
                ->get();
            $app_period = DB::table('etl_appointment_detail')->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('mfl_code', Auth::user()->facility_id)
                ->where('appointment_date', '<=', date("Y-M-D"))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('new_date', 'ASC')
                ->groupBy('new_date')
                ->get();
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $partners = DB::table('tbl_partner')->select('id', 'name')->where('status', '=', 'Active')->get();
            $counties = DB::table('tbl_county')->select('id', 'name')->get();
            $sub_counties = DB::table('tbl_sub_county')->select('id', 'name')->get();
            $facilities = DB::table('tbl_master_facility')->select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->get();
            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->get();
            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),

            )
                ->groupBy('facility')
                ->get();
            $client_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->get();

            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
                DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')
            )
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->groupBy('partner')
                ->get();
            $client_app_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->get();
            $app_period = DB::table('etl_appointment_detail')->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('appointment_date', '<=', date("Y-M-D"))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('new_date', 'ASC')
                ->groupBy('new_date')
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),

            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('facility')
                ->get();
            $client_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->get();

            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
                DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('partner')
                ->get();
            $client_app_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->get();
            $app_period = DB::table('etl_appointment_detail')->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('partner_id', Auth::user()->partner_id)
                ->where('appointment_date', '<=', date("Y-M-D"))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('new_date', 'ASC')
                ->groupBy('new_date')
                ->get();
        }
        if (Auth::user()->access_level == 'Sub County') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('facility')
                ->get();
            $client_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->get();

            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
                DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->get();
            $client_app_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->get();
            $app_period = DB::table('etl_appointment_detail')->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->where('appointment_date', '<=', date("Y-M-D"))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('new_date', 'ASC')
                ->groupBy('new_date')
                ->get();
        }
        if (Auth::user()->access_level == 'County') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))
                ->where('county_id', Auth::user()->county_id)
                ->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('facility')
                ->get();
            $client_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->get();

            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
                DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
                DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
                DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
                DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
                DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')

            )
                ->where('county_id', Auth::user()->county_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
                ->get();
            $client_app_list = DB::table('etl_client_detail')->select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->get();
            $app_period = DB::table('etl_appointment_detail')->select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('county_id', Auth::user()->county_id)
                ->where('appointment_date', '<=', date("Y-M-D"))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('new_date', 'ASC')
                ->groupBy('new_date')
                ->get();
        }


        return view('dashboard.appointment', compact(
            'all_appoinments',
            'consented_clients',
            'all_tx_curr',
            'appointment_gender',
            'appointment_age',
            'appointment_marital',
            'appointment_partner',
            'appointment_county',
            'client_missed',
            'missed_age',
            'missed_gender',
            'missed_marital',
            'missed_county',
            'missed_partner',
            'client_list',
            'client_app_list',
            'app_period',
            'partners',
            'counties',
            'sub_counties',
            'facilities'
        ));
    }

    public function filter_data(Request $request)
    {
        $data                = [];
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        $partners = DB::table('tbl_partner')->select('id', 'name')->where('status', '=', 'Active')->get();
        $counties = DB::table('tbl_county')->select('id', 'name')->get();
        $sub_counties = DB::table('tbl_sub_county')->select('id', 'name')->get();
        $facilities = DB::table('tbl_master_facility')->select('tbl_master_facility.code', 'tbl_master_facility.name')
            ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
            ->get();
        $all_appoinments = ETLAppointment::select(
            DB::raw('COUNT(*) as total_app'),
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(future) AS future '),
            DB::raw('SUM(received_sms) AS messages '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
            DB::raw('AVG(percent_future) AS percent_future ')
        );
        $consented_clients = ETLClient::select(
            DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
            DB::raw('AVG(percent_consented) AS percent_consented ')
        );
        $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
            ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code');
        $appointment_gender = ETLAppointment::select(
            'gender',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('gender');
        $appointment_age = ETLAppointment::select(
            'age_group',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('age_group');
        $appointment_marital = ETLAppointment::select(
            'marital',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('marital');
        $appointment_county = ETLAppointment::select(
            'county',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('county');
        $appointment_partner = ETLAppointment::select(
            'partner',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('AVG(percent_kept) AS percent_kept '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('partner');
        $appointment_facility = DB::table('etl_appointment_detail')->select(
            'facility',
            DB::raw('SUM(app_kept) AS kept_app '),
            DB::raw('SUM(app_not_kept) AS not_kept_app '),

        )
            ->groupBy('facility');
        $client_list = DB::table('etl_client_detail')->select(
            DB::raw('COUNT(ccc_number) AS ccc_number ')
        );

        // missed appointment
        $client_missed = DB::table('etl_appointment_detail')->select(
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(received_sms) AS messages '),
            DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages '),
            DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages '),
            DB::raw('SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages '),
            DB::raw('SUM(called) AS called '),
            DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called '),
            DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called '),
            DB::raw('SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called '),
            DB::raw('SUM(physically_traced) AS physically_traced '),
            DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced '),
            DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced '),
            DB::raw('SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome '),
            DB::raw('SUM(CASE WHEN consent = "Yes" THEN 1 ELSE 0 END) AS consent '),
            DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent '),
            DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent '),
            DB::raw('SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent ')
        );

        $missed_age = DB::table('etl_appointment_detail')->select(
            'age_group',
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('age_group');
        $missed_gender = DB::table('etl_appointment_detail')->select(
            'gender',
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('gender');
        $missed_marital = DB::table('etl_appointment_detail')->select(
            'marital',
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('marital');
        $missed_county = DB::table('etl_appointment_detail')->select(
            'county',
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('county');
        $missed_partner = DB::table('etl_appointment_detail')->select(
            'partner',
            DB::raw('SUM(app_not_kept) AS not_kept_app '),
            DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )
            ->groupBy('partner');
        $client_app_list = DB::table('etl_client_detail')->select(
            DB::raw('COUNT(ccc_number) AS ccc_number ')
        );
        $app_period = DB::table('etl_appointment_detail')->select(
            DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
            DB::raw('AVG(percent_rtc) AS percent_rtc '),
            DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
        )->whereNotNull('appointment_date')
            ->where('appointment_date', '<=', date("Y-M-D"))
            ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
            ->orderBy('new_date', 'ASC')
            ->groupBy('new_date');

            if (!empty($selected_partners)) {
                $all_appoinments = $all_appoinments->where('partner_id', $selected_partners);
                $consented_clients = $consented_clients->where('partner_id', $selected_partners);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.partner_id', $selected_partners);
                $appointment_gender = $appointment_gender->where('partner_id', $selected_partners);
                $appointment_age = $appointment_age->where('partner_id', $selected_partners);
                $appointment_marital = $appointment_marital->where('partner_id', $selected_partners);
                $appointment_county = $appointment_county->where('partner_id', $selected_partners);
                $appointment_partner = $appointment_partner->where('partner_id', $selected_partners);
                $appointment_facility = $appointment_facility->where('partner_id', $selected_partners);
                $client_list = $client_list->where('partner_id', $selected_partners);
                $client_missed = $client_missed->where('partner_id', $selected_partners);
                $missed_age = $missed_age->where('partner_id', $selected_partners);
                $missed_gender = $missed_gender->where('partner_id', $selected_partners);
                $missed_marital = $missed_marital->where('partner_id', $selected_partners);
                $missed_county = $missed_county->where('partner_id', $selected_partners);
                $missed_partner = $missed_partner->where('partner_id', $selected_partners);
                $client_app_list = $client_app_list->where('partner_id', $selected_partners);
                $app_period = $app_period->where('partner_id', $selected_partners);
            }
            if (!empty($selected_counties)) {
                $all_appoinments = $all_appoinments->where('county_id', $selected_counties);
                $consented_clients = $consented_clients->where('county_id', $selected_counties);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.county_id', $selected_counties);
                $appointment_gender = $appointment_gender->where('county_id', $selected_counties);
                $appointment_age = $appointment_age->where('county_id', $selected_counties);
                $appointment_marital = $appointment_marital->where('county_id', $selected_counties);
                $appointment_county = $appointment_county->where('county_id', $selected_counties);
                $appointment_partner = $appointment_partner->where('county_id', $selected_counties);
                $appointment_facility = $appointment_facility->where('county_id', $selected_counties);
                $client_list = $client_list->where('county_id', $selected_counties);
                $client_missed = $client_missed->where('county_id', $selected_counties);
                $missed_age = $missed_age->where('county_id', $selected_counties);
                $missed_gender = $missed_gender->where('county_id', $selected_counties);
                $missed_marital = $missed_marital->where('county_id', $selected_counties);
                $missed_county = $missed_county->where('county_id', $selected_counties);
                $missed_partner = $missed_partner->where('county_id', $selected_counties);
                $client_app_list = $client_app_list->where('county_id', $selected_counties);
                $app_period = $app_period->where('county_id', $selected_counties);
            }
            if (!empty($selected_subcounties)) {
                $all_appoinments = $all_appoinments->where('subcounty_id', $selected_subcounties);
                $consented_clients = $consented_clients->where('subcounty_id', $selected_subcounties);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
                $appointment_gender = $appointment_gender->where('subcounty_id', $selected_subcounties);
                $appointment_age = $appointment_age->where('subcounty_id', $selected_subcounties);
                $appointment_marital = $appointment_marital->where('subcounty_id', $selected_subcounties);
                $appointment_county = $appointment_county->where('subcounty_id', $selected_subcounties);
                $appointment_partner = $appointment_partner->where('subcounty_id', $selected_subcounties);
                $appointment_facility = $appointment_facility->where('subcounty_id', $selected_subcounties);
                $client_list = $client_list->where('subcounty_id', $selected_subcounties);
                $client_missed = $client_missed->where('subcounty_id', $selected_subcounties);
                $missed_age = $missed_age->where('subcounty_id', $selected_subcounties);
                $missed_gender = $missed_gender->where('subcounty_id', $selected_subcounties);
                $missed_marital = $missed_marital->where('subcounty_id', $selected_subcounties);
                $missed_county = $missed_county->where('subcounty_id', $selected_subcounties);
                $missed_partner = $missed_partner->where('subcounty_id', $selected_subcounties);
                $client_app_list = $client_app_list->where('subcounty_id', $selected_subcounties);
                $app_period = $app_period->where('subcounty_id', $selected_subcounties);
            }
            if (!empty($selected_facilites)) {
                $all_appoinments = $all_appoinments->where('mfl_code', $selected_facilites);
                $consented_clients = $consented_clients->where('mfl_code', $selected_facilites);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.mfl_code', $selected_facilites);
                $appointment_gender = $appointment_gender->where('mfl_code', $selected_facilites);
                $appointment_age = $appointment_age->where('mfl_code', $selected_facilites);
                $appointment_marital = $appointment_marital->where('mfl_code', $selected_facilites);
                $appointment_county = $appointment_county->where('mfl_code', $selected_facilites);
                $appointment_partner = $appointment_partner->where('mfl_code', $selected_facilites);
                $appointment_facility = $appointment_facility->where('mfl_code', $selected_facilites);
                $client_list = $client_list->where('mfl_code', $selected_facilites);
                $client_missed = $client_missed->where('mfl_code', $selected_facilites);
                $missed_age = $missed_age->where('mfl_code', $selected_facilites);
                $missed_gender = $missed_gender->where('mfl_code', $selected_facilites);
                $missed_marital = $missed_marital->where('mfl_code', $selected_facilites);
                $missed_county = $missed_county->where('mfl_code', $selected_facilites);
                $missed_partner = $missed_partner->where('mfl_code', $selected_facilites);
                $client_app_list = $client_app_list->where('mfl_code', $selected_facilites);
                $app_period = $app_period->where('mfl_code', $selected_facilites);
            }
            $data["all_appoinments"] = $all_appoinments->get();
            $data["consented_clients"] = $consented_clients->get();
            $data["all_tx_curr"] = $all_tx_curr->get();
            $data["appointment_gender"] = $appointment_gender->get();
            $data["appointment_age"] = $appointment_age->get();
            $data["appointment_marital"] = $appointment_marital->get();
            $data["appointment_county"] = $appointment_county->get();
            $data["appointment_partner"] = $appointment_partner->get();
            $data["appointment_facility"] = $appointment_facility->get();
            $data["client_list"] = $client_list->get();
            $data["client_missed"] = $client_missed->get();
            $data["missed_age"] = $missed_age->get();
            $data["missed_gender"] = $missed_gender->get();
            $data["missed_marital"] = $missed_marital->get();
            $data["missed_county"] = $missed_county->get();
            $data["missed_partner"] = $missed_partner->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
    }
}
