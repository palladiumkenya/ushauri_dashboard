<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use\App\Models\Pmtct;
use\App\Models\Client;
use\App\Models\Partner;
use\App\Models\Appointments;

use Carbon\Carbon;
use Auth;
use DB;

class PmtcController extends Controller
{

    public function get_pmtct_honored_appointment()
    {
        $all_honored_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            //->innerJoin('tbl_appointment_types')->ON('tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
            ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
            ->where('appointment_kept', '=', 'Yes');

        return view('pmtct/kept_appointments')->with('all_honored_appointment_clients', $all_honored_appointment_clients->get());
    }

    public function pmtct_appointment_dairy()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_booked_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->get();

            $all_schedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNull('tbl_client.hei_no')
                ->get();

            $all_unschedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereNull('tbl_client.hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_booked_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                // ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $all_schedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $all_unschedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                // ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_booked_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();

            $all_schedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();

            $all_unschedule_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->selectRaw('tbl_client.clinic_number, tbl_appointment_types.name as app_type, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();
        }

        return view('pmtct/pmtct_appointment_dairy', compact('all_booked_pmtct_clients', 'all_schedule_appointment_clients', 'all_unschedule_appointment_clients'));
    }

    public function pmtct_defaulter_dairy()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_missed_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Missed')
                ->get();

            $all_defaulted_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Defaulted')
                ->get();

