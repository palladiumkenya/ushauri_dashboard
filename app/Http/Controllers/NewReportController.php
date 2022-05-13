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
         $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
         ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
         ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
         ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
         ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
         ->select('tbl_client.clinic_number', 'tbl_client.f_name', 'tbl_client.m_name', 'tbl_client.l_name', 'tbl_client.dob', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.phone_no', 'tbl_gender.name as gender', 'tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county')
         ->where('tbl_client.status', '=', 'Active')
         ->whereNull('tbl_client.hei_no')
         ->paginate(100);

        return view('new_reports.clients', compact('clients'));
    }
}
