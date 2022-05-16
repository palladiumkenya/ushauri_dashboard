<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\Partner;
use App\Models\Facility;
use App\Models\Gender;
use App\Models\County;
use DB;
use Auth;

class NewReportController extends Controller
{
    public function clients_list()
    {
        if (Auth::user()->access_level == 'Facility')
        {
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
        if (Auth::user()->access_level == 'Partner')
        {
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
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor')
         {
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

            return view('new_reports.appointments', compact('appointments'));
    }
}
