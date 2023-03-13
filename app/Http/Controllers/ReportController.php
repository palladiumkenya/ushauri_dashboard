<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\TodayAppointment;
use App\Models\OutcomeReport;
use App\Models\MessageExtract;
use App\Models\UserReport;
use App\Models\Summary;
use App\Models\MonthlyApp;
use App\Models\Partner;
use App\Models\Dcm;
use DB;
use Auth;
use Cache;

class ReportController extends Controller
{
    public function deactivated_clients()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_deactivated_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.status as client_type', 'tbl_groups.name', 'tbl_client.created_at')
                ->whereIn('tbl_client.status', ['Disabled', 'Deceased'])
                ->paginate(15000);
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_deactivated_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.status as client_type', 'tbl_groups.name', 'tbl_client.created_at')
                ->whereIn('tbl_client.status', ['Disabled', 'Deceased'])
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(10000);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');
            $all_deactivated_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.status as client_type', 'tbl_groups.name', 'tbl_client.created_at')
                ->whereIn('tbl_client.status', ['Disabled', 'Deceased'])
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->paginate(10000);
        }

        return view('reports.deactivated_clients', compact('all_deactivated_clients', 'all_partners'));
    }

    public function transfer_out()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_transfer_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.mfl_code')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, ' ', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                // ->where('tbl_client.prev_clinic', '=', 'tbl_master_facility.code')
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->paginate(1000);

            $all_transfer_in = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.prev_clinic')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, ' ', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                ->where('tbl_client.mfl_code', '!=', 'tbl_client.prev_clinic')
                ->paginate(1000);
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_transfer_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.mfl_code')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, ' ', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->where('tbl_client.prev_clinic', Auth::user()->facility_id)
                ->paginate(1000);

            $all_transfer_in = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.prev_clinic')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, ' ', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                ->where('tbl_client.mfl_code', '!=', 'tbl_client.prev_clinic')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1000);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');
            $all_transfer_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.mfl_code')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, ' ', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                ->where('tbl_client.status', '=', 'Transfer Out')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->paginate(1000);

            $all_transfer_in = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->join('tbl_master_facility', 'tbl_master_facility.code', '=', 'tbl_client.prev_clinic')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), DB::raw("CONCAT(`tbl_client`.`prev_clinic`, '', `tbl_master_facility`.`name`) as clinic_previous"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at')
                ->where('tbl_client.mfl_code', '!=', 'tbl_client.prev_clinic')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }


        return view('reports.transfer_out_clients', compact('all_transfer_clients', 'all_transfer_in', 'all_partners'));
    }

    public function today_appointments()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_today_appointments = TodayAppointment::select('clinic_no', 'client_name', 'file_no', 'client_phone_no', 'appointment_type', 'appntmnt_date')
                ->get();
        }

        if (Auth::user()->access_level == 'Facility') {
            $all_today_appointments = TodayAppointment::select('clinic_no', 'client_name', 'file_no', 'client_phone_no', 'appointment_type', 'appntmnt_date')
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
        }

        return view('reports.today_appointment', compact('all_today_appointments', 'all_partners'));
    }

    public function consented_report()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $consented_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.enrollment_date', 'tbl_client.art_date', 'tbl_client.updated_at', 'tbl_client.status', 'tbl_client.consent_date')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->paginate(1000);
        }

        if (Auth::user()->access_level == 'Facility') {
            $consented_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.enrollment_date', 'tbl_client.art_date', 'tbl_client.updated_at', 'tbl_client.status', 'tbl_client.consent_date')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1000);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->pluck('name', 'id');
            $consented_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.enrollment_date', 'tbl_client.art_date', 'tbl_client.updated_at', 'tbl_client.status', 'tbl_client.consent_date')
                ->where('tbl_client.smsenable', '=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }

        return view('reports.consented', compact('consented_clients', 'all_partners'));
    }

    public function tracing_outcome()
    {
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->pluck('name', 'id');
        }
        if (Auth::user()->access_level == 'Sub County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)->pluck('tbl_partner.name', 'tbl_partner.id');
        }
        if (Auth::user()->access_level == 'County') {
            $all_partners = Partner::join('tbl_partner_facility', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')->where('tbl_partner.status', '=', 'Active')->where('tbl_partner_facility.county_id', Auth::user()->county_id)->pluck('tbl_partner.name', 'tbl_partner.id');
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');
        }
        return view('reports.outcomeform', compact('all_partners'));
    }

    public function tracing_outcome_filter(Request $request)
    {
        // $selected_partners = $request->partners;
        // $selected_counties = $request->counties;
        // $selected_subcounties = $request->subcounties;
        // $selected_facilites = $request->facilities;
        $selected_from = $request->date_from;
        $selected_to = $request->date_to;
        //$outcome_report = OutcomeReport::select('*')->get();
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $outcome_report = OutcomeReport::select('*')
                ->where('Appointment_Date', '>=', date($request->date_from))
                ->where('Appointment_Date', '<=', date($request->date_to))
                ->get();
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');
        }
        if (Auth::user()->access_level == 'Partner') {

            $outcome_report = OutcomeReport::select('*')->where('partner_id', Auth::user()->partner_id)
                ->where('Appointment_Date', '>=', date($request->date_from))
                ->where('Appointment_Date', '<=', date($request->date_to))
                ->get();
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->pluck('name', 'id');
        }
        if (Auth::user()->access_level == 'Sub County') {

            $outcome_report = OutcomeReport::select('*')->where('sub_county_id', Auth::user()->subcounty_id)
                ->where('Appointment_Date', '>=', date($request->date_from))
                ->where('Appointment_Date', '<=', date($request->date_to))
                ->get();
            $all_partners = Partner::where('status', '=', 'Active')->where('id', Auth::user()->partner_id)->pluck('name', 'id');
        }
        if (Auth::user()->access_level == 'Facility') {

            $outcome_report = OutcomeReport::select('*')->where('mfl_code', Auth::user()->facility_id)
            ->where('Appointment_Date', '>=', date($request->date_from))
            ->where('Appointment_Date', '<=', date($request->date_to))
            ->get();
            $all_partners = Partner::where('status', '=', 'Active')->orderBy('name', 'ASC')->pluck('name', 'id');
        }
        // if (!empty($request->date_from)) {
        //     $outcome_report =  $outcome_report->where('Appointment_Date', '>=', date($request->date_from));
        // }
        // if (!empty($request->date_to)) {
        //     $outcome_report = $outcome_report->where('Appointment_Date', '<=', date($request->date_to));
        // }
        if (!empty($request->partners)) {
            $outcome_report = $outcome_report->where('partner_id', '=', $request->partners);
        }
        if (!empty($request->counties)) {
            $outcome_report = $outcome_report->where('county_id', $request->counties);
        }
        if (!empty($request->subcounties)) {
            $outcome_report = $outcome_report->where('sub_county_id', $request->subcounties);
        }
        if (!empty($request->facilities)) {
            $outcome_report = $outcome_report->where('MFL', $request->facilities);
        }

        // $outcome_report = $outcome_report->orderBy('created_at', 'DESC');

        // dd($outcome_report);
        return view('reports.outcome', compact('outcome_report', 'all_partners', 'selected_from', 'selected_to'));
    }

    public function messages_extract_report()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->pluck('name', 'id');

            // $message_extract = Client::chunk(100, function ($message_extract) {
            //     foreach ($message_extract as $message_extracts) {
            // $message_extract = Client::JOIN('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
            //     ->JOIN('tbl_master_facility', `tbl_master_facility` . `code`, `tbl_partner_facility` . `mfl_code`)
            //     ->JOIN('tbl_gender', `tbl_gender` . `id`, `tbl_client` . `gender`)
            //     ->JOIN('tbl_partner_facility', `tbl_partner_facility` . `mfl_code`, `tbl_client` . `mfl_code`)
            //     ->JOIN('tbl_marital_status', `tbl_marital_status` . `id`, `tbl_client` . `marital`)
            //     ->JOIN('tbl_partner', `tbl_partner` . `id`, `tbl_partner_facility` . `partner_id`)
            //   //  ->JOIN('tbl_time`, `tbl_time' . `id`, `tbl_client` . `txt_time`)
            //     ->JOIN('tbl_language', `tbl_language` . `id`, `tbl_client` . `language_id`)
            //     ->JOIN('tbl_clnt_outgoing', `tbl_clnt_outgoing` . `clnt_usr_id`, `tbl_client` . `id`)
            //     ->JOIN('tbl_message_types', `tbl_message_types` . `id`, `tbl_clnt_outgoing` . `message_type_id`)
            //     ->JOIN('tbl_county', `tbl_county` . `id`, `tbl_partner_facility` . `county_id`)
            //     ->JOIN('tbl_sub_county', `tbl_sub_county` . `id`, `tbl_partner_facility` . `sub_county_id`)
            //     ->select(
            //         'tbl_client.clinic_number as clinic_number',
            //         'tbl_client.mfl_code as mfl_code',
            //         'tbl_master_facility.name as facility_name',
            //         'tbl_gender.name as gender',
            //         'tbl_groups.name as group_name',
            //         'tbl_marital_status.marital as marital',
            //         'tbl_partner_facility.partner_id as partner_id',
            //         'tbl_partner.name as partner_name',
            //         'tbl_client.created_at as created_at',
            //         DB::raw("date_format( `tbl_client`.`created_at`, '%M %Y' ) as month_year"),
            //         // '(date_format( `tbl_client`.`created_at`, '%M %Y' ) AS month_year)',
            //         'tbl_language.name as language',
            //         'tbl_message_types.name as message_type',
            //         'tbl_clnt_outgoing.msg as msg',
            //         'tbl_client.language_id as language_id',
            //         'tbl_client.txt_time as preferred_time',
            //         'tbl_county.name as county',
            //         'tbl_sub_county.name as sub_county',
            //         'tbl_sub_county.id as sub_county_id'
            //     )->get();
            //     }
            // });

            $message_extract = MessageExtract::select(
                'clinic_number',
                'gender',
                'group_name',
                'marital',
                'preferred_time',
                'language',
                'message_type',
                'month_year',
                'msg',
                'partner_name',
                'county',
                'sub_county',
                'mfl_code',
                'facility_name'
            )->get();
        }
        if (Auth::user()->access_level == 'Facility') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->pluck('name', 'id');
            $message_extract = MessageExtract::select(
                'clinic_number',
                'gender',
                'group_name',
                'marital',
                'preferred_time',
                'language',
                'message_type',
                'month_year',
                'msg',
                'partner_name',
                'county',
                'sub_county',
                'mfl_code',
                'facility_name'
            )
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');

            $message_extract = MessageExtract::select(
                'clinic_number',
                'gender',
                'group_name',
                'marital',
                'preferred_time',
                'language',
                'message_type',
                'month_year',
                'msg',
                'partner_name',
                'county',
                'sub_county',
                'mfl_code',
                'facility_name'
            )
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
        }

        return view('reports.message_extract', compact('message_extract', 'all_partners'));
    }

    public function access_report()
    {
        $access_report = UserReport::all();

        return view('reports.user_access', compact('access_report'));
    }

    public function client_report()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $client_summary = Summary::all();
        }

        if (Auth::user()->access_level == 'Facility') {
            $client_summary = Summary::all()
                ->where('mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {

            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');
            $client_summary = Summary::all()
                ->where('partner_id', Auth::user()->partner_id);
        }

        return view('reports.client_summary', compact('client_summary', 'all_partners'));
    }

    public function monthly_appointments()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $monthly_app_summary = MonthlyApp::all();
        }

        if (Auth::user()->access_level == 'Facility') {
            $monthly_app_summary = MonthlyApp::all()
                ->where('mfl_code', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
                ->where('id', Auth::user()->partner_id)
                ->pluck('name', 'id');
            $monthly_app_summary = MonthlyApp::all()
                ->where('partner_id', Auth::user()->partner_id);
        }


        return view('reports.monthly_appointment', compact('monthly_app_summary', 'all_partners'));
    }

    public function dsd_clients()
    {
        if (Auth::user()->access_level == 'Facility') {
            $all_dsd_clients = Dcm::select('*')->groupBy('clinic_number')
                ->where('mfl_code', Auth::user()->facility_id)
                ->where('stability_status', '=', 'DCM')
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {
            $all_dsd_clients = Dcm::select('*')->groupBy('clinic_number')
                ->where('partner_id', Auth::user()->partner_id)
                ->where('stability_status', '=', 'DCM')
                ->paginate(10000);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $all_dsd_clients = Dcm::select('*')->groupBy('clinic_number')
                ->where('stability_status', '=', 'DCM')
                ->paginate(10000);
        }
        return view('reports.dcm_reports', compact('all_dsd_clients'));
    }
}
