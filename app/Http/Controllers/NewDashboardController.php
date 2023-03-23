<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;


use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\PartnerFacility;
use App\Models\ActiveFacilities;
use App\Models\Dcm;
use App\Models\Indicator;
use Auth;
use Carbon\Carbon;
use DB;

class NewDashboardController extends Controller
{
    protected  $remember_period ;

    public function __construct()
    {
        $this->remember_period  = env('REMEMBER_PERIOD', '60 * 60');
    }

    public function dashboard()
    {

        // showing all the active clients, all appointments, missed appointments
        if (Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')
                            ->remember($this->remember_period)
                            ->pluck('name', 'id');

            $client = Client::where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('clinic_number');

            $client_ever_enrolled = Client::whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('clinic_number');

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period)
                ->get();
            $facilities_ever_enrolled = PartnerFacility::remember($this->remember_period)->count('mfl_code');


            //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::select('id')->where([['gender', '=', '2'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $unknown_gender = Client::where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $client_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end)) AS count"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');


            $client_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`dob`)) <= 14)) then `dob` end)) AS count"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end)) AS count"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');


            $client_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `id` end)) AS count"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');


            $client_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `id` end)) AS count"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_unknown_age = Client::where(\DB::raw("CASE
            WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
            WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
            WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
            WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
            date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->remember($this->remember_period)->pluck('name', 'id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period)
                ->get();

            $facilities_ever_enrolled = PartnerFacility::where('partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('mfl_code');

            //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('id')->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.county_id', Auth::user()->county_id)->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period)
                ->get();

            $facilities_ever_enrolled = PartnerFacility::where('county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('mfl_code');

            //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('id')->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
            ->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period)
                ->get();

            $facilities_ever_enrolled = PartnerFacility::where('sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('mfl_code');

            //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('id')->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                    WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                    date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                    WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')
                            ->orderBy('name', 'ASC')
                            ->remember("$this->remember_period")
                            ->pluck('name', 'id');

            $client = Client::where('status', '=', 'Active')
                        ->whereNull('hei_no')
                        ->remember("$this->remember_period")
                        ->count('id');

            $client_ever_enrolled = Client::whereNull('hei_no')
                                    ->remember("$this->remember_period")
                                    ->count('id');

            // $missed_appointment = Appointments::select('id')->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])->count();
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period)
                ->get();
            $facilities_ever_enrolled = PartnerFacility::remember($this->remember_period)->count('mfl_code');

            $clients_male = Client::where([['gender', '=', '2'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->count('id');

            $clients_female = Client::where([['gender', '=', '1'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->count('id');

            $unknown_gender = Client::where([['gender', '!=', '1'], ['gender', '!=', '2'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->count('id');

            $client_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
			WHEN ( locate( '/', `dob` ) > 0 ) THEN
			date_format( str_to_date( `dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
			WHEN ( locate( '-', `dob` ) > 0 ) THEN
		    date_format( str_to_date( `dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
			WHEN ( locate( '/', `dob` ) > 0 ) THEN
			date_format( str_to_date( `dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
			WHEN ( locate( '-', `dob` ) > 0 ) THEN
		    date_format( str_to_date( `dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`dob`)) <= 14)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
			WHEN ( locate( '/', `dob` ) > 0 ) THEN
			date_format( str_to_date( `dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
			WHEN ( locate( '-', `dob` ) > 0 ) THEN
		    date_format( str_to_date( `dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(CASE
			WHEN ( locate( '/', `dob` ) > 0 ) THEN
			date_format( str_to_date( `dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
			WHEN ( locate( '-', `dob` ) > 0 ) THEN
		    date_format( str_to_date( `dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END )) <= 24)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(CASE
			WHEN ( locate( '/', `dob` ) > 0 ) THEN
			date_format( str_to_date( `dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
			WHEN ( locate( '-', `dob` ) > 0 ) THEN
		    date_format( str_to_date( `dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_unknown_age = Client::select('id')
                ->where('dob', '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->count('id');
        }
        // dd($active_facilities);

        return view('new_dashboard.main_dashbaord', compact(
            'all_partners',
            'active_facilities',
            'facilities_ever_enrolled',
            'client',
            'client_ever_enrolled',
            'clients_male',
            'clients_female',
            'unknown_gender',
            'client_to_nine',
            'client_to_fourteen',
            'client_to_nineteen',
            'client_to_twentyfour',
            'client_to_twentyfive_above',
            'client_unknown_age'

        ));
    }

    public function client_dashboard()
    {

        // showing all the active clients, all appointments, missed appointments
        if (Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')->remember($this->remember_period)->pluck('name', 'id');

            $client = Client::where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('clinic_number');

            // client charts
            $client_consented =  Client::select('smsenable')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('smsenable', '=', 'Yes')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented =  Client::select('smsenable')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('smsenable', '!=', 'Yes')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by gender

            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '2')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '1')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_uknown_gender = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '!=', '1')
                    ->where('gender', '!=', '2')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by gender
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '2')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '1')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_uknown_gender = Client::where('smsenable', '!=', 'Yes')
                    ->where('gender', '!=', '1')
                    ->where('gender', '!=', '2')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by age distribution
            $client_consented_to_nine =  Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_uknown_age = Client::select('smsenable')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('smsenable', '=', 'Yes')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_uknown_age = Client::select('smsenable')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('smsenable', '!=', 'Yes')
                    ->where('mfl_code', Auth::user()->facility_id)
                    ->remember($this->remember_period)
                    ->count();

        }
        if (Auth::user()->access_level == 'Partner') {

            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->remember($this->remember_period)->pluck('name', 'id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            // client charts
            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_clientsmsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                    ->remember($this->remember_period)
                    ->count();

        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.county_id', Auth::user()->county_id)->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            // client charts
            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_clientsmsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                    ->remember($this->remember_period)
                    ->count();

        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_client.clinic_number');

            // client charts
            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_clientsmsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '2')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.gender', '=', '1')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_client.smsenable', '=', 'Yes')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->pluck('count');

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select('tbl_client.smsenable')
                    ->where('tbl_client.status', '=', 'Active')
                    ->whereNull('tbl_client.hei_no')
                    ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                    ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                    ->where('tbl_client.smsenable', '!=', 'Yes')
                    ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                    ->remember($this->remember_period)
                    ->count();

        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->remember($this->remember_period)->pluck('name', 'id');

            $client = Client::where('status', '=', 'Active')->whereNull('hei_no')->remember($this->remember_period)->count('clinic_number');

            // client charts
            $client_consented = Client::select('smsenable')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('smsenable', '=', 'Yes')
                    ->remember($this->remember_period)
                    ->count();

            $client_nonconsented = Client::select('smsenable')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('smsenable', '!=', 'Yes')
                    ->remember($this->remember_period)
                    ->count();

            // consented clients by gender

            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '2')
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '=', '1')
                    ->remember($this->remember_period)
                    ->count();

            $client_consented_uknown_gender = Client::where('smsenable', '=', 'Yes')
                    ->where('status', '=', 'Active')
                    ->whereNull('hei_no')
                    ->where('gender', '!=', '1')
                    ->where('gender', '!=', '2')
                    ->remember($this->remember_period)
                    ->count();

            // non consented clients by gender
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '2')
                ->remember($this->remember_period)
                ->count();

            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '1')
                ->remember($this->remember_period)
                ->count();

            $client_nonconsented_uknown_gender = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->remember($this->remember_period)
                ->count();

            // consented clients by age distribution
            $client_consented_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_consented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_consented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_consented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_consented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_consented_uknown_age = Client::select('smsenable')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('smsenable', '=', 'Yes')
                ->remember($this->remember_period)
                ->count();

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_nonconsented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_nonconsented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->remember($this->remember_period)
                ->pluck('count');

            $client_nonconsented_uknown_age = Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('smsenable', '!=', 'Yes')
                ->remember($this->remember_period)
                ->count();
        }


        // dd($active_facilities);

        return view('new_dashboard.client_dashboard', compact(
            'all_partners',
            'client',
            'client_consented',
            'client_nonconsented',
            'client_consented_male',
            'client_consented_female',
            'client_consented_uknown_gender',
            'client_nonconsented_male',
            'client_nonconsented_female',
            'client_nonconsented_uknown_gender',
            'client_consented_to_nine',
            'client_consented_to_fourteen',
            'client_consented_to_nineteen',
            'client_consented_to_twentyfour',
            'client_consented_to_twentyfive_above',
            'client_consented_uknown_age',
            'client_nonconsented_to_nine',
            'client_nonconsented_to_fourteen',
            'client_nonconsented_to_nineteen',
            'client_nonconsented_to_twentyfour',
            'client_nonconsented_to_twentyfive_above',
            'client_nonconsented_uknown_age'

        ));
    }

    public function appointment_charts()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')->remember($this->remember_period)->pluck('name', 'id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // main appointments
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '<=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.appntmnt_date', '=', date('Y-m-d'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->remember($this->remember_period)->pluck('name', 'id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // main appointments
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id', 'tbl_appointment.client_id', 'tbl_client.clinic_number')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.county_id', Auth::user()->county_id)->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // main appointments
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id', 'tbl_appointment.client_id', 'tbl_client.clinic_number')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)->pluck('tbl_partner.name', 'tbl_partner.id');
            $indicator = Indicator::select(['name', 'description'])->get();
            // main appointments
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id', 'tbl_appointment.client_id', 'tbl_client.clinic_number')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->remember($this->remember_period)->pluck('name', 'id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // main appointments
            $appointment = Appointments::select('id')
                ->remember($this->remember_period)
                ->count('id');

            $appointment_honoured = Appointments::select('id')
                ->where('date_attended', '=', DB::raw('appntmnt_date'))
                ->remember($this->remember_period)
                ->count('id');
            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::select('id')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('id');

            $all_future_apps = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->select('tbl_appointment.id', 'tbl_appointment.client_id', 'tbl_client.clinic_number', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period)
                ->count();

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END )) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }


        return view('new_dashboard.appointment_dashboard', compact(
            'all_partners',
            'indicator',
            'appointment',
            'appointment_honoured',
            'all_future_apps',
            'appointment_not_honoured',
            'appointment_honoured_male',
            'appointment_honoured_female',
            'appointment_honoured_uknown_gender',
            'appointment_honored_to_nine',
            'appointment_honored_to_fourteen',
            'appointment_honored_to_nineteen',
            'appointment_honored_to_twentyfour',
            'appointment_honored_to_twentyfive_above',
            'appointment_honored_to_uknown_age',
            'appointment_not_honoured_male',
            'appointment_not_honoured_female',
            'appointment_not_honoured_uknown_gender',
            'appointment_not_honored_to_nine',
            'appointment_not_honored_to_fourteen',
            'appointment_not_honored_to_nineteen',
            'appointment_not_honored_to_twentyfour',
            'appointment_not_honored_to_twentyfive_above',
            'appointment_not_honored_to_uknown_age',
        ));
    }
    public function missed_appointment_charts()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')->remember($this->remember_period)->pluck('name', 'id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->remember($this->remember_period)->pluck('name', 'id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
            ->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');
            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period)
                ->count();
        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner.id', '=', 'tbl_partner_facility.partner_id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.county_id', Auth::user()->county_id)->remember($this->remember_period)->pluck('tbl_partner.name', 'tbl_partner.id');

            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');
            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();
            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period)
                ->count();
        }

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->remember($this->remember_period)->pluck('name', 'id');
            $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();
            // main appointments
            // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period)
                ->count('id');

            // missed appointments

            $appointment_missed = Appointments::where('app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->count('id');
            $appointment_defaulted = Appointments::where('app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->count('id');
            $appointment_lftu = Appointments::where('app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->count('id');

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
               date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
               date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->pluck('count');

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period)
                ->count('tbl_appointment.id');
        }

        return view('new_dashboard.missed_dashboard', compact(
            'all_partners',
            'indicator',
            'appointment_not_honoured',
            'appointment_missed',
            'appointment_defaulted',
            'appointment_lftu',
            'appointment_missed_female',
            'appointment_missed_male',
            'appointment_missed_uknown_gender',
            'appointment_missed_to_nine',
            'appointment_missed_to_fourteen',
            'appointment_missed_to_nineteen',
            'appointment_missed_to_twentyfour',
            'appointment_missed_to_twentyfive_above',
            'appointment_missed_to_uknown_age',
            'appointment_defaulted_female',
            'appointment_defaulted_male',
            'appointment_defaulted_uknown_gender',
            'appointment_defaulted_to_nine',
            'appointment_defaulted_to_fourteen',
            'appointment_defaulted_to_nineteen',
            'appointment_defaulted_to_twentyfour',
            'appointment_defaulted_to_twentyfive_above',
            'appointment_defaulted_to_uknown_age',
            'appointment_ltfu_female',
            'appointment_ltfu_male',
            'appointment_ltfu_uknown_gender',
            'appointment_ltfu_to_nine',
            'appointment_ltfu_to_fourteen',
            'appointment_ltfu_to_nineteen',
            'appointment_ltfu_to_twentyfour',
            'appointment_ltfu_to_twentyfive_above',
            'appointment_ltfu_to_uknown_age'
        ));
    }

    public function filter_charts(Request $request)
    {
        // $data                = [];

        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        if (Auth::user()->access_level == 'Facility') {
        }
        if (Auth::user()->access_level == 'Partner') {
        }
        $all_partners = Partner::where('status', '=', 'Active')->remember($this->remember_period)->pluck('name', 'id');

        $indicator = Indicator::select(['name', 'description'])->remember($this->remember_period)->get();

        $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $missed_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->select('tbl_partner_facility.mfl_code')
            ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period)
            ->orderBy('tbl_appointment.created_at', 'DESC')
            ->groupBy('tbl_partner_facility.mfl_code')
            ->get();

        // active clients by gender
        $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_client.id')
            ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end"))
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `id` end"))
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `id` end"))
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.dob', '=', '')
            ->orWhereNull('tbl_client.dob')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        // appointment by gender
        $appointment_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->where('tbl_client.gender', '=', '2')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->where('tbl_client.gender', '=', '1')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->where('tbl_client.gender', '!=', '1')
            ->where('tbl_client.gender', '!=', '2')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // appointment by age
        $appointment_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $appointment_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $appointment_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $appointment_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.dob', '=', '')
            ->orWhereNull('tbl_client.dob')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        // Total missed appointment by gender
        $appointment_total_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_client.gender', '=', '1')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.gender', '=', '2')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $appointment_total_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_client.gender', '!=', '1')
            ->where('tbl_client.gender', '!=', '2')
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        // Total missed appointment by age
        $appointment_total_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $appointment_total_missed_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->orWhereNull('tbl_client.dob')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
            ->where('tbl_appointment.created_at', '>=', date($request->from))
            ->where('tbl_appointment.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // client charts
        $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_client.id')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_client.id')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // consented clients by gender

        $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.gender', '=', '2')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.gender', '=', '1')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.gender', '!=', '1')
            ->where('tbl_client.gender', '!=', '2')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // non consented clients by gender
        $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.gender', '=', '2')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);


        $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.gender', '=', '1')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.gender', '!=', '1')
            ->where('tbl_client.gender', '!=', '2')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // consented clients by age distribution
        $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_client.smsenable')
            ->where('tbl_client.dob', '=', '')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->orWhereNull('tbl_client.dob')
            ->where('tbl_client.smsenable', '=', 'Yes')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        // non consented clients by age distribution
        $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_client.smsenable')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->where('tbl_client.dob', '=', '')
            ->orWhereNull('tbl_client.dob')
            ->where('tbl_client.smsenable', '!=', 'Yes')
            ->where('tbl_client.created_at', '>=', date($request->from))
            ->where('tbl_client.created_at', '<=', date($request->to))
            ->remember($this->remember_period);

        if (!empty($selected_partners)) {
            $client = $client->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment = $appointment->where('tbl_partner_facility.partner_id', $selected_partners);
            $missed_appointment = $missed_appointment->where('tbl_partner_facility.partner_id', $selected_partners);
            //  $active_facilities = $active_facilities->where('tbl_partner_facility.partner_id', $selected_partners);
            $clients_male = $clients_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $clients_female = $clients_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_male = $appointment_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_female = $appointment_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_uknown_gender = $appointment_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_to_nine = $appointment_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_to_fourteen = $appointment_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_to_nineteen = $appointment_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_to_twentyfour = $appointment_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_to_twentyfive_above = $appointment_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_uknown_age = $appointment_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_female = $appointment_total_missed_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_male = $appointment_total_missed_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_uknown_gender = $appointment_total_missed_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_to_nine = $appointment_total_missed_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_to_fourteen = $appointment_total_missed_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_to_nineteen = $appointment_total_missed_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_to_twentyfour = $appointment_total_missed_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_to_twentyfive_above = $appointment_total_missed_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_total_missed_uknown_age = $appointment_total_missed_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);

            $client_consented = $client_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $client = $client->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment = $appointment->where('tbl_partner_facility.county_id', $selected_counties);
            $missed_appointment = $missed_appointment->where('tbl_partner_facility.county_id', $selected_counties);
            // $active_facilities = $active_facilities->where('tbl_partner_facility.county_id', $selected_counties);
            $clients_male = $clients_male->where('tbl_partner_facility.county_id', $selected_counties);
            $clients_female = $clients_female->where('tbl_partner_facility.county_id', $selected_counties);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_male = $appointment_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_female = $appointment_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_uknown_gender = $appointment_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_to_nine = $appointment_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_to_fourteen = $appointment_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_to_nineteen = $appointment_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_to_twentyfour = $appointment_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_to_twentyfive_above = $appointment_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_uknown_age = $appointment_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_female = $appointment_total_missed_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_male = $appointment_total_missed_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_uknown_gender = $appointment_total_missed_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_to_nine = $appointment_total_missed_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_to_fourteen = $appointment_total_missed_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_to_nineteen = $appointment_total_missed_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_to_twentyfour = $appointment_total_missed_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_to_twentyfive_above = $appointment_total_missed_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_total_missed_uknown_age = $appointment_total_missed_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);

            $client_consented = $client_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $client = $client->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment = $appointment->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $missed_appointment = $missed_appointment->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            //  $active_facilities = $active_facilities->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $clients_male = $clients_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $clients_female = $clients_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_male = $appointment_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_female = $appointment_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_uknown_gender = $appointment_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_to_nine = $appointment_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_to_fourteen = $appointment_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_to_nineteen = $appointment_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_to_twentyfour = $appointment_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_to_twentyfive_above = $appointment_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_uknown_age = $appointment_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_female = $appointment_total_missed_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_male = $appointment_total_missed_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_uknown_gender = $appointment_total_missed_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_to_nine = $appointment_total_missed_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_to_fourteen = $appointment_total_missed_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_to_nineteen = $appointment_total_missed_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_to_twentyfour = $appointment_total_missed_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_to_twentyfive_above = $appointment_total_missed_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_total_missed_uknown_age = $appointment_total_missed_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);

            $client_consented = $client_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $client = $client->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment = $appointment->where('tbl_partner_facility.mfl_code', $selected_facilites);
            //  $missed_appointment = $missed_appointment->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $active_facilities = $active_facilities->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $clients_male = $clients_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $clients_female = $clients_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_male = $appointment_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_female = $appointment_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_uknown_gender = $appointment_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_to_nine = $appointment_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_to_fourteen = $appointment_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_to_nineteen = $appointment_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_to_twentyfour = $appointment_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_to_twentyfive_above = $appointment_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_uknown_age = $appointment_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_female = $appointment_total_missed_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_male = $appointment_total_missed_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_uknown_gender = $appointment_total_missed_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_to_nine = $appointment_total_missed_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_to_fourteen = $appointment_total_missed_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_to_nineteen = $appointment_total_missed_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_to_twentyfour = $appointment_total_missed_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_to_twentyfive_above = $appointment_total_missed_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_total_missed_uknown_age = $appointment_total_missed_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);

            $client_consented = $client_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }
        $data["client"]        = $client->count();
        $data["appointment"]        = $appointment->count();
        $data["missed_appointment"]        = $missed_appointment->count();
        $data["clients_male"]        = $clients_male->count();
        $data["clients_female"]        = $clients_female->count();
        $data["unknown_gender"]        = $unknown_gender->count();
        $data["client_to_nine"]        = $client_to_nine->count();
        $data["client_to_fourteen"]        = $client_to_fourteen->count();
        $data["client_to_nineteen"]        = $client_to_nineteen->count();
        $data["client_to_twentyfour"]        = $client_to_twentyfour->count();
        $data["client_to_twentyfive_above"]        = $client_to_twentyfive_above->count();
        $data["client_unknown_age"]        = $client_unknown_age->count();
        $data["appointment_male"]        = $appointment_male->count();
        $data["appointment_female"]        = $appointment_female->count();
        $data["appointment_uknown_gender"]        = $appointment_uknown_gender->count();
        $data["appointment_to_nine"]        = $appointment_to_nine->count();
        $data["appointment_to_fourteen"]        = $appointment_to_fourteen->count();
        $data["appointment_to_nineteen"]        = $appointment_to_nineteen->count();
        $data["appointment_to_twentyfour"]        = $appointment_to_twentyfour->count();
        $data["appointment_to_twentyfive_above"]        = $appointment_to_twentyfive_above->count();
        $data["appointment_uknown_age"]        = $appointment_uknown_age->count();
        $data["appointment_total_missed_female"]        = $appointment_total_missed_female->count();
        $data["appointment_total_missed_male"]        = $appointment_total_missed_male->count();
        $data["appointment_total_missed_uknown_gender"]        = $appointment_total_missed_uknown_gender->count();
        $data["appointment_total_missed_to_nine"]        = $appointment_total_missed_to_nine->count();
        $data["appointment_total_missed_to_fourteen"]        = $appointment_total_missed_to_fourteen->count();
        $data["appointment_total_missed_to_nineteen"]        = $appointment_total_missed_to_nineteen->count();
        $data["appointment_total_missed_to_twentyfour"]        = $appointment_total_missed_to_twentyfour->count();
        $data["appointment_total_missed_to_twentyfive_above"]        = $appointment_total_missed_to_twentyfive_above->count();
        $data["appointment_total_missed_uknown_age"]        = $appointment_total_missed_uknown_age->count();

        $data["client_consented"]        = $client_consented->count();
        $data["client_nonconsented"]        = $client_nonconsented->count();
        $data["client_consented_male"]        = $client_consented_male->count();
        $data["client_consented_female"]        = $client_consented_female->count();
        $data["client_consented_uknown_gender"]        = $client_consented_uknown_gender->count();
        $data["client_nonconsented_male"]        = $client_nonconsented_male->count();
        $data["client_nonconsented_female"]        = $client_nonconsented_female->count();
        $data["client_nonconsented_uknown_gender"]        = $client_nonconsented_uknown_gender->count();
        $data["client_consented_to_nine"]        = $client_consented_to_nine->count();
        $data["client_consented_to_fourteen"]        = $client_consented_to_fourteen->count();
        $data["client_consented_to_nineteen"]        = $client_consented_to_nineteen->count();
        $data["client_consented_to_twentyfour"]        = $client_consented_to_twentyfour->count();
        $data["client_consented_to_twentyfive_above"]        = $client_consented_to_twentyfive_above->count();
        $data["client_consented_uknown_age"]        = $client_consented_uknown_age->count();
        $data["client_nonconsented_to_nine"]        = $client_nonconsented_to_nine->count();
        $data["client_nonconsented_to_fourteen"]        = $client_nonconsented_to_fourteen->count();
        $data["client_nonconsented_to_nineteen"]        = $client_nonconsented_to_nineteen->count();
        $data["client_nonconsented_to_twentyfour"]        = $client_nonconsented_to_twentyfour->count();
        $data["client_nonconsented_to_twentyfive_above"]        = $client_nonconsented_to_twentyfive_above->count();
        $data["client_nonconsented_uknown_age"]        = $client_nonconsented_uknown_age->count();


        //return view('new_dashboard.main_dashbaord', compact('data'));
        return view('new_dashboard.main_dashbaord', compact(
            'all_partners',
            'active_facilities',
            'indicator',
            'client',
            'appointment',
            'missed_appointment',
            'clients_male',
            'clients_female',
            'unknown_gender',
            'client_to_nine',
            'client_to_fourteen',
            'client_to_nineteen',
            'client_to_twentyfour',
            'client_to_twentyfive_above',
            'client_unknown_age',
            'appointment_male',
            'appointment_female',
            'appointment_uknown_gender',
            'appointment_to_nine',
            'appointment_to_fourteen',
            'appointment_to_nineteen',
            'appointment_to_twentyfour',
            'appointment_to_twentyfive_above',
            'appointment_uknown_age',
            'appointment_total_missed_female',
            'appointment_total_missed_male',
            'appointment_total_missed_uknown_gender',
            'appointment_total_missed_to_nine',
            'appointment_total_missed_to_fourteen',
            'appointment_total_missed_to_nineteen',
            'appointment_total_missed_to_twentyfour',
            'appointment_total_missed_to_twentyfive_above',
            'appointment_total_missed_uknown_age',
            'client_consented',
            'client_nonconsented',
            'client_consented_male',
            'client_consented_female',
            'client_consented_uknown_gender',
            'client_nonconsented_male',
            'client_nonconsented_female',
            'client_nonconsented_uknown_gender',
            'client_consented_to_nine',
            'client_consented_to_fourteen',
            'client_consented_to_nineteen',
            'client_consented_to_twentyfour',
            'client_consented_to_twentyfive_above',
            'client_consented_uknown_age',
            'client_nonconsented_to_nine',
            'client_nonconsented_to_fourteen',
            'client_nonconsented_to_nineteen',
            'client_nonconsented_to_twentyfour',
            'client_nonconsented_to_twentyfive_above',
            'client_nonconsented_uknown_age'

        ));
    }
    public function filter_dashboard_charts(Request $request)
    {
        $data                = [];

        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;
        $selected_from = $request->from;
        $selected_to = $request->to;
        $selected_module = $request->module;

        if (Auth::user()->access_level == 'Facility') {
            $facilities_ever_enrolled = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->select('tbl_partner_facility.mfl_code')->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)->remember($this->remember_period);

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select('tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '=', '1'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Partner') {

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $facilities_ever_enrolled =  PartnerFacility::select('tbl_partner_facility.mfl_code')->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)->remember($this->remember_period);

            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '=', '1'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);


            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);


            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);


            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);


            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Sub County') {

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $facilities_ever_enrolled =  PartnerFacility::select('tbl_partner_facility.mfl_code')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)->remember($this->remember_period);

            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '=', '1'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);


            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);


            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);


            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);


            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'County') {

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $facilities_ever_enrolled =  PartnerFacility::select('tbl_partner_facility.mfl_code')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)->remember($this->remember_period);

            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '=', '1'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);


            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);


            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);


            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);


            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
        }

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {


            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_ever_enrolled = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.id', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period);

            $facilities_ever_enrolled = PartnerFacility::select('tbl_partner_facility.mfl_code')->remember($this->remember_period);

            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '=', '1'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where([['tbl_client.gender', '!=', '1'], ['tbl_client.gender', '!=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);


            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);


            $client_unknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);
        }

        if (!empty($selected_partners)) {
            $client = $client->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_ever_enrolled = $client_ever_enrolled->where('tbl_partner_facility.partner_id', $selected_partners);
            $active_facilities = $active_facilities->where('tbl_partner_facility.partner_id', $selected_partners);
            $facilities_ever_enrolled = $facilities_ever_enrolled->where('tbl_partner_facility.partner_id', $selected_partners);
            $clients_male = $clients_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $clients_female = $clients_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $client = $client->where('tbl_partner_facility.county_id', $selected_counties);
            $client_ever_enrolled = $client_ever_enrolled->where('tbl_partner_facility.county_id', $selected_counties);
            $active_facilities = $active_facilities->where('tbl_partner_facility.county_id', $selected_counties);
            $facilities_ever_enrolled = $facilities_ever_enrolled->where('county_id', $selected_counties);
            $clients_male = $clients_male->where('tbl_partner_facility.county_id', $selected_counties);
            $clients_female = $clients_female->where('tbl_partner_facility.county_id', $selected_counties);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $client = $client->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_ever_enrolled = $client_ever_enrolled->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $active_facilities = $active_facilities->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $facilities_ever_enrolled = $facilities_ever_enrolled->where('sub_county_id', $selected_subcounties);
            $clients_male = $clients_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $clients_female = $clients_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $client = $client->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_ever_enrolled = $client_ever_enrolled->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $active_facilities = $active_facilities->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $facilities_ever_enrolled = $facilities_ever_enrolled->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $clients_male = $clients_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $clients_female = $clients_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $unknown_gender = $unknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_nine = $client_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_fourteen = $client_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_nineteen = $client_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_unknown_age = $client_unknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }
        if (!empty($selected_from || $selected_to)) {
            $client = $client->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_ever_enrolled = $client_ever_enrolled->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            // $active_facilities = $active_facilities->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '>=', date($request->to));
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_partner_facility.mfl_code, MAX(DATE(tbl_appointment.created_at)) as max_date')
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', Carbon::now()->subMonths(6))
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', date($request->from))
                ->where(DB::raw('(SELECT MAX(DATE(tbl_appointment.created_at)) from tbl_appointment)'), '>=', date($request->to))
                // ->whereRaw('tbl_appointment.created_at', '>=', date($request->from))->whereDate('tbl_appointment.created_at', '>=', date($request->to))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->remember($this->remember_period);

            $facilities_ever_enrolled = $facilities_ever_enrolled->where('tbl_partner_facility.created_at', '>=', date($request->from))->where('tbl_partner_facility.created_at', '<=', date($request->to));
            $clients_male = $clients_male->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $clients_female = $clients_female->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $unknown_gender = $unknown_gender->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_to_nine = $client_to_nine->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_to_fourteen = $client_to_fourteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_to_nineteen = $client_to_nineteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_to_twentyfour = $client_to_twentyfour->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_to_twentyfive_above = $client_to_twentyfive_above->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_unknown_age = $client_unknown_age->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
        }
        if (!empty($selected_module == 'DSD')) {
            $client = $client->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_ever_enrolled = $client_ever_enrolled->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $active_facilities = $active_facilities->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $facilities_ever_enrolled = $facilities_ever_enrolled->join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id')->groupBy('tbl_partner_facility.mfl_code')->having(DB::raw('count(tbl_client.mfl_code)'), '>', 0);
            $clients_male = $clients_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $clients_female = $clients_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $unknown_gender = $unknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_to_nine = $client_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_to_fourteen = $client_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_to_nineteen = $client_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_to_twentyfour = $client_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_to_twentyfive_above = $client_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_unknown_age = $client_unknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
        }
        if (!empty($selected_module == 'PMTCT')) {
            $client = $client->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_ever_enrolled = $client_ever_enrolled->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $active_facilities = $active_facilities->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $facilities_ever_enrolled = $facilities_ever_enrolled->join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')->groupBy('tbl_partner_facility.mfl_code')->having(DB::raw('count(tbl_client.mfl_code)'), '>', 0);
            $clients_male = $clients_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $clients_female = $clients_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $unknown_gender = $unknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_to_nine = $client_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_to_fourteen = $client_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_to_nineteen = $client_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_to_twentyfour = $client_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_to_twentyfive_above = $client_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_unknown_age = $client_unknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
        }
        $data["client"]        = $client->count();
        $data["facilities_ever_enrolled"]        = $facilities_ever_enrolled->count();
        $data["client_ever_enrolled"]        = $client_ever_enrolled->count();
        $data["active_facilities"]        = $active_facilities->get()->count();
        $data["clients_male"]        = $clients_male->count();
        $data["clients_female"]        = $clients_female->count();
        $data["unknown_gender"]        = $unknown_gender->count();
        $data["client_to_nine"]        = $client_to_nine->pluck('count');
        $data["client_to_fourteen"]        = $client_to_fourteen->pluck('count');
        $data["client_to_nineteen"]        = $client_to_nineteen->pluck('count');
        $data["client_to_twentyfour"]        = $client_to_twentyfour->pluck('count');
        $data["client_to_twentyfive_above"]        = $client_to_twentyfive_above->pluck('count');
        $data["client_unknown_age"]        = $client_unknown_age->count();


        return $data;
    }
    public function filter_client_charts(Request $request)
    {
        $data                = [];
        // client charts
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;
        $selected_from = $request->from;
        $selected_to = $request->to;
        $selected_module = $request->module;

        if (Auth::user()->access_level == 'Facility') {

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);


            $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
        }

        if (Auth::user()->access_level == 'Partner') {
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);


            $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Sub County') {
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);


            $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'County') {
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);


            $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->remember($this->remember_period);

            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.id')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->remember($this->remember_period);

            // consented clients by gender

            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);

            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $client_consented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // non consented clients by gender
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);


            $client_nonconsented_female =  Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $client_nonconsented_uknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // consented clients by age distribution
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_consented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->remember($this->remember_period);

            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_nonconsented_to_fourteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->remember($this->remember_period);

            $client_nonconsented_uknown_age = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->remember($this->remember_period);
        }

        if (!empty($selected_partners)) {
            $client = $client->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented = $client_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $client = $client->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented = $client_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $client = $client->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented = $client_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $client = $client->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented = $client_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented = $client_nonconsented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_male = $client_consented_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_female = $client_consented_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }
        if (!empty($selected_from || $selected_to)) {
            $client = $client->where('tbl_client.created_at', '>=', date($selected_from))->where('tbl_client.created_at', '<=', date($selected_to));
            $client_consented = $client_consented->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented = $client_nonconsented->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_male = $client_consented_male->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_female = $client_consented_female->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_uknown_gender = $client_consented_uknown_gender->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_male = $client_nonconsented_male->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_female = $client_nonconsented_female->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_to_nine = $client_consented_to_nine->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_to_fourteen = $client_consented_to_fourteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_to_nineteen = $client_consented_to_nineteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_consented_uknown_age = $client_consented_uknown_age->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->where('tbl_client.created_at', '>=', date($request->from))->where('tbl_client.created_at', '<=', date($request->to));
        }
        if (!empty($selected_module == 'DSD')) {
            $client = $client->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented = $client_consented->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented = $client_nonconsented->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_male = $client_consented_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_female = $client_consented_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_uknown_gender = $client_consented_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_male = $client_nonconsented_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_female = $client_nonconsented_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_to_nine = $client_consented_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_to_fourteen = $client_consented_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_to_nineteen = $client_consented_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_consented_uknown_age = $client_consented_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
        }
        if (!empty($selected_module == 'PMTCT')) {
            $client = $client->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented = $client_consented->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented = $client_nonconsented->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_male = $client_consented_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_female = $client_consented_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_uknown_gender = $client_consented_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_male = $client_nonconsented_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_female = $client_nonconsented_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_uknown_gender = $client_nonconsented_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_to_nine = $client_consented_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_to_fourteen = $client_consented_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_to_nineteen = $client_consented_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_to_twentyfour = $client_consented_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_to_twentyfive_above = $client_consented_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_consented_uknown_age = $client_consented_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_to_nine = $client_nonconsented_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_to_fourteen = $client_nonconsented_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_to_nineteen = $client_nonconsented_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_to_twentyfour = $client_nonconsented_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_to_twentyfive_above = $client_nonconsented_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $client_nonconsented_uknown_age = $client_nonconsented_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
        }


        $data["client"]        = $client->count();
        $data["client_consented"]        = $client_consented->count();
        $data["client_nonconsented"]        = $client_nonconsented->count();
        $data["client_consented_male"]        = $client_consented_male->count();
        $data["client_consented_female"]        = $client_consented_female->count();
        $data["client_consented_uknown_gender"]        = $client_consented_uknown_gender->count();
        $data["client_nonconsented_male"]        = $client_nonconsented_male->count();
        $data["client_nonconsented_female"]        = $client_nonconsented_female->count();
        $data["client_nonconsented_uknown_gender"]        = $client_nonconsented_uknown_gender->count();
        $data["client_consented_to_nine"]        = $client_consented_to_nine->pluck('count');
        $data["client_consented_to_fourteen"]        = $client_consented_to_fourteen->pluck('count');
        $data["client_consented_to_nineteen"]        = $client_consented_to_nineteen->pluck('count');
        $data["client_consented_to_twentyfour"]        = $client_consented_to_twentyfour->pluck('count');
        $data["client_consented_to_twentyfive_above"]        = $client_consented_to_twentyfive_above->pluck('count');
        $data["client_consented_uknown_age"]        = $client_consented_uknown_age->count();
        $data["client_nonconsented_to_nine"]        = $client_nonconsented_to_nine->pluck('count');
        $data["client_nonconsented_to_fourteen"]        = $client_nonconsented_to_fourteen->pluck('count');
        $data["client_nonconsented_to_nineteen"]        = $client_nonconsented_to_nineteen->pluck('count');
        $data["client_nonconsented_to_twentyfour"]        = $client_nonconsented_to_twentyfour->pluck('count');
        $data["client_nonconsented_to_twentyfive_above"]        = $client_nonconsented_to_twentyfive_above->pluck('count');
        $data["client_nonconsented_uknown_age"]        = $client_nonconsented_uknown_age->count();

        return $data;
    }
    public function filter_appointment_charts(Request $request)
    {
        $data                = [];
        // client charts
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;
        $selected_from = $request->from;
        $selected_to = $request->to;
        $selected_module = $request->module;

        if (Auth::user()->access_level == 'Facility') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Partner') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Sub County') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'County') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->remember($this->remember_period);

            $appointment_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);

            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->remember($this->remember_period);

            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->remember($this->remember_period);

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);

            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);
        }

        if (!empty($selected_partners)) {
            $appointment = $appointment->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honoured = $appointment_honoured->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honoured_male = $appointment_honoured_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honoured_female = $appointment_honoured_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_nine = $appointment_honored_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honoured_male = $appointment_not_honoured_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honoured_female = $appointment_not_honoured_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $appointment = $appointment->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honoured = $appointment_honoured->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honoured_male = $appointment_honoured_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honoured_female = $appointment_honoured_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_nine = $appointment_honored_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honoured_male = $appointment_not_honoured_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honoured_female = $appointment_not_honoured_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $appointment = $appointment->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honoured = $appointment_honoured->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honoured_male = $appointment_honoured_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honoured_female = $appointment_honoured_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_nine = $appointment_honored_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honoured_male = $appointment_not_honoured_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honoured_female = $appointment_not_honoured_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $appointment = $appointment->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honoured = $appointment_honoured->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honoured_male = $appointment_honoured_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honoured_female = $appointment_honoured_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_nine = $appointment_honored_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honoured_male = $appointment_not_honoured_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honoured_female = $appointment_not_honoured_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }
        if (!empty($selected_from || $selected_to)) {
            $appointment = $appointment->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honoured = $appointment_honoured->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honoured_male = $appointment_honoured_male->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honoured_female = $appointment_honoured_female->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_nine = $appointment_honored_to_nine->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honoured_male = $appointment_not_honoured_male->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honoured_female = $appointment_not_honoured_female->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
        }
        if (!empty($selected_module == 'DSD')) {
            $appointment = $appointment->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honoured = $appointment_honoured->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honoured = $appointment_not_honoured->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honoured_male = $appointment_honoured_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honoured_female = $appointment_honoured_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_nine = $appointment_honored_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honoured_male = $appointment_not_honoured_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honoured_female = $appointment_not_honoured_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
        }
        if (!empty($selected_module == 'PMTCT')) {
            $appointment = $appointment->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honoured = $appointment_honoured->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honoured = $appointment_not_honoured->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honoured_male = $appointment_honoured_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honoured_female = $appointment_honoured_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honoured_uknown_gender = $appointment_honoured_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_nine = $appointment_honored_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_fourteen = $appointment_honored_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_nineteen = $appointment_honored_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_twentyfour = $appointment_honored_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_twentyfive_above = $appointment_honored_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_honored_to_uknown_age = $appointment_honored_to_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honoured_male = $appointment_not_honoured_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honoured_female = $appointment_not_honoured_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honoured_uknown_gender = $appointment_not_honoured_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_nine = $appointment_not_honored_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_fourteen = $appointment_not_honored_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_nineteen = $appointment_not_honored_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_twentyfour = $appointment_not_honored_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_twentyfive_above = $appointment_not_honored_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_not_honored_to_uknown_age = $appointment_not_honored_to_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
        }

        $data["appointment"]        = $appointment->count();
        $data["appointment_honoured"]        = $appointment_honoured->count();
        $data["appointment_not_honoured"]        = $appointment_not_honoured->count();
        $data["appointment_honoured_male"]        = $appointment_honoured_male->count();
        $data["appointment_honoured_female"]        = $appointment_honoured_female->count();
        $data["appointment_honoured_uknown_gender"]        = $appointment_honoured_uknown_gender->count();
        $data["appointment_honored_to_nine"]        = $appointment_honored_to_nine->pluck('count');
        $data["appointment_honored_to_fourteen"]        = $appointment_honored_to_fourteen->pluck('count');
        $data["appointment_honored_to_nineteen"]        = $appointment_honored_to_nineteen->pluck('count');
        $data["appointment_honored_to_twentyfour"]        = $appointment_honored_to_twentyfour->pluck('count');
        $data["appointment_honored_to_twentyfive_above"]        = $appointment_honored_to_twentyfive_above->pluck('count');
        $data["appointment_honored_to_uknown_age"]        = $appointment_honored_to_uknown_age->count();
        $data["appointment_not_honoured_male"]        = $appointment_not_honoured_male->count();
        $data["appointment_not_honoured_female"]        = $appointment_not_honoured_female->count();
        $data["appointment_not_honoured_uknown_gender"]        = $appointment_not_honoured_uknown_gender->count();
        $data["appointment_not_honored_to_nine"]        = $appointment_not_honored_to_nine->pluck('count');
        $data["appointment_not_honored_to_fourteen"]        = $appointment_not_honored_to_fourteen->pluck('count');
        $data["appointment_not_honored_to_nineteen"]        = $appointment_not_honored_to_nineteen->pluck('count');
        $data["appointment_not_honored_to_twentyfour"]        = $appointment_not_honored_to_twentyfour->pluck('count');
        $data["appointment_not_honored_to_twentyfive_above"]        = $appointment_not_honored_to_twentyfive_above->pluck('count');
        $data["appointment_not_honored_to_uknown_age"]        = $appointment_not_honored_to_uknown_age->count();

        return $data;
    }
    public function filter_missed_appointment_charts(Request $request)
    {
        $data                = [];
        // client charts
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;
        $selected_from = $request->from;
        $selected_to = $request->to;
        $selected_module = $request->module;

        if (Auth::user()->access_level == 'Facility') {
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(id) as count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Partner') {
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(id) as count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Sub County') {
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(id) as count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'County') {
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(id) as count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
                ->remember($this->remember_period);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $appointment_not_honoured = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("COUNT(id) as count"))
                ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->remember($this->remember_period);
            // missed appointments

            $appointment_missed = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_defaulted = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_lftu = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);

            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->remember($this->remember_period);

            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);

            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->remember($this->remember_period);

            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->remember($this->remember_period);

            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->remember($this->remember_period);

            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->remember($this->remember_period);

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);

            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"), '=', '')
                ->orWhereNull(\DB::raw("CASE
                WHEN ( locate( '/', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%m/%d/%Y' ), '%Y-%m-%d' )
                WHEN ( locate( '-', `tbl_client`.`dob` ) > 0 ) THEN
                date_format( str_to_date( `tbl_client`.`dob`, '%Y-%m-%d' ), '%Y-%m-%d' ) END"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->remember($this->remember_period);
        }

        if (!empty($selected_partners)) {
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed = $appointment_missed->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted = $appointment_defaulted->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_lftu = $appointment_lftu->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_female = $appointment_missed_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_male = $appointment_missed_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_nine = $appointment_missed_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_female = $appointment_defaulted_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_male = $appointment_defaulted_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_female = $appointment_ltfu_female->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_male = $appointment_ltfu_male->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->where('tbl_partner_facility.partner_id', $selected_partners);
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed = $appointment_missed->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted = $appointment_defaulted->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_lftu = $appointment_lftu->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_female = $appointment_missed_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_male = $appointment_missed_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_nine = $appointment_missed_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_female = $appointment_defaulted_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_male = $appointment_defaulted_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_female = $appointment_ltfu_female->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_male = $appointment_ltfu_male->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->where('tbl_partner_facility.county_id', $selected_counties);
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed = $appointment_missed->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted = $appointment_defaulted->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_lftu = $appointment_lftu->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_female = $appointment_missed_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_male = $appointment_missed_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_nine = $appointment_missed_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_female = $appointment_defaulted_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_male = $appointment_defaulted_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_female = $appointment_ltfu_female->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_male = $appointment_ltfu_male->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed = $appointment_missed->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted = $appointment_defaulted->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_lftu = $appointment_lftu->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_female = $appointment_missed_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_male = $appointment_missed_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_nine = $appointment_missed_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_female = $appointment_defaulted_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_male = $appointment_defaulted_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_female = $appointment_ltfu_female->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_male = $appointment_ltfu_male->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }
        if (!empty($selected_from || $selected_to)) {
            $appointment_not_honoured = $appointment_not_honoured->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed = $appointment_missed->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted = $appointment_defaulted->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_lftu = $appointment_lftu->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_female = $appointment_missed_female->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_male = $appointment_missed_male->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_nine = $appointment_missed_to_nine->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_female = $appointment_defaulted_female->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_male = $appointment_defaulted_male->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_female = $appointment_ltfu_female->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_male = $appointment_ltfu_male->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to));
        }
        if (!empty($selected_module == 'DSD')) {
            $appointment_not_honoured = $appointment_not_honoured->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed = $appointment_missed->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted = $appointment_defaulted->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_lftu = $appointment_lftu->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_female = $appointment_missed_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_male = $appointment_missed_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_nine = $appointment_missed_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_female = $appointment_defaulted_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_male = $appointment_defaulted_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_female = $appointment_ltfu_female->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_male = $appointment_ltfu_male->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->join('tbl_dfc_module', 'tbl_client.id', '=', 'tbl_dfc_module.client_id');
        }
        if (!empty($selected_module == 'PMTCT')) {
            $appointment_not_honoured = $appointment_not_honoured->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed = $appointment_missed->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted = $appointment_defaulted->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_lftu = $appointment_lftu->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_female = $appointment_missed_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_male = $appointment_missed_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_uknown_gender = $appointment_missed_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_nine = $appointment_missed_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_fourteen = $appointment_missed_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_nineteen = $appointment_missed_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_twentyfour = $appointment_missed_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_twentyfive_above = $appointment_missed_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_missed_to_uknown_age = $appointment_missed_to_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_female = $appointment_defaulted_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_male = $appointment_defaulted_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_uknown_gender = $appointment_defaulted_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_nine = $appointment_defaulted_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_fourteen = $appointment_defaulted_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_nineteen = $appointment_defaulted_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_twentyfour = $appointment_defaulted_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_twentyfive_above = $appointment_defaulted_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_defaulted_to_uknown_age = $appointment_defaulted_to_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_female = $appointment_ltfu_female->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_male = $appointment_ltfu_male->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_uknown_gender = $appointment_ltfu_uknown_gender->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_nine = $appointment_ltfu_to_nine->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_fourteen = $appointment_ltfu_to_fourteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_nineteen = $appointment_ltfu_to_nineteen->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_twentyfour = $appointment_ltfu_to_twentyfour->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_twentyfive_above = $appointment_ltfu_to_twentyfive_above->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
            $appointment_ltfu_to_uknown_age = $appointment_ltfu_to_uknown_age->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id');
        }

        $data["appointment_not_honoured"]        = $appointment_not_honoured->count();
        $data["appointment_missed"]        = $appointment_missed->count();
        $data["appointment_defaulted"]        = $appointment_defaulted->count();
        $data["appointment_lftu"]        = $appointment_lftu->count();
        $data["appointment_missed_female"]        = $appointment_missed_female->count();
        $data["appointment_missed_male"]        = $appointment_missed_male->count();
        $data["appointment_missed_uknown_gender"]        = $appointment_missed_uknown_gender->count();
        $data["appointment_missed_to_nine"]        = $appointment_missed_to_nine->pluck('count');
        $data["appointment_missed_to_fourteen"]        = $appointment_missed_to_fourteen->pluck('count');
        $data["appointment_missed_to_nineteen"]        = $appointment_missed_to_nineteen->pluck('count');
        $data["appointment_missed_to_twentyfour"]        = $appointment_missed_to_twentyfour->pluck('count');
        $data["appointment_missed_to_twentyfive_above"]        = $appointment_missed_to_twentyfive_above->pluck('count');
        $data["appointment_missed_to_uknown_age"]        = $appointment_missed_to_uknown_age->count();
        $data["appointment_defaulted_female"]        = $appointment_defaulted_female->count();
        $data["appointment_defaulted_male"]        = $appointment_defaulted_male->count();
        $data["appointment_defaulted_uknown_gender"]        = $appointment_defaulted_uknown_gender->count();
        $data["appointment_defaulted_to_nine"]        = $appointment_defaulted_to_nine->pluck('count');
        $data["appointment_defaulted_to_fourteen"]        = $appointment_defaulted_to_fourteen->pluck('count');
        $data["appointment_defaulted_to_nineteen"]        = $appointment_defaulted_to_nineteen->pluck('count');
        $data["appointment_defaulted_to_twentyfour"]        = $appointment_defaulted_to_twentyfour->pluck('count');
        $data["appointment_defaulted_to_twentyfive_above"]        = $appointment_defaulted_to_twentyfive_above->pluck('count');
        $data["appointment_defaulted_to_uknown_age"]        = $appointment_defaulted_to_uknown_age->count();
        $data["appointment_ltfu_female"]        = $appointment_ltfu_female->count();
        $data["appointment_ltfu_male"]        = $appointment_ltfu_male->count();
        $data["appointment_ltfu_uknown_gender"]        = $appointment_ltfu_uknown_gender->count();
        $data["appointment_ltfu_to_nine"]        = $appointment_ltfu_to_nine->pluck('count');
        $data["appointment_ltfu_to_fourteen"]        = $appointment_ltfu_to_fourteen->pluck('count');
        $data["appointment_ltfu_to_nineteen"]        = $appointment_ltfu_to_nineteen->pluck('count');
        $data["appointment_ltfu_to_twentyfour"]        = $appointment_ltfu_to_twentyfour->pluck('count');
        $data["appointment_ltfu_to_twentyfive_above"]        = $appointment_ltfu_to_twentyfive_above->pluck('count');
        $data["appointment_ltfu_to_uknown_age"]        = $appointment_ltfu_to_uknown_age->count();

        return $data;
    }
}
