<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\Partner;
use App\Models\County;
use App\Models\SubCounty;
use Auth;

class NewDashboardController extends Controller
{
    public function dashboard()
    {

        // showing all the active clients, all appointments, missed appointments
        if (Auth::user()->access_level == 'Facility') {
            $client = Client::where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $appointment = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_appointment = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $appointment = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $missed_appointment = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client = Client::where('status', '=', 'Active')
                ->count();
            $appointment = Appointment::select('id')
                ->count();
            $missed_appointment = Appointment::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '!=', 'Booked')
                ->orwhere('tbl_appointment.app_status', '!=', 'Notified')
                ->count();
        }

        // showing all consented clients
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_consented = Client::where('smsenable', '=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_consented = Client::where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_consented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // showing all non consented clients
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_nonconsented = Client::where('smsenable', '!=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_nonconsented = Client::where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_nonconsented = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
    }

    public function client_charts()
    {

        // active clients by gender
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $clients_male = Client::where('gender', '=', '2')
                ->where('status', '=', 'Active')
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $clients_male = Client::where('gender', '=', '2')
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $clients_female = Client::where('gender', '=', '1')
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $clients_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '2')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $clients_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.gender', '=', '1')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // active clients by age distribution

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();

            $client_to_twentyfive_above = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 25) then `dob` end"))
                ->where('status', '=', 'Active')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();

            $client_to_twentyfive_above = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 25) then `dob` end"))
                ->where('status', '=', 'Active')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();

            $client_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) then `tbl_client`.`dob` end"))
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // consented clients by gender

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->count();
            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_consented_male = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_female = Client::where('smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_consented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
        // non consented clients by gender

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->count();
            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_nonconsented_male = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_female = Client::where('smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_nonconsented_male = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('gender', '=', '2')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_female = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('gender', '=', '1')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // consented clients by age distribution
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_consented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
            $client_consented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_consented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_consented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_consented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_consented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }

        // non consented clients by age distribution
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_nonconsented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->count();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_nonconsented_to_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) > 0) and ((year(curdate()) - year(`dob`)) <= 9)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 10) and ((year(curdate()) - year(`dob`)) <= 19)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_twentyfour = Client::select(\DB::raw("case when (((year(curdate()) - year(`dob`)) >= 20) and ((year(curdate()) - year(`dob`)) <= 24)) then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::select(\DB::raw("case when ((year(curdate()) - year(`dob`)) >= 25)  then `dob` end"))
                ->where('smsenable', '!=', 'Yes')
                ->where('mfl_code', Auth::user()->facility_id)
                ->count();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_nonconsented_to_nine = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_nineteen = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_twentyfour = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
            $client_nonconsented_to_twentyfive_above = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(\DB::raw("case when ((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)  then `tbl_client`.`dob` end"))
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->count();
        }
    }
}
