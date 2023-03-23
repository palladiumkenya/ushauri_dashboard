<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Client;
use App\Models\Appointments;
use App\Models\Clinic;
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
use Illuminate\Support\Facades\Auth as FacadesAuth;

class FinalDashboardController extends Controller
{
    protected  $remember_period ;

    public function __construct()
    {
        $this->remember_period  = env('REMEMBER_PERIOD', '60 * 60');
    }

    public function index()
    {
        // dd(Auth::user()->access_level);

        $indicator_k = Indicator::where('name', 'Kept&Not Kept')
            ->select('description')
            ->remember($this->remember_period)
            ->get();

        $indicator_m = Indicator::where('name', 'Missed Appointments')
            ->select('description')
            ->remember($this->remember_period)
            ->get();

        $clinics = Clinic::select('id', 'name')
            ->where('status', '=', 'Active')
            ->remember($this->remember_period)
            ->get();

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $partners = Partner::where("status", "=", "Active")
            ->remember($this->remember_period)
            ->get();

            $counties = County::where("status", "=", "Active")
            ->remember($this->remember_period)
            ->get(['id', 'name']);

            $sub_counties = SubCounty::where("status", "=", "Active")
            ->remember($this->remember_period)
            ->get(['id', 'name']);

            $facilities = Facility::join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
            ->remember($this->remember_period)
            ->get(['tbl_master_facility.code', 'tbl_master_facility.name']);

            return view('dashboard.appointment', compact('partners', 'counties', 'sub_counties', 'facilities', 'clinics', 'indicator_k', 'indicator_m'));
        }
        if (Auth::user()->access_level == 'Facility') {

            $client_list = ETLClient::join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                        ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                        ->groupBy('etl_appointment_detail.client_id')
                        ->remember($this->remember_period)
                        ->get(['etl_client_detail.upi_no',
                        'etl_client_detail.ccc_number',
                        'etl_client_detail.dob',
                        'etl_client_detail.consented',
                        'etl_client_detail.client_status',
                        'etl_client_detail.client_name',
                        'etl_client_detail.dsd_status',
                        'etl_client_detail.phone_no',
                        DB::raw('COUNT(etl_appointment_detail.app_kept) AS kept_app '),
                        DB::raw('SUM(etl_appointment_detail.app_not_kept) AS not_kept_app ')]);

            $client_app_list = ETLClient::join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
            ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
            ->whereNotNull('etl_appointment_detail.final_outcome')
            ->groupBy('etl_appointment_detail.client_id')
            ->remember($this->remember_period)
            ->get(['etl_client_detail.upi_no',
            'etl_client_detail.ccc_number',
            'etl_client_detail.dob',
            'etl_client_detail.consented',
            'etl_client_detail.client_status',
            'etl_client_detail.client_name',
            'etl_client_detail.phone_no',
            'etl_client_detail.dsd_status',
            'etl_appointment_detail.days_defaulted',
            'etl_appointment_detail.final_outcome']);

            return view('dashboard.appointment', compact('clinics', 'client_list', 'client_app_list', 'indicator_k', 'indicator_m'));
        }
        if (Auth::user()->access_level == 'County') {

            $partners = Partner::select('tbl_partner.id', 'tbl_partner.name')
                ->join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')
                ->where('tbl_partner.status', '=', 'Active')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_partner.name')
                ->remember($this->remember_period)
                ->get();

            $counties = County::where("status", "=", "Active")
                ->remember($this->remember_period)
                ->get(['id', 'name']);

            $sub_counties = SubCounty::select('tbl_sub_county.id', 'tbl_sub_county.name')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->groupBy('tbl_sub_county.name')
                ->remember($this->remember_period)
                ->get();


            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->get();

            return view('dashboard.appointment', compact('partners','counties', 'sub_counties', 'facilities', 'clinics', 'indicator_k', 'indicator_m'));
        }
        if (Auth::user()->access_level == 'Sub County') {

            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->get();

            return view('dashboard.appointment', compact('facilities', 'clinics', 'indicator_k', 'indicator_m'));
        }
        if (Auth::user()->access_level == 'Partner') {

            $facilities = Facility::select('tbl_master_facility.code', 'tbl_master_facility.name')
                ->join('tbl_partner_facility', 'tbl_master_facility.code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->get();

            $counties = County::select('tbl_county.id', 'tbl_county.name')
                ->join('tbl_partner_facility', 'tbl_county.id', '=', 'tbl_partner_facility.county_id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->groupBy('tbl_county.name')
                ->remember($this->remember_period)
                ->get();

            $sub_counties = SubCounty::select('tbl_sub_county.id', 'tbl_sub_county.name')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->groupBy('tbl_sub_county.name')
                ->remember($this->remember_period)
                ->get();

            return view('dashboard.appointment', compact('facilities', 'clinics', 'counties', 'sub_counties', 'indicator_k', 'indicator_m'));
        }
    }
    public function appointment()
    {
        if (Auth::user()->access_level == 'Facility') {

            $data = [];
            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_tx_cur.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('mfl_code', Auth::user()->facility_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('mfl_code', Auth::user()->facility_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

                $client_list = ETLClient::select(
                    'etl_client_detail.upi_no',
                    'etl_client_detail.ccc_number',
                    'etl_client_detail.dob',
                    'etl_client_detail.consented',
                    'etl_client_detail.client_status',
                    'etl_client_detail.client_name',
                    'etl_client_detail.dsd_status',
                    'etl_client_detail.phone_no',
                    DB::raw('COUNT(etl_appointment_detail.app_kept) AS kept_app '),
                    DB::raw('SUM(etl_appointment_detail.app_not_kept) AS not_kept_app ')
                )
                    ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                    ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                    ->groupBy('etl_appointment_detail.client_id')
                    ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('facility')
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('mfl_code', Auth::user()->facility_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('mfl_code', Auth::user()->facility_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->whereNotNull('facility')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                'etl_client_detail.upi_no',
                'etl_client_detail.ccc_number',
                'etl_client_detail.dob',
                'etl_client_detail.consented',
                'etl_client_detail.client_status',
                'etl_appointment_detail.days_defaulted',
                'etl_appointment_detail.final_outcome',
                'etl_client_detail.client_name',
                'etl_client_detail.phone_no'
            )
                ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('etl_appointment_detail.final_outcome')
                ->groupBy('etl_appointment_detail.client_id')
                ->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('appointment_date')
                ->where('mfl_code', Auth::user()->facility_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '<=', date('Y-M'))
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $data = [];

            $all_appoinments = ETLAppointment::selectRaw('
                (SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                SUM(future) AS future,
                SUM(received_sms) AS messages,
                AVG(percent_kept) AS percent_kept,
                AVG(percent_not_kept) AS percent_not_kept,
                AVG(percent_future) AS percent_future '
            )
            ->remember($this->remember_period);

            $consented_clients = ETLClient::selectRaw(
                'SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented,
                 AVG(percent_consented) AS percent_consented '
            )
            ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::selectRaw(
                'gender,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                ROUND(AVG(percent_kept),1) AS percent_kept,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('gender')
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::selectRaw(
                'age_group,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                ROUND(AVG(percent_kept),1) AS percent_kept,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('age_group')
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::selectRaw(
                'marital,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                ROUND(AVG(percent_kept),1) AS percent_kept,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('marital')
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::selectRaw(
                'county,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                ROUND(AVG(percent_kept),1) AS percent_kept,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('county')
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::selectRaw(
                'partner,
                SUM(app_kept) AS kept_app,
                SUM(app_not_kept) AS not_kept_app,
                ROUND(AVG(percent_kept),1) AS percent_kept,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('partner')
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('partner')
                ->remember($this->remember_period);


            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
                SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
                SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
                SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
                SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
                SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
                SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
                SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
                SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
                SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
                SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
                SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
                SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
                SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
                SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
                SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
                SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
                SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
                SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
                SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
                SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
                SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
            ->groupBy('client_id')
            ->remember($this->remember_period);

            $missed_age = ETLAppointment::selectRaw(
                'age_group,
                SUM(app_not_kept) AS not_kept_app,
                SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome,
                ROUND(AVG(percent_rtc),1) AS percent_rtc,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('age_group')
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::selectRaw(
                'gender,
                SUM(app_not_kept) AS not_kept_app,
                SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome,
                ROUND(AVG(percent_rtc),1) AS percent_rtc,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('gender')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::selectRaw(
                'marital,
                SUM(app_not_kept) AS not_kept_app,
                SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome,
                ROUND(AVG(percent_rtc),1) AS percent_rtc,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('marital')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::selectRaw(
                'county,
                SUM(app_not_kept) AS not_kept_app,
                SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome,
                ROUND(AVG(percent_rtc),1) AS percent_rtc,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('county')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::selectRaw(
                'partner,
                SUM(app_not_kept) AS not_kept_app,
                SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome,
                ROUND(AVG(percent_rtc),1) AS percent_rtc,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )
                ->whereNotNull('partner')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('partner')
                ->remember($this->remember_period);

            $app_period = ETLAppointment::selectRaw(
                'DATE_FORMAT(appointment_date, "%Y-%M") AS new_date,
                ROUND(AVG(percent_rtc),1) AS percent_rtc ,
                (SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app,
                ROUND(AVG(percent_not_kept),1) AS percent_not_kept '
            )->whereNotNull('appointment_date')
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '<=', date('Y-M'))
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

            $data["all_appoinments"] = $all_appoinments->get();
            $data["consented_clients"] = $consented_clients->get();
            $data["all_tx_curr"] = $all_tx_curr->get();
            $data["appointment_gender"] = $appointment_gender->get();
            $data["appointment_age"] = $appointment_age->get();
            $data["appointment_marital"] = $appointment_marital->get();
            $data["appointment_county"] = $appointment_county->get();
            $data["appointment_partner"] = $appointment_partner->get();
            $data["client_missed"] = $client_missed->get();
            $data["missed_age"] = $missed_age->get();
            $data["missed_gender"] = $missed_gender->get();
            $data["missed_marital"] = $missed_marital->get();
            $data["missed_county"] = $missed_county->get();
            $data["missed_partner"] = $missed_partner->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Partner') {

            $data                = [];

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),

            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('partner_id', Auth::user()->partner_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->whereNotNull('facility')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('partner_id', Auth::user()->partner_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '<=', date('Y-M'))
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Sub County') {

            $data  = [];

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('facility')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);
            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->whereNotNull('facility')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '<=', date('Y-M'))
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'County') {

            $data    = [];

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('partner')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('facility')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_kept', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '

            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('partner')
                ->where('county_id', Auth::user()->county_id)
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->whereNotNull('facility')
                ->orderBy('percent_rtc', 'DESC')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('county_id', Auth::user()->county_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '<=', date('Y-M'))
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
    }

    public function filter_data(Request $request)
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $data = [];
            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_clinics = $request->clinics;
            $selected_appointments = $request->appointments;
            $selected_from = $request->from;
            $selected_to = $request->to;
            $selected_site = $request->sites;

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('county')
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('partner')
                ->groupBy('partner')
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )->groupBy('client_id')
            ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('county')
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('partner')
                ->groupBy('partner')
                ->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

            if (!empty($selected_partners)) {
                $all_appoinments = $all_appoinments->where('partner_id', $selected_partners);
                $consented_clients = $consented_clients->where('partner_id', $selected_partners);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.partner_id', $selected_partners);
                $appointment_gender = $appointment_gender->where('partner_id', $selected_partners);
                $appointment_age = $appointment_age->where('partner_id', $selected_partners);
                $appointment_marital = $appointment_marital->where('partner_id', $selected_partners);
                $appointment_county = $appointment_county->where('partner_id', $selected_partners);
                $appointment_partner = $appointment_partner->where('partner_id', $selected_partners);
                $client_missed = $client_missed->where('partner_id', $selected_partners);
                $missed_age = $missed_age->where('partner_id', $selected_partners);
                $missed_gender = $missed_gender->where('partner_id', $selected_partners);
                $missed_marital = $missed_marital->where('partner_id', $selected_partners);
                $missed_county = $missed_county->where('partner_id', $selected_partners);
                $missed_partner = $missed_partner->where('partner_id', $selected_partners);
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
                $client_missed = $client_missed->where('county_id', $selected_counties);
                $missed_age = $missed_age->where('county_id', $selected_counties);
                $missed_gender = $missed_gender->where('county_id', $selected_counties);
                $missed_marital = $missed_marital->where('county_id', $selected_counties);
                $missed_county = $missed_county->where('county_id', $selected_counties);
                $missed_partner = $missed_partner->where('county_id', $selected_counties);
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
                $client_missed = $client_missed->where('subcounty_id', $selected_subcounties);
                $missed_age = $missed_age->where('subcounty_id', $selected_subcounties);
                $missed_gender = $missed_gender->where('subcounty_id', $selected_subcounties);
                $missed_marital = $missed_marital->where('subcounty_id', $selected_subcounties);
                $missed_county = $missed_county->where('subcounty_id', $selected_subcounties);
                $missed_partner = $missed_partner->where('subcounty_id', $selected_subcounties);
                $app_period = $app_period->where('subcounty_id', $selected_subcounties);
            }
            if (!empty($selected_facilites)) {
                $all_appoinments = $all_appoinments->where('mfl_code', $selected_facilites);
                $consented_clients = $consented_clients->where('mfl_code', $selected_facilites);
                $all_tx_curr = $all_tx_curr->where('tbl_partner_facility.mfl_code', $selected_facilites)->groupBy('tbl_partner_facility.mfl_code');
                $appointment_gender = $appointment_gender->where('mfl_code', $selected_facilites);
                $appointment_age = $appointment_age->where('mfl_code', $selected_facilites);
                $appointment_marital = $appointment_marital->where('mfl_code', $selected_facilites);
                $appointment_county = $appointment_county->where('mfl_code', $selected_facilites);
                $appointment_partner = $appointment_partner->where('mfl_code', $selected_facilites);
                $client_missed = $client_missed->where('mfl_code', $selected_facilites);
                $missed_age = $missed_age->where('mfl_code', $selected_facilites);
                $missed_gender = $missed_gender->where('mfl_code', $selected_facilites);
                $missed_marital = $missed_marital->where('mfl_code', $selected_facilites);
                $missed_county = $missed_county->where('mfl_code', $selected_facilites);
                $missed_partner = $missed_partner->where('mfl_code', $selected_facilites);
                $app_period = $app_period->where('mfl_code', $selected_facilites);
            }

            if (!empty($selected_clinics)) {
                $all_appoinments = $all_appoinments->where('clinic_type', $selected_clinics);
                $consented_clients = $consented_clients->where('clinic_type', $selected_clinics);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('clinic_type', $selected_clinics);
                $appointment_age = $appointment_age->where('clinic_type', $selected_clinics);
                $appointment_marital = $appointment_marital->where('clinic_type', $selected_clinics);
                $appointment_county = $appointment_county->where('clinic_type', $selected_clinics);
                $appointment_partner = $appointment_partner->where('clinic_type', $selected_clinics);
                $client_missed = $client_missed->where('clinic_type', $selected_clinics);
                $missed_age = $missed_age->where('clinic_type', $selected_clinics);
                $missed_gender = $missed_gender->where('clinic_type', $selected_clinics);
                $missed_marital = $missed_marital->where('clinic_type', $selected_clinics);
                $missed_county = $missed_county->where('clinic_type', $selected_clinics);
                $missed_partner = $missed_partner->where('clinic_type', $selected_clinics);
                $app_period = $app_period->where('clinic_type', $selected_clinics);
            }
            if (!empty($selected_appointments)) {
                $all_appoinments = $all_appoinments;
                $consented_clients = $consented_clients;
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender;
                $appointment_age = $appointment_age;
                $appointment_marital = $appointment_marital;
                $appointment_county = $appointment_county;
                $appointment_partner = $appointment_partner;
                $client_missed = $client_missed->where('appointment_status', $selected_appointments);
                $missed_age = $missed_age->where('appointment_status', $selected_appointments);
                $missed_gender = $missed_gender->where('appointment_status', $selected_appointments);
                $missed_marital = $missed_marital->where('appointment_status', $selected_appointments);
                $missed_county = $missed_county->where('appointment_status', $selected_appointments);
                $missed_partner = $missed_partner->where('appointment_status', $selected_appointments);
                $app_period = $app_period->where('appointment_status', $selected_appointments);
            }
            if (!empty($selected_from || $selected_to)) {
                $all_appoinments = $all_appoinments->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $consented_clients = $consented_clients->where('consented_date', '>=', date($request->from))->where('consented_date', '<=', date($request->to));
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_age = $appointment_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_marital = $appointment_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_county = $appointment_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_partner = $appointment_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_missed = $client_missed->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_age = $missed_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_gender = $missed_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_marital = $missed_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_county = $missed_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_partner = $missed_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $app_period = $app_period->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
            }

            if (!empty($selected_site)) {
                $all_appoinments = $all_appoinments->where('facility_type', $selected_site);
                $consented_clients = $consented_clients->where('facility_type', $selected_site);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('facility_type', $selected_site);
                $appointment_age = $appointment_age->where('facility_type', $selected_site);
                $appointment_marital = $appointment_marital->where('facility_type', $selected_site);
                $appointment_county = $appointment_county->where('facility_type', $selected_site);
                $appointment_partner = $appointment_partner->where('facility_type', $selected_site);
                $client_missed = $client_missed->where('facility_type', $selected_site);
                $missed_age = $missed_age->where('facility_type', $selected_site);
                $missed_gender = $missed_gender->where('facility_type', $selected_site);
                $missed_marital = $missed_marital->where('facility_type', $selected_site);
                $missed_county = $missed_county->where('facility_type', $selected_site);
                $missed_partner = $missed_partner->where('facility_type', $selected_site);
                $app_period = $app_period->where('facility_type', $selected_site);
            }

            $data["all_appoinments"] = $all_appoinments->get();
            $data["consented_clients"] = $consented_clients->get();
            $data["all_tx_curr"] = $all_tx_curr->get();
            $data["appointment_gender"] = $appointment_gender->get();
            $data["appointment_age"] = $appointment_age->get();
            $data["appointment_marital"] = $appointment_marital->get();
            $data["appointment_county"] = $appointment_county->get();
            $data["appointment_partner"] = $appointment_partner->get();
            $data["client_missed"] = $client_missed->get();
            $data["missed_age"] = $missed_age->get();
            $data["missed_gender"] = $missed_gender->get();
            $data["missed_marital"] = $missed_marital->get();
            $data["missed_county"] = $missed_county->get();
            $data["missed_partner"] = $missed_partner->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Facility') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_clinics = $request->clinics;
            $selected_appointments = $request->appointments;
            $selected_from = $request->from;
            $selected_to = $request->to;
            $selected_site = $request->site;
            $data                = [];
            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_tx_cur.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

                $client_list = ETLClient::select(
                    'etl_client_detail.upi_no',
                    'etl_client_detail.ccc_number',
                    'etl_client_detail.dob',
                    'etl_client_detail.consented',
                    'etl_client_detail.client_status',
                    'etl_client_detail.client_name',
                    'etl_client_detail.dsd_status',
                    'etl_client_detail.phone_no',
                    DB::raw('COUNT(etl_appointment_detail.app_kept) AS kept_app '),
                    DB::raw('SUM(etl_appointment_detail.app_not_kept) AS not_kept_app ')
                )
                    ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                    ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                    ->groupBy('etl_appointment_detail.client_id')
                    ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('facility')
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                'etl_client_detail.upi_no',
                'etl_client_detail.ccc_number',
                'etl_client_detail.dob',
                'etl_client_detail.consented',
                'etl_client_detail.client_status',
                'etl_appointment_detail.days_defaulted',
                'etl_appointment_detail.final_outcome',
                'etl_client_detail.client_name',
                'etl_client_detail.phone_no'
            )
                ->join('etl_appointment_detail', 'etl_client_detail.client_id', '=', 'etl_appointment_detail.client_id')
                ->where('etl_client_detail.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('etl_appointment_detail.final_outcome')
                ->groupBy('etl_appointment_detail.client_id')
                ->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('mfl_code', Auth::user()->facility_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

                // if ($selected_appointments == 'Missed') {
                //     $app_period = DB::table('etl_appointment_detail')->select(
                //         DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                //         DB::raw('ROUND((( COUNT(CASE WHEN appointment_status = "Missed" THEN app_not_kept END)/(COUNT(appointment_id)) ) * 100 ), 1 ) AS percent_not_kept'),
                //         DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                //         // DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
                //     )->whereNotNull('appointment_date')
                //         ->where('mfl_code', Auth::user()->facility_id)
                //         ->where('appointment_date', '<=', date("Y-M-D"))
                //         ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                //         ->orderBy('appointment_date', 'ASC')
                //         ->groupBy('new_date');
                // }

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

            if (!empty($selected_clinics)) {
                $all_appoinments = $all_appoinments->where('clinic_type', $selected_clinics);
                $consented_clients = $consented_clients->where('clinic_type', $selected_clinics);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('clinic_type', $selected_clinics);
                $appointment_age = $appointment_age->where('clinic_type', $selected_clinics);
                $appointment_marital = $appointment_marital->where('clinic_type', $selected_clinics);
                $appointment_county = $appointment_county->where('clinic_type', $selected_clinics);
                $appointment_partner = $appointment_partner->where('clinic_type', $selected_clinics);
                $appointment_facility = $appointment_facility->where('clinic_type', $selected_clinics);
                $client_list = $client_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $client_missed = $client_missed->where('clinic_type', $selected_clinics);
                $missed_age = $missed_age->where('clinic_type', $selected_clinics);
                $missed_gender = $missed_gender->where('clinic_type', $selected_clinics);
                $missed_marital = $missed_marital->where('clinic_type', $selected_clinics);
                $missed_county = $missed_county->where('clinic_type', $selected_clinics);
                $missed_partner = $missed_partner->where('clinic_type', $selected_clinics);
                $client_app_list = $client_app_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $app_period = $app_period->where('clinic_type', $selected_clinics);
            }
            if (!empty($selected_appointments)) {
                $all_appoinments = $all_appoinments;
                $consented_clients = $consented_clients;
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender;
                $appointment_age = $appointment_age;
                $appointment_marital = $appointment_marital;
                $appointment_county = $appointment_county;
                $appointment_partner = $appointment_partner;
                $appointment_facility = $appointment_facility;
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_status', $selected_appointments);
                $missed_age = $missed_age->where('appointment_status', $selected_appointments);
                $missed_gender = $missed_gender->where('appointment_status', $selected_appointments);
                $missed_marital = $missed_marital->where('appointment_status', $selected_appointments);
                $missed_county = $missed_county->where('appointment_status', $selected_appointments);
                $missed_partner = $missed_partner->where('appointment_status', $selected_appointments);
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_status', $selected_appointments);
            }
            if (!empty($selected_from || $selected_to)) {
                $all_appoinments = $all_appoinments->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $consented_clients = $consented_clients->where('consented_date', '>=', date($request->from))->where('consented_date', '<=', date($request->to));
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_age = $appointment_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_marital = $appointment_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_county = $appointment_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_partner = $appointment_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_facility = $appointment_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_age = $missed_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_gender = $missed_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_marital = $missed_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_county = $missed_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_partner = $missed_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
            }

            if (!empty($selected_sites)) {
                $all_appoinments = $all_appoinments->where('facility_type', $selected_sites);
                $consented_clients = $consented_clients->where('facility_type', $selected_sites);
                $all_tx_curr = $all_tx_curr->join('etl_client_detail', 'tbl_tx_cur.mfl_code', '=', 'etl_client_detail.mfl_code')->where('etl_client_detail.facility_type', '=', 'EMR Based')->groupBy('etl_client_detail.mfl_code');
                $appointment_gender = $appointment_gender->where('facility_type', $selected_sites);
                $appointment_age = $appointment_age->where('facility_type', $selected_sites);
                $appointment_marital = $appointment_marital->where('facility_type', $selected_sites);
                $appointment_county = $appointment_county->where('facility_type', $selected_sites);
                $appointment_partner = $appointment_partner->where('facility_type', $selected_sites);
                $appointment_facility = $appointment_facility->where('facility_type', $selected_sites);
                $client_list = $client_list->where('facility_type', $selected_sites);
                $client_missed = $client_missed->where('facility_type', $selected_sites);
                $missed_age = $missed_age->where('facility_type', $selected_sites);
                $missed_gender = $missed_gender->where('facility_type', $selected_sites);
                $missed_marital = $missed_marital->where('facility_type', $selected_sites);
                $missed_county = $missed_county->where('facility_type', $selected_sites);
                $missed_partner = $missed_partner->where('facility_type', $selected_sites);
                $client_app_list = $client_app_list->where('facility_type', $selected_sites);
                $app_period = $app_period->where('facility_type', $selected_sites);
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
        if (Auth::user()->access_level == 'Partner') {

            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_clinics = $request->clinics;
            $selected_appointments = $request->appointments;
            $selected_from = $request->from;
            $selected_to = $request->to;
            $selected_site = $request->sites;
            $data                = [];

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),

            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->whereNotNull('facility')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('partner_id', Auth::user()->partner_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
                $missed_facility = $missed_facility->where('partner_id', $selected_partners);
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
                $missed_facility = $missed_facility->where('county_id', $selected_counties);
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
                $missed_facility = $missed_facility->where('subcounty_id', $selected_subcounties);
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
                $missed_facility = $missed_facility->where('mfl_code', $selected_facilites);
                $client_app_list = $client_app_list->where('mfl_code', $selected_facilites);
                $app_period = $app_period->where('mfl_code', $selected_facilites);
            }

            if (!empty($selected_clinics)) {
                $all_appoinments = $all_appoinments->where('clinic_type', $selected_clinics);
                $consented_clients = $consented_clients->where('clinic_type', $selected_clinics);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('clinic_type', $selected_clinics);
                $appointment_age = $appointment_age->where('clinic_type', $selected_clinics);
                $appointment_marital = $appointment_marital->where('clinic_type', $selected_clinics);
                $appointment_county = $appointment_county->where('clinic_type', $selected_clinics);
                $appointment_partner = $appointment_partner->where('clinic_type', $selected_clinics);
                $appointment_facility = $appointment_facility->where('clinic_type', $selected_clinics);
                $client_list = $client_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $client_missed = $client_missed->where('clinic_type', $selected_clinics);
                $missed_age = $missed_age->where('clinic_type', $selected_clinics);
                $missed_gender = $missed_gender->where('clinic_type', $selected_clinics);
                $missed_marital = $missed_marital->where('clinic_type', $selected_clinics);
                $missed_county = $missed_county->where('clinic_type', $selected_clinics);
                $missed_partner = $missed_partner->where('clinic_type', $selected_clinics);
                $missed_facility = $missed_facility->where('clinic_type', $selected_clinics);
                $client_app_list = $client_app_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $app_period = $app_period->where('clinic_type', $selected_clinics);
            }
            if (!empty($selected_appointments)) {
                $all_appoinments = $all_appoinments;
                $consented_clients = $consented_clients;
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender;
                $appointment_age = $appointment_age;
                $appointment_marital = $appointment_marital;
                $appointment_county = $appointment_county;
                $appointment_partner = $appointment_partner;
                $appointment_facility = $appointment_facility;
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_status', $selected_appointments);
                $missed_age = $missed_age->where('appointment_status', $selected_appointments);
                $missed_gender = $missed_gender->where('appointment_status', $selected_appointments);
                $missed_marital = $missed_marital->where('appointment_status', $selected_appointments);
                $missed_county = $missed_county->where('appointment_status', $selected_appointments);
                $missed_partner = $missed_partner->where('appointment_status', $selected_appointments);
                $missed_facility = $missed_facility->where('appointment_status', $selected_appointments);
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_status', $selected_appointments);
            }
            if (!empty($selected_from || $selected_to)) {
                $all_appoinments = $all_appoinments->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $consented_clients = $consented_clients->where('consented_date', '>=', date($request->from))->where('consented_date', '<=', date($request->to));
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_age = $appointment_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_marital = $appointment_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_county = $appointment_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_partner = $appointment_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_facility = $appointment_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_age = $missed_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_gender = $missed_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_marital = $missed_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_county = $missed_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_partner = $missed_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_facility = $missed_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
            }

            if (!empty($selected_site)) {
                $all_appoinments = $all_appoinments->where('facility_type', $selected_site);
                $consented_clients = $consented_clients->where('facility_type', $selected_site);
                $all_tx_curr = $all_tx_curr->join('etl_client_detail', 'tbl_tx_cur.mfl_code', '=', 'etl_client_detail.mfl_code')->where('etl_client_detail.facility_type', $selected_site)->groupBy('etl_client_detail.mfl_code');
                $appointment_gender = $appointment_gender->where('facility_type', $selected_site);
                $appointment_age = $appointment_age->where('facility_type', $selected_site);
                $appointment_marital = $appointment_marital->where('facility_type', $selected_site);
                $appointment_county = $appointment_county->where('facility_type', $selected_site);
                $appointment_partner = $appointment_partner->where('facility_type', $selected_site);
                $appointment_facility = $appointment_facility->where('facility_type', $selected_site);
                $client_list = $client_list->where('facility_type', $selected_site);
                $client_missed = $client_missed->where('facility_type', $selected_site);
                $missed_age = $missed_age->where('facility_type', $selected_site);
                $missed_gender = $missed_gender->where('facility_type', $selected_site);
                $missed_marital = $missed_marital->where('facility_type', $selected_site);
                $missed_county = $missed_county->where('facility_type', $selected_site);
                $missed_partner = $missed_partner->where('facility_type', $selected_site);
                $missed_facility = $missed_facility->where('facility_type', $selected_site);
                $client_app_list = $client_app_list->where('facility_type', $selected_site);
                $app_period = $app_period->where('facility_type', $selected_site);
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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'Sub County') {

            $data  = [];
            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_clinics = $request->clinics;
            $selected_appointments = $request->appointments;
            $selected_from = $request->from;
            $selected_to = $request->to;
            $selected_sites = $request->sites;

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('AVG(percent_rtc) AS percent_rtc '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->whereNotNull('facility')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
                $missed_facility = $missed_facility->where('partner_id', $selected_partners);
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
                $missed_facility = $missed_facility->where('county_id', $selected_counties);
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
                $missed_facility = $missed_facility->where('subcounty_id', $selected_subcounties);
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
                $missed_facility = $missed_facility->where('mfl_code', $selected_facilites);
                $client_app_list = $client_app_list->where('mfl_code', $selected_facilites);
                $app_period = $app_period->where('mfl_code', $selected_facilites);
            }

            if (!empty($selected_clinics)) {
                $all_appoinments = $all_appoinments->where('clinic_type', $selected_clinics);
                $consented_clients = $consented_clients->where('clinic_type', $selected_clinics);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('clinic_type', $selected_clinics);
                $appointment_age = $appointment_age->where('clinic_type', $selected_clinics);
                $appointment_marital = $appointment_marital->where('clinic_type', $selected_clinics);
                $appointment_county = $appointment_county->where('clinic_type', $selected_clinics);
                $appointment_partner = $appointment_partner->where('clinic_type', $selected_clinics);
                $appointment_facility = $appointment_facility->where('clinic_type', $selected_clinics);
                $client_list = $client_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $client_missed = $client_missed->where('clinic_type', $selected_clinics);
                $missed_age = $missed_age->where('clinic_type', $selected_clinics);
                $missed_gender = $missed_gender->where('clinic_type', $selected_clinics);
                $missed_marital = $missed_marital->where('clinic_type', $selected_clinics);
                $missed_county = $missed_county->where('clinic_type', $selected_clinics);
                $missed_partner = $missed_partner->where('clinic_type', $selected_clinics);
                $missed_facility = $missed_facility->where('clinic_type', $selected_clinics);
                $client_app_list = $client_app_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $app_period = $app_period->where('clinic_type', $selected_clinics);
            }
            if (!empty($selected_appointments)) {
                $all_appoinments = $all_appoinments;
                $consented_clients = $consented_clients;
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender;
                $appointment_age = $appointment_age;
                $appointment_marital = $appointment_marital;
                $appointment_county = $appointment_county;
                $appointment_partner = $appointment_partner;
                $appointment_facility = $appointment_facility;
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_status', $selected_appointments);
                $missed_age = $missed_age->where('appointment_status', $selected_appointments);
                $missed_gender = $missed_gender->where('appointment_status', $selected_appointments);
                $missed_marital = $missed_marital->where('appointment_status', $selected_appointments);
                $missed_county = $missed_county->where('appointment_status', $selected_appointments);
                $missed_partner = $missed_partner->where('appointment_status', $selected_appointments);
                $missed_facility = $missed_facility->where('appointment_status', $selected_appointments);
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_status', $selected_appointments);
            }
            if (!empty($selected_from || $selected_to)) {
                $all_appoinments = $all_appoinments->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $consented_clients = $consented_clients->where('consented_date', '>=', date($request->from))->where('consented_date', '<=', date($request->to));
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_age = $appointment_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_marital = $appointment_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_county = $appointment_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_partner = $appointment_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_facility = $appointment_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_age = $missed_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_gender = $missed_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_marital = $missed_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_county = $missed_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_partner = $missed_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_facility = $missed_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
            }

            if (!empty($selected_sites)) {
                $all_appoinments = $all_appoinments->where('facility_type', $selected_sites);
                $consented_clients = $consented_clients->where('facility_type', $selected_sites);
                $all_tx_curr = $all_tx_curr->join('etl_client_detail', 'tbl_tx_cur.mfl_code', '=', 'etl_client_detail.mfl_code')->where('etl_client_detail.facility_type', $selected_sites)->groupBy('etl_client_detail.mfl_code');
                $appointment_gender = $appointment_gender->where('facility_type', $selected_sites);
                $appointment_age = $appointment_age->where('facility_type', $selected_sites);
                $appointment_marital = $appointment_marital->where('facility_type', $selected_sites);
                $appointment_county = $appointment_county->where('facility_type', $selected_sites);
                $appointment_partner = $appointment_partner->where('facility_type', $selected_sites);
                $appointment_facility = $appointment_facility->where('facility_type', $selected_sites);
                $client_list = $client_list->where('facility_type', $selected_sites);
                $client_missed = $client_missed->where('facility_type', $selected_sites);
                $missed_age = $missed_age->where('facility_type', $selected_sites);
                $missed_gender = $missed_gender->where('facility_type', $selected_sites);
                $missed_marital = $missed_marital->where('facility_type', $selected_sites);
                $missed_county = $missed_county->where('facility_type', $selected_sites);
                $missed_partner = $missed_partner->where('facility_type', $selected_sites);
                $missed_facility = $missed_facility->where('facility_type', $selected_sites);
                $client_app_list = $client_app_list->where('facility_type', $selected_sites);
                $app_period = $app_period->where('facility_type', $selected_sites);
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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
        if (Auth::user()->access_level == 'County') {

            $data    = [];
            $selected_partners = $request->partners;
            $selected_counties = $request->counties;
            $selected_subcounties = $request->subcounties;
            $selected_facilites = $request->facilities;
            $selected_clinics = $request->clinics;
            $selected_appointments = $request->appointments;
            $selected_from = $request->from;
            $selected_to = $request->to;
            $selected_sites = $request->sites;

            $all_appoinments = ETLAppointment::select(
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(future) AS future '),
                DB::raw('SUM(received_sms) AS messages '),
                DB::raw('AVG(percent_kept) AS percent_kept '),
                DB::raw('AVG(percent_not_kept) AS percent_not_kept '),
                DB::raw('AVG(percent_future) AS percent_future ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $consented_clients = ETLClient::select(
                DB::raw('SUM(CASE WHEN consented = "Yes" THEN 1 ELSE 0 END) AS consented '),
                DB::raw('AVG(percent_consented) AS percent_consented ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $all_tx_curr = Txcurr::select(DB::raw('SUM(tbl_tx_cur.tx_cur) as tx_cur'))
                ->join('tbl_partner_facility', 'tbl_tx_cur.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $appointment_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $appointment_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $appointment_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $appointment_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('partner')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $appointment_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_kept) AS kept_app '),
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('ROUND(AVG(percent_kept),1) AS percent_kept '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('facility')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // missed appointment
            $client_missed = ETLAppointment::selectRaw('
            SUM(CASE WHEN app_not_kept = 1 THEN 1 ELSE 0 END) AS not_kept_app,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN received_sms END) AS messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_messages,
            SUM(CASE WHEN received_sms = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_messages,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN called END) AS called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_called,
            SUM(CASE WHEN called = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_called,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN physically_traced END) AS physically_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_traced,
            SUM(CASE WHEN physically_traced = 1 AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_traced,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN rtc_no END) AS final_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_outcome,
            SUM(CASE WHEN final_outcome = "Client returned to care" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_outcome,
            SUM(CASE WHEN appointment_status = "Missed" OR appointment_status = "Defaulted" OR appointment_status = "IIT" THEN consent_no END) AS consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Missed" THEN 1 ELSE 0 END) AS missed_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "Defaulted" THEN 1 ELSE 0 END) AS defaulted_consent,
            SUM(CASE WHEN consent = "Yes" AND appointment_status = "IIT" THEN 1 ELSE 0 END) AS iit_consent '

            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('client_id')
                ->remember($this->remember_period);

            $missed_age = ETLAppointment::select(
                'age_group',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('age_group')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('age_group')
                ->remember($this->remember_period);

            $missed_gender = ETLAppointment::select(
                'gender',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('gender')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('gender')
                ->remember($this->remember_period);

            $missed_marital = ETLAppointment::select(
                'marital',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->whereNotNull('marital')
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('marital')
                ->remember($this->remember_period);

            $missed_county = ETLAppointment::select(
                'county',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('county')
                ->remember($this->remember_period);

            $missed_partner = ETLAppointment::select(
                'partner',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('county_id', Auth::user()->county_id)
                ->groupBy('partner')
                ->remember($this->remember_period);

            $missed_facility = ETLAppointment::select(
                'facility',
                DB::raw('SUM(app_not_kept) AS not_kept_app '),
                DB::raw('SUM(CASE WHEN final_outcome = "Client returned to care" THEN 1 ELSE 0 END) AS final_outcome'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )
                ->where('subcounty_id', Auth::user()->subcounty_id)
                ->whereNotNull('facility')
                ->groupBy('facility')
                ->remember($this->remember_period);

            $client_app_list = ETLClient::select(
                DB::raw('COUNT(ccc_number) AS ccc_number ')
            )->remember($this->remember_period);

            $app_period = ETLAppointment::select(
                DB::raw('DATE_FORMAT(appointment_date, "%Y-%M") AS new_date'),
                DB::raw('ROUND(AVG(percent_rtc),1) AS percent_rtc '),
                DB::raw('(SUM(app_kept)+SUM(app_not_kept)+SUM(future)) as total_app'),
                DB::raw('ROUND(AVG(percent_not_kept),1) AS percent_not_kept ')
            )->whereNotNull('appointment_date')
                ->where('county_id', Auth::user()->county_id)
                ->where('appointment_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where(DB::raw('DATE_FORMAT(appointment_date, "%Y-%M")'), '>=', "2017-January")
                ->orderBy('appointment_date', 'ASC')
                ->groupBy('new_date')
                ->remember($this->remember_period);

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
                $missed_facility = $missed_facility->where('partner_id', $selected_partners);
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
                $missed_facility = $missed_facility->where('county_id', $selected_counties);
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
                $missed_facility = $missed_facility->where('subcounty_id', $selected_subcounties);
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
                $missed_facility = $missed_facility->where('mfl_code', $selected_facilites);
                $client_app_list = $client_app_list->where('mfl_code', $selected_facilites);
                $app_period = $app_period->where('mfl_code', $selected_facilites);
            }

            if (!empty($selected_clinics)) {
                $all_appoinments = $all_appoinments->where('clinic_type', $selected_clinics);
                $consented_clients = $consented_clients->where('clinic_type', $selected_clinics);
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('clinic_type', $selected_clinics);
                $appointment_age = $appointment_age->where('clinic_type', $selected_clinics);
                $appointment_marital = $appointment_marital->where('clinic_type', $selected_clinics);
                $appointment_county = $appointment_county->where('clinic_type', $selected_clinics);
                $appointment_partner = $appointment_partner->where('clinic_type', $selected_clinics);
                $appointment_facility = $appointment_facility->where('clinic_type', $selected_clinics);
                $client_list = $client_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $client_missed = $client_missed->where('clinic_type', $selected_clinics);
                $missed_age = $missed_age->where('clinic_type', $selected_clinics);
                $missed_gender = $missed_gender->where('clinic_type', $selected_clinics);
                $missed_marital = $missed_marital->where('clinic_type', $selected_clinics);
                $missed_county = $missed_county->where('clinic_type', $selected_clinics);
                $missed_partner = $missed_partner->where('clinic_type', $selected_clinics);
                $missed_facility = $missed_facility->where('clinic_type', $selected_clinics);
                $client_app_list = $client_app_list->where('etl_client_detail.clinic_type', $selected_clinics);
                $app_period = $app_period->where('clinic_type', $selected_clinics);
            }
            if (!empty($selected_appointments)) {
                $all_appoinments = $all_appoinments;
                $consented_clients = $consented_clients;
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender;
                $appointment_age = $appointment_age;
                $appointment_marital = $appointment_marital;
                $appointment_county = $appointment_county;
                $appointment_partner = $appointment_partner;
                $appointment_facility = $appointment_facility;
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_status', $selected_appointments);
                $missed_age = $missed_age->where('appointment_status', $selected_appointments);
                $missed_gender = $missed_gender->where('appointment_status', $selected_appointments);
                $missed_marital = $missed_marital->where('appointment_status', $selected_appointments);
                $missed_county = $missed_county->where('appointment_status', $selected_appointments);
                $missed_partner = $missed_partner->where('appointment_status', $selected_appointments);
                $missed_facility = $missed_facility->where('appointment_status', $selected_appointments);
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_status', $selected_appointments);
            }
            if (!empty($selected_from || $selected_to)) {
                $all_appoinments = $all_appoinments->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $consented_clients = $consented_clients->where('consented_date', '>=', date($request->from))->where('consented_date', '<=', date($request->to));
                $all_tx_curr = $all_tx_curr;
                $appointment_gender = $appointment_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_age = $appointment_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_marital = $appointment_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_county = $appointment_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_partner = $appointment_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $appointment_facility = $appointment_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_list = $client_list;
                $client_missed = $client_missed->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_age = $missed_age->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_gender = $missed_gender->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_marital = $missed_marital->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_county = $missed_county->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_partner = $missed_partner->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $missed_facility = $missed_facility->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
                $client_app_list = $client_app_list;
                $app_period = $app_period->where('appointment_date', '>=', date($request->from))->where('appointment_date', '<=', date($request->to));
            }

            if (!empty($selected_sites)) {
                $all_appoinments = $all_appoinments->where('facility_type', $selected_sites);
                $consented_clients = $consented_clients->where('facility_type', $selected_sites);
                $all_tx_curr = $all_tx_curr->join('etl_client_detail', 'tbl_tx_cur.mfl_code', '=', 'etl_client_detail.mfl_code')->where('etl_client_detail.facility_type', $selected_sites)->groupBy('etl_client_detail.mfl_code');
                $appointment_gender = $appointment_gender->where('facility_type', $selected_sites);
                $appointment_age = $appointment_age->where('facility_type', $selected_sites);
                $appointment_marital = $appointment_marital->where('facility_type', $selected_sites);
                $appointment_county = $appointment_county->where('facility_type', $selected_sites);
                $appointment_partner = $appointment_partner->where('facility_type', $selected_sites);
                $appointment_facility = $appointment_facility->where('facility_type', $selected_sites);
                $client_list = $client_list->where('facility_type', $selected_sites);
                $client_missed = $client_missed->where('facility_type', $selected_sites);
                $missed_age = $missed_age->where('facility_type', $selected_sites);
                $missed_gender = $missed_gender->where('facility_type', $selected_sites);
                $missed_marital = $missed_marital->where('facility_type', $selected_sites);
                $missed_county = $missed_county->where('facility_type', $selected_sites);
                $missed_partner = $missed_partner->where('facility_type', $selected_sites);
                $missed_facility = $missed_facility->where('facility_type', $selected_sites);
                $client_app_list = $client_app_list->where('facility_type', $selected_sites);
                $app_period = $app_period->where('facility_type', $selected_sites);
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
            $data["missed_facility"] = $missed_facility->get();
            $data["client_app_list"] = $client_app_list->get();
            $data["app_period"] = $app_period->get();

            return $data;
        }
    }
    public function get_county_facilities(Request $request, $id)
    {
        $partner_ids = array();
        $strings_array = $request->partners;
        if (!empty($strings_array)) {
            foreach ($strings_array as $each_id) {
                $partner_ids[] = (int) $each_id;
            }
        }
        $partners_with_ids = PartnerFacility::select('partner_id')->distinct('partner_id')->groupBy('partner_id')->remember($this->remember_period)->get();

        $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->where("tbl_partner_facility.county_id", $id)
            ->orderBy('tbl_master_facility.name', 'ASC')
            ->remember($this->remember_period)
            ->pluck("tbl_master_facility.name", "tbl_master_facility.code");

        return json_encode($facilities);
    }
    public function get_partner_sub_counties($id)
    {
        $subcounties = PartnerFacility::join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
            ->where("tbl_partner_facility.partner_id", $id)
            ->orderBy('tbl_sub_county.name', 'ASC')
            ->remember($this->remember_period)
            ->pluck("tbl_sub_county.name", "tbl_sub_county.id");

        if (Auth::user()->access_level == 'Partner') {
            $subcounties = PartnerFacility::join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->where("tbl_partner_facility.partner_id", $id)
                ->where("tbl_partner_facility.partner_id", '=', Auth::user()->partner_id)
                ->orderBy('tbl_sub_county.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_sub_county.name", "tbl_sub_county.id");
        }
        if (Auth::user()->access_level == 'Sub County') {
            $subcounties = PartnerFacility::join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->where("tbl_partner_facility.partner_id", $id)
                ->where("tbl_partner_facility.sub_county_id", '=', Auth::user()->subcounty_id)
                ->orderBy('tbl_sub_county.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_sub_county.name", "tbl_sub_county.id");
        }
        return json_encode($subcounties);
    }
    public function get_partner_facilities(Request $request, $id)
    {
        $partner_ids = array();
        $strings_array = $request->partners;
        if (!empty($strings_array)) {
            foreach ($strings_array as $each_id) {
                $partner_ids[] = (int) $each_id;
            }
        }
        $partners_with_ids = PartnerFacility::select('partner_id')->distinct('partner_id')->groupBy('partner_id')->remember($this->remember_period)->get();

        $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->where("tbl_partner_facility.partner_id", $id)
            // ->where("tbl_partner_facility.partner_id", $id)
            ->orderBy('tbl_master_facility.name', 'ASC')
            ->remember($this->remember_period)
            ->pluck("tbl_master_facility.name", "tbl_master_facility.code");

        if (Auth::user()->access_level == 'Partner') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_partner_facility.partner_id", $id)
                ->where("tbl_partner_facility.partner_id", '=', Auth::user()->partner_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }
        if (Auth::user()->access_level == 'County') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_partner_facility.partner_id", $id)
                ->where("tbl_partner_facility.county_id", '=', Auth::user()->county_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }
        if (Auth::user()->access_level == 'Sub County') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_partner_facility.partner_id", $id)
                ->whereIn('tbl_partner_facility.partner_id', $partners_with_ids)
                ->where("tbl_partner_facility.sub_county_id", '=', Auth::user()->subcounty_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }

        return json_encode($facilities);
    }
    public function get_sitetype_facilities(Request $request, $id)
    {
        $partner_ids = array();
        if (!empty($id == 'EMR Based' || $id == 'Paper Based')) {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_master_facility.site_type", $id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }

        if (Auth::user()->access_level == 'Partner') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_master_facility.site_type", $id)
                ->where("tbl_partner_facility.partner_id", '=', Auth::user()->partner_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }
        if (Auth::user()->access_level == 'County') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_master_facility.site_type", $id)
                ->where("tbl_partner_facility.county_id", '=', Auth::user()->county_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }
        if (Auth::user()->access_level == 'Sub County') {
            $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->where("tbl_master_facility.site_type", $id)
                ->where("tbl_partner_facility.sub_county_id", '=', Auth::user()->subcounty_id)
                ->orderBy('tbl_master_facility.name', 'ASC')
                ->remember($this->remember_period)
                ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
        }

        return json_encode($facilities);
    }
}
