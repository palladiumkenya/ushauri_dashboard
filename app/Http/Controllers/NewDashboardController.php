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
use App\Models\Indicator;
use Auth;
use Carbon\Carbon;
use DB;

class NewDashboardController extends Controller
{
    public function dashboard()
    {

        // showing all the active clients, all appointments, missed appointments
        if (Auth::user()->access_level == 'Facility') {

        }
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count('tbl_client.clinic_number');

            $indicator = Indicator::select(['name', 'description'])->get();
            $appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $missed_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_appointment.id')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();


              //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('id')->where([['tbl_client.gender', '=', '2'], ['tbl_client.status', '=', 'Active'],])
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $unknown_gender = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_nine = Cache::remember('client_to_nine', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });

            $client_to_fourteen = Cache::remember('client-fourteen', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });

            $client_to_nineteen = Cache::remember('client-nineteen', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });

            $client_to_twentyfour = Cache::remember('client-twentyfour', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });

            $client_to_twentyfive_above = Cache::remember('client-twentyfive-above', 10, function () {
            return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`dob` end)) AS count"))
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });

            $client_unknown_age = Cache::remember('client-unknown-age', 10, function () {
            return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });

            // appointment by gender
            $appointment_male = Cache::remember('appointment-male', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $appointment_female = Cache::remember('appointment-female', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $appointment_uknown_gender = Cache::remember('appointment-uknown-gender', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // appointment by age
            $appointment_to_nine = Cache::remember('appointment-to-nine', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_to_fourteen = Cache::remember('appointment-to-fourteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_to_nineteen = Cache::remember('appointment-to-nineteen', 10, function () {
           return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_to_twentyfour = Cache::remember('appointment-to-twentyfour', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_to_twentyfive_above = Cache::remember('appointment-to-twentyfive-above', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_uknown_age = Cache::remember('appointment-uknown-age', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });

            // Total missed appointment by gender
            $appointment_total_missed_female = Cache::remember('appointment-total-missed-female', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $appointment_total_missed_male = Cache::remember('appointment-total-missed-male', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $appointment_total_missed_uknown_gender = Cache::remember('appointment-total-missed-uknown-gender', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });

            // Total missed appointment by age
            $appointment_total_missed_to_nine = Cache::remember('appointment-total-missed-to-nine', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_total_missed_to_fourteen = Cache::remember('appointment-total-missed-to-fourteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_total_missed_to_nineteen = Cache::remember('appointment-total-missed-to-nineteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_total_missed_to_twentyfour = Cache::remember('appointment-total-missed-to-twentyfour', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_total_missed_to_twentyfive_above = Cache::remember('appointment-total-missed-to-twentyfive_above', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $appointment_total_missed_uknown_age = Cache::remember('appointment-total-missed-uknown-age', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.dob', '=', '')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // client charts
            $client_consented = Cache::remember('client-consented', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_clientsmsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $client_nonconsented = Cache::remember('client-nonconsented', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // consented clients by gender

            $client_consented_male = Cache::remember('client-consented-male', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $client_consented_female = Cache::remember('client-consented-female', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $client_consented_uknown_gender = Cache::remember('client-consented-uknown-gender', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // non consented clients by gender
            $client_nonconsented_male = Cache::remember('client-nonconsented-male', 10, function () {
            return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $client_nonconsented_female = Cache::remember('client-nonconsented-female', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            $client_nonconsented_uknown_gender = Cache::remember('client-nonconsented-uknown-gender', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // consented clients by age distribution
            $client_consented_to_nine = Cache::remember('client-consented-to-nine', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_consented_to_fourteen = Cache::remember('tbl-client', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_consented_to_nineteen = Cache::remember('client-consented-to-nineteen', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_consented_to_twentyfour = Cache::remember('client-consented-to-twentyfour', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_consented_to_twentyfive_above = Cache::remember('client-consented-to-twentyfive-above', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_consented_uknown_age = Cache::remember('client-consented-uknown-age', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.dob', '=', '')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Cache::remember('client-nonconsented-to-nine', 10, function () {
            return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_nonconsented_to_fourteen = Cache::remember('client-nonconsented-to-fourteen', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_nonconsented_to_nineteen = Cache::remember('client-nonconsented-to-nineteen', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_nonconsented_to_twentyfour = Cache::remember('client-nonconsented-to-twentyfour', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_nonconsented_to_twentyfive_above = Cache::remember('client-nonconsented-to-twentyfive-above', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->pluck('count');
            });
            $client_nonconsented_uknown_age = Cache::remember('client-nonconsented-uknown-age', 10, function () {
                return Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_client.smsenable')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            });

        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');
            $client = Client::where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->count('clinic_number');

            $indicator = Indicator::select(['name', 'description'])->get();
            $appointment = Appointments::select('id')
                ->count();
            $missed_appointment = Appointments::select('id')
            ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->count();
               // dd($missed_appointment);
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();


              //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::select('id')->where([['gender', '=', '2'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->count();
            $unknown_gender = Client::where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->count();

            $client_to_nine = Cache::remember('client_to_nine', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });

            $client_to_fourteen = Cache::remember('client-fourteen', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 14)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });

            $client_to_nineteen = Cache::remember('client-nineteen', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 15) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });

            $client_to_twentyfour = Cache::remember('client-twentyfour', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });

            $client_to_twentyfive_above = Cache::remember('client-twentyfive-above', 10, function () {
            return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 25)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });

            $client_unknown_age = Cache::remember('client-unknown-age', 10, function () {
            return Client::where('dob', '=', '')
                ->orWhereNull('dob')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->count();
            });

            // appointment by gender
            $appointment_male = Cache::remember('appointment-male', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->count();
            });
            $appointment_female = Cache::remember('appointment-female', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->count();
            });
            $appointment_uknown_gender = Cache::remember('appointment-uknown-gender', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->count();
            });
            // appointment by age
            $appointment_to_nine = Cache::remember('appointment-to-nine', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->pluck('count');
            });
            $appointment_to_fourteen = Cache::remember('appointment-to-fourteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->pluck('count');
            });
            $appointment_to_nineteen = Cache::remember('appointment-to-nineteen', 10, function () {
           return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->pluck('count');
            });
            $appointment_to_twentyfour = Cache::remember('appointment-to-twentyfour', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->pluck('count');
            });
            $appointment_to_twentyfive_above = Cache::remember('appointment-to-twentyfive-above', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->pluck('count');
            });
            $appointment_uknown_age = Cache::remember('appointment-uknown-age', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->count();
            });

            // Total missed appointment by gender
            $appointment_total_missed_female = Cache::remember('appointment-total-missed-female', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.gender', '=', '1')
                ->count();
            });
            $appointment_total_missed_male = Cache::remember('appointment-total-missed-male', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->count();
            });
            $appointment_total_missed_uknown_gender = Cache::remember('appointment-total-missed-uknown-gender', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->count();
            });

            // Total missed appointment by age
            $appointment_total_missed_to_nine = Cache::remember('appointment-total-missed-to-nine', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->pluck('count');
            });
            $appointment_total_missed_to_fourteen = Cache::remember('appointment-total-missed-to-fourteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->pluck('count');
            });
            $appointment_total_missed_to_nineteen = Cache::remember('appointment-total-missed-to-nineteen', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->pluck('count');
            });
            $appointment_total_missed_to_twentyfour = Cache::remember('appointment-total-missed-to-twentyfour', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->pluck('count');
            });
            $appointment_total_missed_to_twentyfive_above = Cache::remember('appointment-total-missed-to-twentyfive_above', 10, function () {
            return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->pluck('count');
            });
            $appointment_total_missed_uknown_age = Cache::remember('appointment-total-missed-uknown-age', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->whereIn('tbl_appointment.app_status', ['Defaulted', 'LTFU', 'Missed'])
                ->count();
            });
            // client charts
            $client_consented = Cache::remember('client-consented', 10, function () {
                return Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('smsenable', '=', 'Yes')
                ->count();
            });
            $client_nonconsented = Cache::remember('client-nonconsented', 10, function () {
                return Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('smsenable', '!=', 'Yes')
                ->count();
            });
            // consented clients by gender

            $client_consented_male = Cache::remember('client-consented-male', 10, function () {
                return Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '2')
                ->count();
            });
            $client_consented_female = Cache::remember('client-consented-female', 10, function () {
                return Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '1')
                ->count();
            });
            $client_consented_uknown_gender = Cache::remember('client-consented-uknown-gender', 10, function () {
                return Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->count();
            });
            // non consented clients by gender
            $client_nonconsented_male = Cache::remember('client-nonconsented-male', 10, function () {
            return Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '2')
                ->count();
            });
            $client_nonconsented_female = Cache::remember('client-nonconsented-female', 10, function () {
                return Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '1')
                ->count();
            });
            $client_nonconsented_uknown_gender = Cache::remember('client-nonconsented-uknown-gender', 10, function () {
                return Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->count();
            });
            // consented clients by age distribution
            $client_consented_to_nine = Cache::remember('client-consented-to-nine', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_consented_to_fourteen = Cache::remember('tbl-client', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_consented_to_nineteen = Cache::remember('client-consented-to-nineteen', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_consented_to_twentyfour = Cache::remember('client-consented-to-twentyfour', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_consented_to_twentyfive_above = Cache::remember('client-consented-to-twentyfive-above', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_consented_uknown_age = Cache::remember('client-consented-uknown-age', 10, function () {
                return Client::select('smsenable')
                ->where('dob', '=', '')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->orWhereNull('dob')
                ->where('smsenable', '=', 'Yes')
                ->count();
            });
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Cache::remember('client-nonconsented-to-nine', 10, function () {
            return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_nonconsented_to_fourteen = Cache::remember('client-nonconsented-to-fourteen', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_nonconsented_to_nineteen = Cache::remember('client-nonconsented-to-nineteen', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_nonconsented_to_twentyfour = Cache::remember('client-nonconsented-to-twentyfour', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_nonconsented_to_twentyfive_above = Cache::remember('client-nonconsented-to-twentyfive-above', 10, function () {
                return Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');
            });
            $client_nonconsented_uknown_age = Cache::remember('client-nonconsented-uknown-age', 10, function () {
                return Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('dob', '=', '')
                ->orWhereNull('dob')
                ->where('smsenable', '!=', 'Yes')
                ->count();
            });


        }


        // dd($active_facilities);

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

    public function appointment_charts()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');
                 // main appointments
                 $appointment = Appointments::select('id')
                ->count();
                 $appointment_honoured = Cache::remember('appointment-honoured', 10, function () {
                    return Appointments::select(\DB::raw("COUNT(id) as count"))
                    ->where('date_attended', '=', DB::raw('appntmnt_date'))
                ->pluck('count');
                });
               // dd($appointment_honoured);
                $appointment_not_honoured = Cache::remember('appointment-not-honoured', 10, function () {
                    return Appointments::select(\DB::raw("COUNT(id) as count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });

                // appointment honored by gender
                $appointment_honoured_male = Cache::remember('appointment-honoured-male', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->where('tbl_client.gender', '=', '2')
                    ->pluck('count');
                });
                $appointment_honoured_female = Cache::remember('appointment-honoured-female', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->where('tbl_client.gender', '=', '1')
                    ->count();
                });
                $appointment_honoured_uknown_gender = Cache::remember('appointment-honoured-uknown-gender', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->count();
                });
                // appointment honored by age
                $appointment_honored_to_nine = Cache::remember('appointment-honored-to-nine', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->pluck('count');
                });
                $appointment_honored_to_fourteen = Cache::remember('appointment-honored-to-fourteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->pluck('count');
                });
                $appointment_honored_to_nineteen = Cache::remember('appointment-honored-to-nineteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->pluck('count');
                });
                $appointment_honored_to_twentyfour = Cache::remember('appointment-honored-to-twentyfour', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->pluck('count');
                });
                $appointment_honored_to_twentyfive_above = Cache::remember('appointment-honored-to-twentyfive_above', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->pluck('count');
                });
                $appointment_honored_to_uknown_age = Cache::remember('appointment_honored-to-uknown-age', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select('tbl_client.dob')
                    ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                    ->where('tbl_client.dob', '=', '')
                    ->orWhereNull('tbl_client.dob')
                    ->count();
                });

                // appointment not honored by gender
                $appointment_not_honoured_male = Cache::remember('appointment-not-honoured-male', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_client.gender', '=', '2')
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->count();
                });
                $appointment_not_honoured_female = Cache::remember('appointment-not-honoured-female', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_client.gender', '=', '1')
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->count();
                });
                $appointment_not_honoured_uknown_gender = Cache::remember('appointment-not-honoured-uknown-gender', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->count();
                });
                // appointment not honored by age
                $appointment_not_honored_to_nine = Cache::remember('appointment-not-honored-to-nine', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });
                $appointment_not_honored_to_fourteen = Cache::remember('appointment-not-honored-to-fourteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });
                $appointment_not_honored_to_nineteen = Cache::remember('appointment_not_honored_to_nineteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });
                $appointment_not_honored_to_twentyfour = Cache::remember('appointment_not_honored_to_twentyfour', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });
                $appointment_not_honored_to_twentyfive_above = Cache::remember('appointment_not_honored_to_twentyfive_above', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                    ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->pluck('count');
                });
                $appointment_not_honored_to_uknown_age = Cache::remember('appointment_not_honored_to_uknown_age', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select('tbl_client.dob')
                    ->where('tbl_client.dob', '=', '')
                    ->orWhereNull('tbl_client.dob')
                    ->whereIn('app_status', ['Defaulted', 'LTFU', 'Missed'])
                    ->count();
                });

                    // missed appointments

                $appointment_missed = Cache::remember('appointment_missed', 10, function () {
                        return Appointments::where('app_status', '=', 'Missed')
                    ->count();
                });
                $appointment_defaulted = Cache::remember('appointment_defaulted', 10, function () {
                    return Appointments::where('app_status', '=', 'Defaulted')
                    ->count();
                });
                $appointment_lftu = Cache::remember('appointment_lftu', 10, function () {
                    return Appointments::where('app_status', '=', 'LTFU')
                    ->count();
                });

                // missed appointment by gender
                $appointment_missed_female = Cache::remember('appointment_missed_female', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->where('tbl_client.gender', '=', '1')
                    ->count();
                });
                $appointment_missed_male = Cache::remember('appointment_missed_male', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->where('tbl_client.gender', '=', '2')
                    ->count();
                });
                    $appointment_missed_uknown_gender = Cache::remember('appointment_missed_uknown_gender', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->count();
                });
                // missed appointment by age
                $appointment_missed_to_nine = Cache::remember('appointment_missed_to_nine', 10, function () {
                return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->pluck('count');
                });
                $appointment_missed_to_fourteen = Cache::remember('appointment_missed_to_fourteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->pluck('count');
                });
                $appointment_missed_to_nineteen = Cache::remember('appointment_missed_to_nineteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->pluck('count');
                });
                $appointment_missed_to_twentyfour = Cache::remember('appointment_missed_to_twentyfour', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->pluck('count');
                });
                $appointment_missed_to_twentyfive_above = Cache::remember('appointment_missed_to_twentyfive_above', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->pluck('count');
                });
                    $appointment_missed_to_uknown_age = Cache::remember('appointment_missed_to_uknown_age', 10, function () {
                        return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select('tbl_client.dob')
                    ->where('tbl_client.dob', '=', '')
                    ->orWhereNull('tbl_client.dob')
                    ->where('tbl_appointment.app_status', '=', 'Missed')
                    ->count();
                });

                // defaulted appointment by gender
                $appointment_defaulted_female = Cache::remember('appointment_defaulted_female', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->where('tbl_client.gender', '=', '1')
                    ->count();
                });
                $appointment_defaulted_male = Cache::remember('appointment_defaulted_male', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->where('tbl_client.gender', '=', '2')
                    ->count();
                });
                    $appointment_defaulted_uknown_gender = Cache::remember('appointment_defaulted_uknown_gender', 10, function () {
                        return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->count();
                });

                // defaulted appointment by age
                $appointment_defaulted_to_nine = Cache::remember('appointment_defaulted_to_nine', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->pluck('count');
                });
                $appointment_defaulted_to_fourteen = Cache::remember('appointment_defaulted_to_fourteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->pluck('count');
                });
                $appointment_defaulted_to_nineteen = Cache::remember('appointment_defaulted_to_nineteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->pluck('count');
                });
                $appointment_defaulted_to_twentyfour = Cache::remember('appointment_defaulted_to_twentyfour', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->pluck('count');
                });
                $appointment_defaulted_to_twentyfive_above = Cache::remember('appointment_defaulted_to_twentyfive_above', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->pluck('count');
                });
                    $appointment_defaulted_to_uknown_age = Cache::remember('appointment_defaulted_to_uknown_age', 10, function () {
                        return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select('tbl_client.dob')
                    ->where('tbl_client.dob', '=', '')
                    ->orWhereNull('tbl_client.dob')
                    ->where('tbl_appointment.app_status', '=', 'Defaulted')
                    ->count();
                });
                // ltfu appointment by gender
                $appointment_ltfu_female = Cache::remember('appointment_ltfu_female', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->where('tbl_client.gender', '=', '1')
                    ->count();
                });
                $appointment_ltfu_male = Cache::remember('appointment_ltfu_male', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->where('tbl_client.gender', '=', '2')
                    ->count();
                });
                    $appointment_ltfu_uknown_gender = Cache::remember('appointment_ltfu_uknown_gender', 10, function () {
                        return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->where('tbl_client.gender', '!=', '1')
                    ->where('tbl_client.gender', '!=', '2')
                    ->count();
                });

                // ltfu appointment by age
                $appointment_ltfu_to_nine = Cache::remember('appointment_ltfu_to_nine', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->pluck('count');
                });
                $appointment_ltfu_to_fourteen = Cache::remember('appointment_ltfu_to_fourteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->pluck('count');
                });
                $appointment_ltfu_to_nineteen = Cache::remember('appointment_ltfu_to_nineteen', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->pluck('count');
                });
                $appointment_ltfu_to_twentyfour = Cache::remember('appointment_ltfu_to_twentyfour', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->pluck('count');
                });
                $appointment_ltfu_to_twentyfive_above = Cache::remember('appointment_ltfu_to_twentyfive_above', 10, function () {
                    return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->pluck('count');
                });
                    $appointment_ltfu_to_uknown_age = Cache::remember('appointment_ltfu_to_uknown_age', 10, function () {
                        return Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                    ->select('tbl_client.dob')
                    ->where('tbl_client.dob', '=', '')
                    ->orWhereNull('tbl_client.dob')
                    ->where('tbl_appointment.app_status', '=', 'LTFU')
                    ->count();
                });
        }


                return view('new_dashboard.appointment_dashboard', compact('all_partners', 'appointment', 'appointment_honoured',
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

        // $selected_counties = $request->partners;
        // $selected_counties = $request->counties;
        // $selected_subcounties = $request->subcounties;
        // $selected_facilites = $request->facilities;

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');
            $client = Client::select('id')->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $indicator = Indicator::all();
            $appointment = Appointments::select('id')
            ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

                $missed_appointment = Appointments::select('id')
                ->where('app_status', '=', 'Missed')
                ->orwhere('app_status', '=', 'LTFU')
                ->orwhere('app_status', '=', 'Defaulted')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw('COUNT(tbl_partner_facility.mfl_code) as facilities'))
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();
               // dd($active_facilities);

              //  dd($active_facilities);
            // active clients by gender
            $clients_male = Client::select('id')->where([['gender', '=', '2'], ['status', '=', 'Active'],])
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $unknown_gender = Client::where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            $client_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');


            $client_to_fourteen =  Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 14)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');


            $client_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 15) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`dob`)) >= 25)) then `id` end)) AS count"))
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_unknown_age = Client::where('dob', '=', '')
                ->orWhereNull('dob')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            // appointment by gender
            $appointment_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            $appointment_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            $appointment_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // appointment by age
            $appointment_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // Total missed appointment by gender
            $appointment_total_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            $appointment_total_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            $appointment_total_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // Total missed appointment by age
            $appointment_total_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_total_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_total_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_total_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '!=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_total_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_total_missed_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // client charts
            $client_consented = Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('smsenable', '=', 'Yes')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $client_nonconsented = Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('smsenable', '!=', 'Yes')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            // consented clients by gender

            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '2')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '1')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            $client_consented_uknown_gender = Client::where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            // non consented clients by gender
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '2')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('gender', '=', '1')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            $client_nonconsented_uknown_gender = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '!=', '1')
                ->where('gender', '!=', '2')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            // consented clients by age distribution
            $client_consented_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');

            $client_consented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_consented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_consented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->pluck('count');

            $client_consented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_consented_uknown_age = Client::select('smsenable')
                ->where('dob', '=', '')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->orWhereNull('dob')
                ->where('smsenable', '=', 'Yes')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            // non consented clients by age distribution
            $client_nonconsented_to_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');
            $client_nonconsented_to_fourteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_nonconsented_to_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');
            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');

            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('smsenable', '!=', 'Yes')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->pluck('count');
            $client_nonconsented_uknown_age = Client::select('smsenable')
                ->where('status', '=', 'Active')
                ->whereNull('hei_no')
                ->where('dob', '=', '')
                ->orWhereNull('dob')
                ->where('smsenable', '!=', 'Yes')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            // main appointments
            $appointment_honoured = Appointments::select('id')->where('appntmnt_date', '=', 'date_attended')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
            ->count();

           // dd($appointment_honoured);
            $appointment_not_honoured = Appointments::select('id')
            ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            // appointment honored by gender
            $appointment_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // appointment honored by age
            $appointment_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');

            $appointment_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.date_attended', '=', 'tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // appointment not honored by gender
            $appointment_not_honoured_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_not_honoured_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_not_honoured_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // appointment not honored by age
            $appointment_not_honored_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_not_honored_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_not_honored_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_not_honored_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_not_honored_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_not_honored_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->orwhere('tbl_appointment.app_status', '=', 'LTFU')
                ->orwhere('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
                // missed appointments

            $appointment_missed = Appointments::where('app_status', '=', 'Missed')
                    ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $appointment_defaulted = Appointments::where('app_status', '=', 'Defaulted')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();
            $appointment_lftu = Appointments::where('app_status', '=', 'LTFU')
                ->where('created_at', '>=', date($request->from))->where('created_at', '<=', date($request->to))
                ->count();

            // missed appointment by gender
            $appointment_missed_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_missed_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_missed_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // missed appointment by age
            $appointment_missed_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_missed_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_missed_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_missed_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_missed_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_missed_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // defaulted appointment by gender
            $appointment_defaulted_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_defaulted_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_defaulted_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // defaulted appointment by age
            $appointment_defaulted_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_defaulted_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_defaulted_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_defaulted_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_defaulted_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_defaulted_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            // ltfu appointment by gender
            $appointment_ltfu_female = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_ltfu_male = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
            $appointment_ltfu_uknown_gender = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.gender', '!=', '1')
                ->where('tbl_client.gender', '!=', '2')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();

            // ltfu appointment by age
            $appointment_ltfu_to_nine = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_ltfu_to_fourteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_ltfu_to_nineteen = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_ltfu_to_twentyfour = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_ltfu_to_twentyfive_above = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
            ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->pluck('count');
            $appointment_ltfu_to_uknown_age = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_client.dob')
                ->where('tbl_client.dob', '=', '')
                ->orWhereNull('tbl_client.dob')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.created_at', '>=', date($request->from))->where('tbl_appointment.created_at', '<=', date($request->to))
                ->count();
        }
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
            'client_nonconsented_uknown_age',
            'appointment_honoured',
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

}
