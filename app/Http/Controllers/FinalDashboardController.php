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
    public function appointment()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_tx_cur.mfl_code', Auth::user()->facility_id)
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('facility')
                ->get();
            // missed appointment
            $client_missed = DB::table('etl_appointment_detail')->select(
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
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
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages ')
            )
                ->get();
            $consented_clients = ETLClient::select(DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '))->get();
            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->get();
            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->groupBy('county')
                ->get();
            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->groupBy('marital')
                ->get();
            $missed_county = DB::table('etl_appointment_detail')->select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->groupBy('county')
                ->get();
            $missed_partner = DB::table('etl_appointment_detail')->select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->groupBy('partner')
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('marital')
                ->get();
            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
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
        }
        if (Auth::user()->access_level == 'Sub County') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
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
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->get();
        }
        if (Auth::user()->access_level == 'County') {

            $all_appoinments = ETLAppointment::select(
                DB::raw('COUNT(*) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->get();
            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->get();
            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
                ->get();
            $appointment_facility = DB::table('etl_appointment_detail')->select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
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
                DB::raw('SUM(called) AS called '),
                DB::raw('SUM(physically_traced) AS physically_traced '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->get();

            $missed_age = DB::table('etl_appointment_detail')->select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->get();
            $missed_gender = DB::table('etl_appointment_detail')->select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->get();
            $missed_marital = DB::table('etl_appointment_detail')->select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
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
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
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
            'client_list'
        ));
    }
}
