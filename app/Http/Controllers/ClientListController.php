<?php

namespace App\Http\Controllers;
//use GuzzleHttp\Client;


use App\Models\Appointments;
use Illuminate\Http\Request;
use App\Models\ClientList;
use App\Models\Group;
use App\Models\Clinic;
use App\Models\Client;
use App\Models\Outcome;
use App\Models\Message;
use App\Models\Facility;
use App\Models\ClientReport;
use App\Models\Pmtct;
use Auth;
use DB;

class ClientListController extends Controller
{
    public function get_client_list()
    {


        $all_clients = Client::select('tbl_clinic.name', 'tbl_client.file_no', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"), 'tbl_groups.name AS group_name', 'tbl_client.dob', 'tbl_client.status', 'tbl_client.clinic_number', 'tbl_client.phone_no', 'tbl_client.created_at', 'tbl_client.enrollment_date', 'tbl_client.art_date', 'tbl_client.client_status')
            ->join('tbl_groups', 'tbl_groups.id', '=', 'tbl_client.group_id')
            ->join('tbl_clinic', 'tbl_clinic.id', '=', 'tbl_client.clinic_id')
            ->where('tbl_client.status', '=', 'Active')
            ->whereNull('tbl_client.hei_no')
            ->whereNotNull('tbl_client.clinic_number');

        if (Auth::user()->access_level == 'Facility') {
            $all_clients->where('mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_clients->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_clients->where('donor_id', Auth::user()->donor_id);
        }
        return view('clients.clients-list')->with('all_clients', $all_clients->get());
    }

    public function profile_index()
    {
        return view('clients.client_profile');
    }

    public function get_client_profile()
    {
        if (Auth::user()->access_level == 'Facility') {
            // $upn_search = $request->input('upn_search');

            $client_profile = Client::join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->leftjoin('tbl_language', 'tbl_client.language_id', '=', 'tbl_language.id')
                ->leftjoin('tbl_groups', 'tbl_client.group_id', '=', 'tbl_groups.id')
                ->leftjoin('tbl_marital_status', 'tbl_client.marital', '=', 'tbl_marital_status.id')
                ->select(
                    DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"),
                    'tbl_client.phone_no',
                    'tbl_client.art_date',
                    'tbl_client.enrollment_date',
                    'tbl_client.file_no',
                    'tbl_client.dob',
                    'tbl_client.clinic_number',
                    'tbl_client.client_status',
                    'tbl_client.status',
                    'tbl_client.smsenable',
                    'tbl_client.consent_date',
                    'tbl_gender.name as gender',
                    'tbl_groups.name as group_name',
                    'tbl_language.name as language',
                    'tbl_marital_status.marital',
                    'tbl_client.upi_no'
                )
                // ->where('tbl_client.clinic_number', 'LIKE', "%{$upn_search}%")
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);

            $data = [];

            $total_appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $future_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $kept_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $defaulted_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $ltfu_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $refill_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '1')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $clinical_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '2')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $adherence_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '3')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $lab_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '4')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $viral_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '5')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $other_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '6')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $outgoing_msg = Message::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
                ->join('tbl_message_types', 'tbl_clnt_outgoing.message_type_id', '=', 'tbl_message_types.id')
                ->select('tbl_client.clinic_number', 'tbl_message_types.name as message_type', 'tbl_clnt_outgoing.destination', 'tbl_clnt_outgoing.created_at', 'tbl_clnt_outgoing.msg')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);
            $appointment_outcome = Outcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')
                ->join('tbl_appointment', 'tbl_clnt_outcome.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->join('tbl_final_outcome', 'tbl_clnt_outcome.fnl_outcome', '=', 'tbl_final_outcome.id')
                ->join('tbl_outcome', 'tbl_clnt_outcome.outcome', '=', 'tbl_outcome.id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type', 'tbl_clnt_outcome.tracer_name', 'tbl_final_outcome.name as final_outcome', 'tbl_outcome.name as outcome')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);

            $hei_profile = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);
        }
        //dd($data);
        return view('clients.client_profile', compact(
            'client_profile',
            'total_appointments',
            'future_appointment',
            'kept_appointment',
            'missed_app',
            'defaulted_app',
            'ltfu_app',
            'refill_app',
            'clinical_app',
            'adherence_app',
            'lab_app',
            'viral_app',
            'other_app',
            'outgoing_msg',
            'appointment_outcome',
            'hei_profile'
        ));
    }

    public function profile_search(Request $request)
    {
        if (Auth::user()->access_level == 'Facility') {
            $upn_search = $request->input('upn_search');

            $client_profile = Client::join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->leftjoin('tbl_language', 'tbl_client.language_id', '=', 'tbl_language.id')
                ->leftjoin('tbl_groups', 'tbl_client.group_id', '=', 'tbl_groups.id')
                ->leftjoin('tbl_marital_status', 'tbl_client.marital', '=', 'tbl_marital_status.id')
                ->select(
                    DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as client_name"),
                    'tbl_client.phone_no',
                    'tbl_client.art_date',
                    'tbl_client.enrollment_date',
                    'tbl_client.file_no',
                    'tbl_client.dob',
                    'tbl_client.clinic_number',
                    'tbl_client.client_status',
                    'tbl_client.status',
                    'tbl_client.smsenable',
                    'tbl_client.consent_date',
                    'tbl_gender.name as gender',
                    'tbl_groups.name as group_name',
                    'tbl_language.name as language',
                    'tbl_marital_status.marital',
                    'tbl_client.upi_no'
                )
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $data = [];

            $total_appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $future_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $kept_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $defaulted_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $ltfu_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $refill_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '1')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $clinical_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '2')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $adherence_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '3')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $lab_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '4')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $viral_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '5')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $other_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '6')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $outgoing_msg = Message::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
                ->join('tbl_message_types', 'tbl_clnt_outgoing.message_type_id', '=', 'tbl_message_types.id')
                ->select('tbl_client.clinic_number', 'tbl_message_types.name as message_type', 'tbl_clnt_outgoing.destination', 'tbl_clnt_outgoing.created_at', 'tbl_clnt_outgoing.msg')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            $appointment_outcome = Outcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')
                ->join('tbl_appointment', 'tbl_clnt_outcome.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->join('tbl_final_outcome', 'tbl_clnt_outcome.fnl_outcome', '=', 'tbl_final_outcome.id')
                ->join('tbl_outcome', 'tbl_clnt_outcome.outcome', '=', 'tbl_outcome.id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type', 'tbl_clnt_outcome.tracer_name', 'tbl_final_outcome.name as final_outcome', 'tbl_outcome.name as outcome')
                ->where('tbl_client.clinic_number', 'LIKE', '%' . $upn_search . '%')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            $hei_search = $request->input('hei_search');

            $hei_profile = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->join('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_pmtct.hei_no', $hei_search)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
        }
        //dd($hei_profile);
        return view('clients.client_profile', compact(
            'client_profile',
            'total_appointments',
            'future_appointment',
            'kept_appointment',
            'missed_app',
            'defaulted_app',
            'ltfu_app',
            'refill_app',
            'clinical_app',
            'adherence_app',
            'lab_app',
            'viral_app',
            'other_app',
            'outgoing_msg',
            'appointment_outcome',
            'hei_profile'
        ));
    }
    public function get_hei_profile()
    {
        if (Auth::user()->access_level == 'Facility') {

            $total_appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $future_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $kept_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $defaulted_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $ltfu_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $refill_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '1')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $clinical_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '2')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $adherence_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '3')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $lab_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '4')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $viral_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '5')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $other_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '6')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $outgoing_msg = Message::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
                ->join('tbl_message_types', 'tbl_clnt_outgoing.message_type_id', '=', 'tbl_message_types.id')
                ->select('tbl_client.clinic_number', 'tbl_client.hei_no', 'tbl_message_types.name as message_type', 'tbl_clnt_outgoing.destination', 'tbl_clnt_outgoing.created_at', 'tbl_clnt_outgoing.msg')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);
            $appointment_outcome = Outcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')
                ->join('tbl_appointment', 'tbl_clnt_outcome.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->join('tbl_final_outcome', 'tbl_clnt_outcome.fnl_outcome', '=', 'tbl_final_outcome.id')
                ->join('tbl_outcome', 'tbl_clnt_outcome.outcome', '=', 'tbl_outcome.id')
                ->select('tbl_client.clinic_number', 'tbl_client.hei_no', 'tbl_client.file_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type', 'tbl_clnt_outcome.tracer_name', 'tbl_final_outcome.name as final_outcome', 'tbl_outcome.name as outcome')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1);

            $hei_profile = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->leftJoin('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();

            // dd($appointment_outcome);
        }
        return view('clients.hei_profile', compact(
            'hei_profile',
            'total_appointments',
            'future_appointment',
            'kept_appointment',
            'missed_app',
            'defaulted_app',
            'ltfu_app',
            'refill_app',
            'clinical_app',
            'adherence_app',
            'lab_app',
            'viral_app',
            'other_app',
            'outgoing_msg',
            'appointment_outcome'
        ));
    }

    public function profile_search_hei(Request $request)
    {
        if (Auth::user()->access_level == 'Facility') {
            $hei_search = $request->input('hei_search');

            $hei_profile = Pmtct::join('tbl_client', 'tbl_pmtct.client_id', '=', 'tbl_client.id')
                ->join('tbl_gender', 'tbl_pmtct.hei_gender', '=', 'tbl_gender.id')
                ->leftJoin('tbl_caregiver_not_on_care', 'tbl_pmtct.care_giver_id', '=', 'tbl_caregiver_not_on_care.id')
                ->select(
                    DB::raw("CONCAT(`tbl_pmtct`.`hei_first_name`, ' ', `tbl_pmtct`.`hei_middle_name`, ' ', `tbl_pmtct`.`hei_last_name`) as hei_name"),
                    'tbl_pmtct.hei_no',
                    'tbl_pmtct.hei_dob',
                    'tbl_gender.name as gender',
                    'tbl_client.clinic_number',
                    DB::raw("CONCAT(`tbl_caregiver_not_on_care`.`care_giver_fname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_mname`, ' ', `tbl_caregiver_not_on_care`.`care_giver_lname`) as caregiver_name")
                )
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            $total_appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $future_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.appntmnt_date', '>=', Now())
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $kept_appointment = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.date_attended', '=', DB::raw('tbl_appointment.appntmnt_date'))
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $missed_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $defaulted_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $ltfu_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $refill_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '1')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $clinical_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '2')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $adherence_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '3')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $lab_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '4')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $viral_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '5')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $other_app = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_appointment.id')
                ->where('tbl_appointment.app_type_1', '=', '6')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->count();
            $outgoing_msg = Message::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
                ->join('tbl_message_types', 'tbl_clnt_outgoing.message_type_id', '=', 'tbl_message_types.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.hei_no', 'tbl_message_types.name as message_type', 'tbl_clnt_outgoing.destination', 'tbl_clnt_outgoing.created_at', 'tbl_clnt_outgoing.msg')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            $appointment_outcome = Outcome::join('tbl_client', 'tbl_clnt_outcome.client_id', '=', 'tbl_client.id')
                ->join('tbl_appointment', 'tbl_clnt_outcome.appointment_id', '=', 'tbl_appointment.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->join('tbl_final_outcome', 'tbl_clnt_outcome.fnl_outcome', '=', 'tbl_final_outcome.id')
                ->join('tbl_outcome', 'tbl_clnt_outcome.outcome', '=', 'tbl_outcome.id')
                ->join('tbl_pmtct', 'tbl_client.id', '=', 'tbl_pmtct.client_id')
                ->select('tbl_client.clinic_number', 'tbl_client.hei_no', 'tbl_client.file_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name as app_type', 'tbl_clnt_outcome.tracer_name', 'tbl_final_outcome.name as final_outcome', 'tbl_outcome.name as outcome')
                ->whereNotNull('tbl_client.hei_no')
                ->where('tbl_pmtct.hei_no', 'LIKE', '%' . $hei_search . '%')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();


            // dd($hei_profile);
        }
        return view('clients.hei_profile', compact(
            'hei_profile',
            'total_appointments',
            'future_appointment',
            'kept_appointment',
            'missed_app',
            'defaulted_app',
            'ltfu_app',
            'refill_app',
            'clinical_app',
            'adherence_app',
            'lab_app',
            'viral_app',
            'other_app',
            'outgoing_msg',
            'appointment_outcome'
        ));
    }

    public function client_extract()
    {


        if (Auth::user()->access_level == 'Facility') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.consented',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->where('client_report.mfl_code', Auth::user()->facility_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->where('client_report.partner_id', Auth::user()->partner_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Donor') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')

                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->get();
            $client_extract;
        }

        if (Auth::user()->access_level == 'Admin') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->get();
        }

        return view('clients.client_extract')->with('client_extract', $client_extract);
    }

    public function filter_client_extract(Request $request)
    {


        if (Auth::user()->access_level == 'Facility') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->where('client_report.mfl_code', Auth::user()->facility_id)
                ->whereDate('client_report.created_at', '>=', date($request->from))
                ->whereDate('client_report.created_at', '<=', date($request->to))
                ->get();
        }

        if (Auth::user()->access_level == 'Partner') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->whereDate('client_report.created_at', '>=', date($request->from))
                ->whereDate('client_report.created_at', '<=', date($request->to))
                ->where('client_report.partner_id', Auth::user()->partner_id)
                ->get();
        }

        if (Auth::user()->access_level == 'Donor') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->whereDate('client_report.created_at', '>=', date($request->from))
                ->whereDate('client_report.created_at', '<=', date($request->to))
                ->get();
        }

        if (Auth::user()->access_level == 'Admin') {
            $client_extract = ClientReport::join('tbl_client', 'client_report.id', '=', 'tbl_client.id')
                ->select(
                    'tbl_client.enrollment_date',
                    'tbl_client.art_date',
                    'client_report.clinic_number',
                    'client_report.gender',
                    'client_report.group_name',
                    'client_report.marital',
                    'client_report.created_at',
                    'client_report.month_year',
                    'client_report.LANGUAGE',
                    'client_report.txt_time',
                    'client_report.partner_name',
                    'client_report.county',
                    'client_report.sub_county',
                    'client_report.mfl_code',
                    'client_report.facility_name',
                    'client_report.consented',
                    'tbl_client.wellness_enable',
                    'tbl_client.upi_no'
                )
                ->whereDate('client_report.created_at', '>=', date($request->from))
                ->whereDate('client_report.created_at', '<=', date($request->to))
                ->get();
        }

        return view('clients.client_extract')->with('client_extract', $client_extract);
    }
}