            $all_ltfu_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'LTFU')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_missed_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $all_defaulted_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $all_ltfu_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_missed_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();

            $all_defaulted_appointment_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();

            $all_ltfu_pmtct_clients = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_appointment.appntmnt_date')
                ->where('app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->get();
        }

        return view('pmtct/pmtct_defaulter_dairy', compact('all_missed_appointment_clients', 'all_defaulted_appointment_clients', 'all_ltfu_pmtct_clients'));
    }

    public function get_all_hei()
    {


        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_hei = Pmtct::join('tbl_client', 'tbl_client.hei_no', '=', 'tbl_pmtct.hei_no')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_pmtct.hei_first_name as f_name',
                    'tbl_pmtct.hei_middle_name as m_name',
                    'tbl_pmtct.hei_last_name as l_name',
                    'tbl_pmtct.hei_dob as dob',
                    'tbl_gender.name as gender',
                    'tbl_pmtct.hei_no',
                    'tbl_client.status'
                )
                ->whereNotNull('tbl_pmtct.hei_no')->paginate(1000);
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_hei = Pmtct::join('tbl_client', 'tbl_client.hei_no', '=', 'tbl_pmtct.hei_no')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_pmtct.hei_first_name as f_name',
                    'tbl_pmtct.hei_middle_name as m_name',
                    'tbl_pmtct.hei_last_name as l_name',
                    'tbl_pmtct.hei_dob as dob',
                    'tbl_gender.name as gender',
                    'tbl_pmtct.hei_no',
                    'tbl_client.status'
                )
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_pmtct.hei_no')
                ->paginate(10000);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_hei = Pmtct::join('tbl_client', 'tbl_client.hei_no', '=', 'tbl_pmtct.hei_no')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_pmtct.hei_first_name as f_name',
                    'tbl_pmtct.hei_middle_name as m_name',
                    'tbl_pmtct.hei_last_name as l_name',
                    'tbl_pmtct.hei_dob as dob',
                    'tbl_gender.name as gender',
                    'tbl_pmtct.hei_no',
                    'tbl_client.status'
                )
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_pmtct.hei_no')->paginate(10000);
        }

        return view('pmtct/all_heis', compact('all_hei'));
    }
    public function hei_appointment_dairy()
    {

        if (Auth::user()->access_level == 'Admin') {
            $hei_appointment = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->leftJoin('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    'tbl_appointment.appntmnt_date as app_date',
                    'tbl_appointment.app_status',
                    'tbl_appointment_types.name as app_type',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->paginate(1000);

            $all_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $hei_appointment = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->leftJoin('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    'tbl_appointment.appntmnt_date as app_date',
                    'tbl_appointment.app_status',
                    'tbl_appointment_types.name as app_type',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $hei_appointment = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->leftJoin('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    'tbl_appointment.appntmnt_date as app_date',
                    'tbl_appointment.app_status',
                    'tbl_appointment_types.name as app_type',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->paginate(1000);

            $all_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.visit_type')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        return view('pmtct/hei_appointment_dairy', compact('all_unscheduled_heis', 'hei_appointment', 'all_scheduled_heis'));
    }

    public function hei_defaulter_dairy()
    {

        if (Auth::user()->access_level == 'Admin') {
            $all_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();

            $all_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no, tbl_appointment.appntmnt_date, tbl_appointment.app_type_1, tbl_appointment.app_status')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->get();
        }

        return view('pmtct/hei_defaulter_dairy', compact('all_missed_heis', 'all_defaulted_heis', 'all_ltfu_heis'));
    }

    public function hei_final_outcome()
    {
        if (Auth::user()->access_level == 'Admin') {
            $all_deceased_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Deceased')
                ->whereNotNull('hei_no')
                ->get();

            $all_transfer_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Transfer Out')
                ->whereNotNull('hei_no')
                ->get();

            $all_discharged_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Disabled')
                ->whereNotNull('hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_deceased_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Deceased')
                ->where('mfl_code', Auth::user()->facility_id)
                ->whereNotNull('hei_no')
                ->get();

            $all_transfer_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Transfer Out')
                ->where('mfl_code', Auth::user()->facility_id)
                ->whereNotNull('hei_no')
                ->get();

            $all_discharged_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Disabled')
                ->where('mfl_code', Auth::user()->facility_id)
                ->whereNotNull('hei_no')
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_deceased_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Deceased')
                ->where('partner_id', Auth::user()->partner_id)
                ->whereNotNull('hei_no')
                ->get();

            $all_transfer_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Transfer Out')
                ->where('partner_id', Auth::user()->partner_id)
                ->whereNotNull('hei_no')
                ->get();

            $all_discharged_heis = Client::selectRaw('clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.hei_no, tbl_client.phone_no')
                ->where('status', '=', 'Disabled')
                ->where('partner_id', Auth::user()->partner_id)
                ->whereNotNull('hei_no')
                ->get();
        }

        return view('pmtct/hei_final_outcome', compact('all_deceased_heis', 'all_transfer_heis', 'all_discharged_heis'));
    }


    public function pmtct_dashboard()
    {

        if (Auth::user()->access_level == 'Facility') {
            $ranges = [

                'ToNine' => 0,
                'ToFourteen' => 10,
                'ToNineteen' => 15,
                'ToTwentyFour' => 20,
                'ToTwentyNine' => 25,
                'ToThirtyFour' => 30,
                'ToThirtyNine' => 35,
                'ToFortyFour' => 40,
                'ToFortyNine' => 45,
                'FiftyPlus' => 50
            ];

            $startdate = Appointments::select('appntmnt_date')->orderBy('appntmnt_date', 'asc')->first();
            $startdate = Carbon::parse($startdate->appntmnt_date)->format('Y-m-d');
            $enddate  = Appointments::select('appntmnt_date')->orderBy('appntmnt_date', 'desc')->first();
            $enddate = Carbon::parse($enddate->appntmnt_date)->format('Y-m-d');

            $tonine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                //->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofiftyplus_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                //->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // Un-Scheduled
            $tonine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                //->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                //->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                // ->whereDate('tbl_appointment.appntmnt_date', '>=', $startdate)->whereDate('tbl_appointment.appntmnt_date', '<=', $enddate)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            //Booked
            $tonine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // Defaulter

            $tonine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // Missed
            $tonine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // LTFU
            $tonine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // honoured appointments
            $tonine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonineteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $totwentynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tothirtynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofortynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofifty_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');
        }

        // partner level

        if (Auth::user()->access_level == 'Partner') {

            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');

            $tonine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofiftyplus_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // Un-Scheduled
            $tonine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            //Booked
            $tonine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // Defaulter

            $tonine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // Missed
            $tonine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // LTFU
            $tonine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // honoured appointments
            $tonine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonineteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $totwentynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tothirtynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofortynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofifty_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');
        }

        // Administrator

        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {


            $tonine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofourteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tonineteen_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $totwentyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $totwentynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //  ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tothirtyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tothirtynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofortyfour_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofortynine_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofiftyplus_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tototal_scheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            // Un-Scheduled
            $tonine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofourteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tonineteen_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $totwentyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $totwentynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tothirtyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tothirtynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofortyfour_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofortynine_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tofifty_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            $tototal_unscheduled = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->pluck('count');

            //Booked
            $tonine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tofourteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tonineteen_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $totwentyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $totwentynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tothirtyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tothirtynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tofortyfour_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tofortynine_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tofifty_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            $tototal_booked = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.appntmnt_date', '>', Now())
                ->pluck('count');

            // Defaulter

            $tonine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tofourteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tonineteen_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $totwentyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $totwentynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tothirtyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tothirtynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tofortyfour_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tofortynine_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tofifty_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            $tototal_defaulted = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->pluck('count');

            // Missed
            $tonine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tofourteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tonineteen_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $totwentyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $totwentynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tothirtyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tothirtynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tofortyfour_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tofortynine_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tofifty_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            $tototal_missed = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->pluck('count');

            // LTFU
            $tonine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tofourteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tonineteen_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $totwentyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $totwentynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tothirtyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tothirtynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tofortyfour_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tofortynine_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tofifty_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            $tototal_ltfu = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->pluck('count');

            // honoured appointments
            $tonine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tofourteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tonineteen_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $totwentyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $totwentynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 29)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tothirtyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 30) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 34)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tothirtynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 35) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 39)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tofortyfour_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 40) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 44)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tofortynine_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 45) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 49)) then `tbl_client`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tofifty_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 50) and ((year(curdate()) - year(`tbl_client`.`dob`)) >= 50)) then `tbl_pmtct`.`id` end)) AS count"))
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');

            $tototal_honoured = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNull('tbl_client.hei_no')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) > 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) >=10)) then `tbl_client`.`id` end)) AS count"))
                ->where('tbl_appointment.appointment_kept', '=', 'Yes')
                ->pluck('count');
        }

        return view('pmtct/pmtct_dashboard', compact(
            'all_partners',
            'tonine_scheduled',
            'tofourteen_scheduled',
            'tonineteen_scheduled',
            'totwentyfour_scheduled',
            'totwentynine_scheduled',
            'tothirtyfour_scheduled',
            'tothirtynine_scheduled',
            'tofortyfour_scheduled',
            'tofortynine_scheduled',
            'tofiftyplus_scheduled',
            'tototal_scheduled',
            'tonine_unscheduled',
            'tofourteen_unscheduled',
            'tonineteen_unscheduled',
            'totwentyfour_unscheduled',
            'totwentynine_unscheduled',
            'tothirtyfour_unscheduled',
            'tothirtynine_unscheduled',
            'tofortyfour_unscheduled',
            'tofortynine_unscheduled',
            'tofifty_unscheduled',
            'tototal_unscheduled',
            'tonine_booked',
            'tofourteen_booked',
            'tonineteen_booked',
            'totwentyfour_booked',
            'totwentynine_booked',
            'tothirtyfour_booked',
            'tothirtynine_booked',
            'tofortyfour_booked',
            'tofortynine_booked',
            'tofifty_booked',
            'tototal_booked',
            'tonine_defaulted',
            'tofourteen_defaulted',
            'tonineteen_defaulted',
            'totwentyfour_defaulted',
            'totwentynine_defaulted',
            'tothirtyfour_defaulted',
            'tothirtynine_defaulted',
            'tofortyfour_defaulted',
            'tofortynine_defaulted',
            'tofifty_defaulted',
            'tototal_defaulted',
            'tonine_missed',
            'tofourteen_missed',
            'tonineteen_missed',
            'totwentyfour_missed',
            'totwentynine_missed',
            'tothirtyfour_missed',
            'tothirtynine_missed',
            'tofortyfour_missed',
            'tofortynine_missed',
            'tofifty_missed',
            'tototal_missed',
            'tonine_ltfu',
            'tofourteen_ltfu',
            'tonineteen_ltfu',
            'totwentyfour_ltfu',
            'totwentynine_ltfu',
            'tothirtyfour_ltfu',
            'tothirtynine_ltfu',
            'tofortyfour_ltfu',
            'tofortynine_ltfu',
            'tofifty_ltfu',
            'tototal_ltfu',
            'tonine_honoured',
            'tofourteen_honoured',
            'tonineteen_honoured',
            'totwentyfour_honoured',
            'totwentynine_honoured',
            'tothirtyfour_honoured',
            'tothirtynine_honoured',
            'tofortyfour_honoured',
            'tofortynine_honoured',
            'tofifty_honoured',
            'tototal_honoured'
        ));
    }

    public function hei_dashboard()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $toone_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofour_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $toone_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');


            $tofour_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                //->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');


            $toone_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofour_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->where('tbl_appointment.appntmnt_date', '<', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $toone_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofour_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $toone_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofour_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $toone_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofour_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tonine_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tofourteen_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            $tototal_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');

            // non-positive hei
            $count_booked_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_deceased_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Deceased')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_transfer_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_discharged_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Disabled')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_pcr_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select(\DB::raw("COUNT(tbl_pmtct.hei_no) as count"))
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->pluck('count');
        }

        if (Auth::user()->access_level == 'Facility') {
            $toone_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $toone_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');


            $toone_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $toone_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $toone_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $toone_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofour_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tonine_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tofourteen_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $tototal_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            // non-positive hei
            $count_booked_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_deceased_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Deceased')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_transfer_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_discharged_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Disabled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');

            $count_pcr_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count');
        }

        if (Auth::user()->access_level == 'Partner') {

            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');
            $toone_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $toone_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');


            $toone_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $toone_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $toone_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $toone_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofour_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tonine_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tofourteen_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $tototal_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end)) AS count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            // non-positive hei
            $count_booked_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Booked')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->whereNotNull('tbl_client.hei_no')
                ->pluck('count');

            $count_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_deceased_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Deceased')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_transfer_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_discharged_heis = Client::select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_client.status', '=', 'Disabled')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');

            $count_pcr_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select(\DB::raw("COUNT(tbl_client.id) as count"))
                ->whereNotNull('tbl_pmtct.date_confirmed_positive')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->pluck('count');
        }

        return view('pmtct/hei_dashboard', compact(
            'all_partners',
            'toone_booked_heis',
            'tofour_booked_heis',
            'tonine_booked_heis',
            'tofourteen_booked_heis',
            'toone_scheduled_heis',
            'tofour_scheduled_heis',
            'tonine_scheduled_heis',
            'tofourteen_scheduled_heis',
            'toone_unscheduled_heis',
            'tofour_unscheduled_heis',
            'tonine_unscheduled_heis',
            'tofourteen_unscheduled_heis',
            'toone_missed_heis',
            'tofour_missed_heis',
            'tonine_missed_heis',
            'tofourteen_missed_heis',
            'toone_defaulted_heis',
            'tofour_defaulted_heis',
            'tonine_defaulted_heis',
            'tofourteen_defaulted_heis',
            'toone_ltfu_heis',
            'tofour_ltfu_heis',
            'tonine_ltfu_heis',
            'tofourteen_ltfu_heis',
            'tototal_booked_heis',
            'tototal_scheduled_heis',
            'tototal_unscheduled_heis',
            'tototal_missed_heis',
            'tototal_defaulted_heis',
            'tototal_ltfu_heis',
            'count_booked_heis',
            'count_scheduled_heis',
            'count_unscheduled_heis',
            'count_deceased_heis',
            'count_transfer_heis',
            'count_discharged_heis',
            'count_missed_heis',
            'count_defaulted_heis',
            'count_ltfu_heis',
            'count_pcr_heis'
        ));
    }

    public function filter_hei_dashboard(Request $request)
    {
        $data                = [];

        $selected_counties = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        $toone_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_booked_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $toone_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            //->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            //->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_scheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            // ->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');


        $toone_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_unscheduled_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->where('tbl_appointment.appntmnt_date', '<', Now())
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $toone_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_missed_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $toone_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_defaulted_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $toone_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) < 1)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofour_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 1) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 4)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tonine_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 5) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 9)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tofourteen_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) >= 10) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        $tototal_ltfu_heis = Pmtct::join('tbl_client', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("case when (((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) > 0) and ((year(curdate()) - year(`tbl_pmtct`.`hei_dob`)) <= 14)) then `tbl_pmtct`.`id` end"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no')
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        // non-positive hei
        $count_booked_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_appointment.id"))
            ->where('tbl_appointment.app_status', '=', 'Booked')
            ->where('tbl_appointment.active_app', '=', 1)
            ->whereNotNull('tbl_client.hei_no');

        $count_scheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_appointment.id"))
            ->where('tbl_appointment.visit_type', '=', 'Scheduled')
            ->whereNotNull('tbl_client.hei_no');

        $count_unscheduled_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_appointment.id"))
            ->where('tbl_appointment.visit_type', '=', 'Un-Scheduled')
            ->whereNotNull('tbl_client.hei_no');

        $count_deceased_heis = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_client.status', '=', 'Deceased')
            ->whereNotNull('tbl_client.hei_no');

        $count_transfer_heis = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_client.status', '=', 'Transfer Out')
            ->whereNotNull('tbl_client.hei_no');

        $count_discharged_heis = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_client.status', '=', 'Disabled')
            ->whereNotNull('tbl_client.hei_no');

        $count_missed_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_appointment.app_status', '=', 'Missed')
            ->whereNotNull('tbl_client.hei_no');

        $count_defaulted_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_appointment.app_status', '=', 'Defaulted')
            ->whereNotNull('tbl_client.hei_no');

        $count_ltfu_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->where('tbl_appointment.app_status', '=', 'LTFU')
            ->whereNotNull('tbl_client.hei_no');

        $count_pcr_heis = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("tbl_client.id"))
            ->whereNotNull('tbl_pmtct.date_confirmed_positive');

        if (!empty($selected_partners)) {
            $toone_booked_heis = $toone_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_booked_heis = $tofour_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_booked_heis = $tonine_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_booked_heis = $tofourteen_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_booked_heis = $tototal_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $toone_scheduled_heis = $toone_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_scheduled_heis = $tofour_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_scheduled_heis = $tonine_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_scheduled_heis = $tofourteen_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_scheduled_heis = $tototal_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $toone_unscheduled_heis = $toone_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_unscheduled_heis = $tofour_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_unscheduled_heis = $tonine_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_unscheduled_heis = $tofourteen_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_unscheduled_heis = $tototal_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $toone_missed_heis = $toone_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_missed_heis = $tofour_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_missed_heis = $tonine_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_missed_heis = $tofourteen_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_missed_heis = $tototal_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $toone_defaulted_heis = $toone_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_defaulted_heis = $tofour_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_defaulted_heis = $tonine_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_defaulted_heis = $tofourteen_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_defaulted_heis = $tototal_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $toone_ltfu_heis = $toone_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofour_ltfu_heis = $tofour_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tonine_ltfu_heis = $tonine_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tofourteen_ltfu_heis = $tofourteen_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $tototal_ltfu_heis = $tototal_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_booked_heis = $count_booked_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_scheduled_heis = $count_scheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_unscheduled_heis = $count_unscheduled_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_deceased_heis = $count_deceased_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_transfer_heis = $count_transfer_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_discharged_heis = $count_discharged_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_missed_heis = $count_missed_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_defaulted_heis = $count_defaulted_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_ltfu_heis = $count_ltfu_heis->where('tbl_partner_facility.partner_id', $selected_partners);
            $count_pcr_heis = $count_pcr_heis->where('tbl_partner_facility.partner_id', $selected_partners);
        }

        if (!empty($selected_counties)) {
            $toone_booked_heis = $toone_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_booked_heis = $tofour_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_booked_heis = $tonine_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_booked_heis = $tofourteen_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_booked_heis = $tototal_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $toone_scheduled_heis = $toone_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_scheduled_heis = $tofour_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_scheduled_heis = $tonine_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_scheduled_heis = $tofourteen_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_scheduled_heis = $tototal_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $toone_unscheduled_heis = $toone_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_unscheduled_heis = $tofour_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_unscheduled_heis = $tonine_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_unscheduled_heis = $tofourteen_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_unscheduled_heis = $tototal_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $toone_missed_heis = $toone_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_missed_heis = $tofour_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_missed_heis = $tonine_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_missed_heis = $tofourteen_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_missed_heis = $tototal_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $toone_defaulted_heis = $toone_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_defaulted_heis = $tofour_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_defaulted_heis = $tonine_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_defaulted_heis = $tofourteen_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_defaulted_heis = $tototal_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $toone_ltfu_heis = $toone_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofour_ltfu_heis = $tofour_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tonine_ltfu_heis = $tonine_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tofourteen_ltfu_heis = $tofourteen_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $tototal_ltfu_heis = $tototal_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_booked_heis = $count_booked_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_scheduled_heis = $count_scheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_unscheduled_heis = $count_unscheduled_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_deceased_heis = $count_deceased_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_transfer_heis = $count_transfer_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_discharged_heis = $count_discharged_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_missed_heis = $count_missed_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_defaulted_heis = $count_defaulted_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_ltfu_heis = $count_ltfu_heis->where('tbl_partner_facility.county_id', $selected_counties);
            $count_pcr_heis = $count_pcr_heis->where('tbl_partner_facility.county_id', $selected_counties);
        }


        if (!empty($selected_subcounties)) {
            $toone_booked_heis = $toone_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_booked_heis = $tofour_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_booked_heis = $tonine_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_booked_heis = $tofourteen_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_booked_heis = $tototal_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $toone_scheduled_heis = $toone_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_scheduled_heis = $tofour_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_scheduled_heis = $tonine_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_scheduled_heis = $tofourteen_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_scheduled_heis = $tototal_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $toone_unscheduled_heis = $toone_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_unscheduled_heis = $tofour_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_unscheduled_heis = $tonine_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_unscheduled_heis = $tofourteen_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_unscheduled_heis = $tototal_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $toone_missed_heis = $toone_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_missed_heis = $tofour_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_missed_heis = $tonine_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_missed_heis = $tofourteen_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_missed_heis = $tototal_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $toone_defaulted_heis = $toone_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_defaulted_heis = $tofour_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_defaulted_heis = $tonine_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_defaulted_heis = $tofourteen_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_defaulted_heis = $tototal_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $toone_ltfu_heis = $toone_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofour_ltfu_heis = $tofour_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tonine_ltfu_heis = $tonine_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tofourteen_ltfu_heis = $tofourteen_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $tototal_ltfu_heis = $tototal_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_booked_heis = $count_booked_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_scheduled_heis = $count_scheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_unscheduled_heis = $count_unscheduled_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_deceased_heis = $count_deceased_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_transfer_heis = $count_transfer_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_discharged_heis = $count_discharged_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_missed_heis = $count_missed_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_defaulted_heis = $count_defaulted_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_ltfu_heis = $count_ltfu_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $count_pcr_heis = $count_pcr_heis->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }

        if (!empty($selected_facilites)) {
            $toone_booked_heis = $toone_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_booked_heis = $tofour_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_booked_heis = $tonine_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_booked_heis = $tofourteen_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_booked_heis = $tototal_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $toone_scheduled_heis = $toone_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_scheduled_heis = $tofour_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_scheduled_heis = $tonine_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_scheduled_heis = $tofourteen_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_scheduled_heis = $tototal_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $toone_unscheduled_heis = $toone_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_unscheduled_heis = $tofour_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_unscheduled_heis = $tonine_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_unscheduled_heis = $tofourteen_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_unscheduled_heis = $tototal_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $toone_missed_heis = $toone_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_missed_heis = $tofour_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_missed_heis = $tonine_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_missed_heis = $tofourteen_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_missed_heis = $tototal_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $toone_defaulted_heis = $toone_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_defaulted_heis = $tofour_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_defaulted_heis = $tonine_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_defaulted_heis = $tofourteen_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_defaulted_heis = $tototal_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $toone_ltfu_heis = $toone_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofour_ltfu_heis = $tofour_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tonine_ltfu_heis = $tonine_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tofourteen_ltfu_heis = $tofourteen_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $tototal_ltfu_heis = $tototal_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_booked_heis = $count_booked_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_scheduled_heis = $count_scheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_unscheduled_heis = $count_unscheduled_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_deceased_heis = $count_deceased_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_transfer_heis = $count_transfer_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_discharged_heis = $count_discharged_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_missed_heis = $count_missed_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_defaulted_heis = $count_defaulted_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_ltfu_heis = $count_ltfu_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $count_pcr_heis = $count_pcr_heis->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }

        $data["toone_booked_heis"]        = $toone_booked_heis->count();
        $data["tofour_booked_heis"]        = $tofour_booked_heis->count();
        $data["tonine_booked_heis"]        = $tonine_booked_heis->count();
        $data["tofourteen_booked_heis"]        = $tofourteen_booked_heis->count();
        $data["tototal_booked_heis"]        = $tototal_booked_heis->count();
        $data["toone_scheduled_heis"]        = $toone_scheduled_heis->count();
        $data["tofour_scheduled_heis"]        = $tofour_scheduled_heis->count();
        $data["tonine_scheduled_heis"]        = $tonine_scheduled_heis->count();
        $data["tofourteen_scheduled_heis"]        = $tofourteen_scheduled_heis->count();
        $data["tototal_scheduled_heis"]        = $tototal_scheduled_heis->count();
        $data["toone_unscheduled_heis"]        = $toone_unscheduled_heis->count();
        $data["tofour_unscheduled_heis"]        = $tofour_unscheduled_heis->count();
        $data["tonine_unscheduled_heis"]        = $tonine_unscheduled_heis->count();
        $data["tofourteen_unscheduled_heis"]        = $tofourteen_unscheduled_heis->count();
        $data["tototal_unscheduled_heis"]        = $tototal_unscheduled_heis->count();
        $data["toone_missed_heis"]        = $toone_missed_heis->count();
        $data["tofour_missed_heis"]        = $tofour_missed_heis->count();
        $data["tonine_missed_heis"]        = $tonine_missed_heis->count();
        $data["tofourteen_missed_heis"]        = $tofourteen_missed_heis->count();
        $data["tototal_missed_heis"]        = $tototal_missed_heis->count();
        $data["toone_defaulted_heis"]        = $toone_defaulted_heis->count();
        $data["tofour_defaulted_heis"]        = $tofour_defaulted_heis->count();
        $data["tonine_defaulted_heis"]        = $tonine_defaulted_heis->count();
        $data["tofourteen_defaulted_heis"]        = $tofourteen_defaulted_heis->count();
        $data["tototal_defaulted_heis"]        = $tototal_defaulted_heis->count();
        $data["toone_ltfu_heis"]        = $toone_ltfu_heis->count();
        $data["tofour_ltfu_heis"]        = $tofour_ltfu_heis->count();
        $data["tonine_ltfu_heis"]        = $tonine_ltfu_heis->count();
        $data["tofourteen_ltfu_heis"]        = $tofourteen_ltfu_heis->count();
        $data["tototal_ltfu_heis"]        = $tototal_ltfu_heis->count();
        $data["count_booked_heis"]        = $count_booked_heis->count();
        $data["count_scheduled_heis"]        = $count_scheduled_heis->count();
        $data["count_unscheduled_heis"]        = $count_unscheduled_heis->count();
        $data["count_deceased_heis"]        = $count_deceased_heis->count();
        $data["count_transfer_heis"]        = $count_transfer_heis->count();
        $data["count_discharged_heis"]        = $count_discharged_heis->count();
        $data["count_missed_heis"]        = $count_missed_heis->count();
        $data["count_defaulted_heis"]        = $count_defaulted_heis->count();
        $data["count_ltfu_heis"]        = $count_ltfu_heis->count();
        $data["count_pcr_heis"]        = $count_pcr_heis->count();

        return $data;
    }

    public function pmtct_enrollment()
    {
       $pmtct_anc = DB::table('tbl_pmtct_anc')->join('tbl_client', 'tbl_pmtct_anc.client_id','=', 'tbl_client.id')
       ->select('tbl_client.f_name', 'tbl_client.l_name', 'tbl_client.clinic_number', 'tbl_pmtct_anc.visit_number')
       ->get();
       
    }
}
