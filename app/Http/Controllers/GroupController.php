<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Group;
use Session;
use Auth;

class GroupController extends Controller
{

    public function index()
    {
        $all_groups = Group::all();

        return view('groups.new_group')->with('all_groups', $all_groups);
    }
    public function addgroup(Request $request)
    {
        try{
            $group = new Group;
            $group->name = $request->name;
            $group->description = $request->description;
            $group->status = $request->status;
            $group->group_type = $request->group_type;


            // $donor->created_by = Auth::;
            if ($group->save()) {
                Session::flash('statuscode', 'success');

                return redirect('admin/groups')->with('status', 'Group has been saved successfully!');
            } else {

                Session::flash('statuscode', 'error');
                return back()->with('error', 'An error has occurred please try again later.');
            }
        }catch(Exception $e)
        {
            return back();
        }
    }
    public function get_pmtct_clients()
    {
        $all_pmtct_clients = Client::join('tbl_groups', 'tbl_groups.id', '=', 'tbl_client.group_id')
        ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.client_status, tbl_client.phone_no, tbl_client.enrollment_date, tbl_groups.name')
        ->where('tbl_client.clinic_id', '=', '2')
        ->whereNull('tbl_client.hei_no');

        if (Auth::user()->access_level == 'Facility') {
            $all_pmtct_clients->where('facility_id', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_pmtct_clients->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_pmtct_clients->where('donor_id', Auth::user()->donor_id);
        }

        return view('clients.pmtct_clients')->with('all_pmtct_clients', $all_pmtct_clients->get());
    }

    public function get_psc_clients()
    {
        $all_psc_clients = Client::join('tbl_groups', 'tbl_groups.id', '=', 'tbl_client.group_id')
        ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.client_status, tbl_client.phone_no, tbl_client.enrollment_date, tbl_groups.name')
        ->where('tbl_groups.name', '=', 'Adults');

        if (Auth::user()->access_level == 'Facility') {
            $all_psc_clients->where('facility_id', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_psc_clients->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_psc_clients->where('donor_id', Auth::user()->donor_id);
        }

        return view('clients.psc_clients')->with('all_psc_clients', $all_psc_clients->get());
    }

    public function get_adolescents_clients()
    {
        $all_adolescents_clients = Client::join('tbl_groups', 'tbl_groups.id', '=', 'tbl_client.group_id')
        ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.client_status, tbl_client.phone_no, tbl_client.enrollment_date, tbl_groups.name')
        ->where('tbl_groups.name', '=', 'Adolescents');

        if (Auth::user()->access_level == 'Facility') {
            $all_adolescents_clients->where('facility_id', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_adolescents_clients->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_adolescents_clients->where('donor_id', Auth::user()->donor_id);
        }

        return view('clients.adolescents_clients')->with('all_adolescents_clients', $all_adolescents_clients->get());
    }

    public function get_paeds_clients()
    {
        $all_paeds_clients = Client::join('tbl_groups', 'tbl_groups.id', '=', 'tbl_client.group_id')
        ->selectRaw('tbl_client.clinic_number, tbl_client.f_name, tbl_client.m_name, tbl_client.l_name, tbl_client.client_status, tbl_client.phone_no, tbl_client.enrollment_date, tbl_groups.name')
        ->where('tbl_groups.name', '=', 'Paeds');

        if (Auth::user()->access_level == 'Facility') {
            $all_paeds_clients->where('facility_id', Auth::user()->facility_id);
        }

        if (Auth::user()->access_level == 'Partner') {
            $all_paeds_clients->where('partner_id', Auth::user()->partner_id);
        }

        if (Auth::user()->access_level == 'Donor') {
            $all_paeds_clients->where('donor_id', Auth::user()->donor_id);
        }

        return view('clients.paeds_clients')->with('all_paeds_clients', $all_paeds_clients->get());
    }

}
