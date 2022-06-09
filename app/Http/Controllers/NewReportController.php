<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\Partner;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\County;
use App\Models\PartnerFacility;
use App\Models\Indicator;
use DB;
use Auth;
use Carbon\Carbon;


class NewReportController extends Controller
{
    public function clients_list()
    {
        if (Auth::user()->access_level == 'Facility') {
            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Partner') {
            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->paginate(1000);
        }

        return view('new_reports.clients', compact('clients'));
    }

    public function appointment_list()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_appointment_types.name as app_type', 'tbl_appointment.appntmnt_date', 'tbl_appointment.app_status', 'tbl_appointment.created_at', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Facility') {
            $appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_appointment_types.name as app_type', 'tbl_appointment.appntmnt_date', 'tbl_appointment.app_status', 'tbl_appointment.created_at', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Partner') {
            $appointments = Appointments::join('tbl_client', 'tbl_appointment.client_id', '=', 'tbl_client.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', '=', 'tbl_appointment_types.id')
                ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_appointment_types.name as app_type', 'tbl_appointment.appntmnt_date', 'tbl_appointment.app_status', 'tbl_appointment.created_at', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }

        return view('new_reports.appointments', compact('appointments'));
    }
    public function active_facility()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->select('tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county', 'tbl_sub_county.name as subcounty')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->select('tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county', 'tbl_sub_county.name as subcounty')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();
        }
        if (Auth::user()->access_level == 'Facility') {
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->select('tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county', 'tbl_sub_county.name as subcounty')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();
        }

        return view('new_reports.active_facilities', compact('active_facilities'));
    }

    public function indicators()
    {
        $indicators = Indicator::all();

        return view('new_reports.indicators', compact('indicators'));
    }

    public function client_message()
    {
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
                ->join('tbl_clnt_outgoing', 'tbl_client.id', 'tbl_clnt_outgoing.clnt_usr_id')
                ->join('tbl_gender', 'tbl_client.gender', 'tbl_gender.id')
                ->join('tbl_language', 'tbl_client.language_id', 'tbl_language.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', 'tbl_appointment_types.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', 'tbl_sub_county.id')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_gender.name as gender',
                    'tbl_language.name as language',
                    'tbl_client.phone_no',
                    'tbl_clnt_outgoing.msg',
                    'tbl_clnt_outgoing.callback_status',
                    'tbl_clnt_outgoing.failure_reason',
                    'tbl_clnt_outgoing.updated_at',
                    'tbl_appointment.appntmnt_date as appointment_date',
                    'tbl_appointment_types.name as app_type',
                    'tbl_appointment.app_status',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_sub_county.name as subcounty'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_clnt_outgoing.created_at', '>=', '2022-02-01')
                ->where('tbl_appointment.appntmnt_date', '>=', '2022-1-20')
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
                ->join('tbl_clnt_outgoing', 'tbl_client.id', 'tbl_clnt_outgoing.clnt_usr_id')
                ->join('tbl_gender', 'tbl_client.gender', 'tbl_gender.id')
                ->join('tbl_language', 'tbl_client.language_id', 'tbl_language.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', 'tbl_appointment_types.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', 'tbl_sub_county.id')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_gender.name as gender',
                    'tbl_language.name as language',
                    'tbl_client.phone_no',
                    'tbl_clnt_outgoing.msg',
                    'tbl_clnt_outgoing.callback_status',
                    'tbl_clnt_outgoing.failure_reason',
                    'tbl_clnt_outgoing.updated_at',
                    'tbl_appointment.appntmnt_date as appointment_date',
                    'tbl_appointment_types.name as app_type',
                    'tbl_appointment.app_status',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_sub_county.name as subcounty'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_clnt_outgoing.created_at', '>=', '2022-02-01')
                ->where('tbl_appointment.appntmnt_date', '>=', '2022-1-20')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
                ->join('tbl_clnt_outgoing', 'tbl_client.id', 'tbl_clnt_outgoing.clnt_usr_id')
                ->join('tbl_gender', 'tbl_client.gender', 'tbl_gender.id')
                ->join('tbl_language', 'tbl_client.language_id', 'tbl_language.id')
                ->join('tbl_appointment_types', 'tbl_appointment.app_type_1', 'tbl_appointment_types.id')
                ->join('tbl_partner_facility', 'tbl_client.mfl_code', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', 'tbl_sub_county.id')
                ->select(
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_gender.name as gender',
                    'tbl_language.name as language',
                    'tbl_client.phone_no',
                    'tbl_clnt_outgoing.msg',
                    'tbl_clnt_outgoing.callback_status',
                    'tbl_clnt_outgoing.failure_reason',
                    'tbl_clnt_outgoing.updated_at',
                    'tbl_appointment.appntmnt_date as appointment_date',
                    'tbl_appointment_types.name as app_type',
                    'tbl_appointment.app_status',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_sub_county.name as subcounty'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->where('tbl_clnt_outgoing.created_at', '>=', '2022-02-01')
                ->where('tbl_appointment.appntmnt_date', '>=', '2022-1-20')
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->paginate(1000);
        }


        return view('new_reports.client_messages', compact('client_messages'));
    }
}
