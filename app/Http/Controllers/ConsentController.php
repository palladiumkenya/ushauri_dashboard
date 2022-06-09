<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Consent;
use Session;

use Illuminate\Http\Request;
use Auth;
use DB;

class ConsentController extends Controller
{
    public function index()
    {

        if (Auth::user()->access_level == 'Facility') {
            $consented_clients = Client::join('tbl_groups', 'tbl_groups.id', 'tbl_client.group_id')
                ->select('tbl_client.clinic_number', 'tbl_client.id', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_client.dob', 'tbl_client.client_status', 'tbl_groups.name', 'tbl_client.created_at', 'tbl_client.smsenable', 'tbl_client.enrollment_date', 'tbl_client.art_date', 'tbl_client.updated_at', 'tbl_client.status', 'tbl_client.consent_date', 'tbl_client.txt_time')
                ->where('tbl_client.smsenable', '!=', 'Yes')
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
        }
        return view('clients.consent')->with('consented_clients', $consented_clients);
    }
    public function addconsentform()
    {
        return view('clients.addconsent');
    }
    public function client_consent(Request $request)
    {
        try {
            $request->validate([
                'consent_date' => 'required',
                'smsenable' => 'required',
                'language_id' => 'required',
                'motivational_enable' => 'required',
                'txt_time' => 'required',
                'phone_no' => 'required|regex:/(01)[0-9]{9}/'
            ]);
            $client = Client::find($request->input('id'));
            $client->consent_date = strtotime($request->input('consent_date'));
            $client->smsenable = $request->input('smsenable');
            $client->language_id = $request->input('language_id');
            $client->motivational_enable = $request->input('motivational_enable');
            $client->txt_time = date("H", strtotime($request->input('txt_time')));
            $client->phone_no = $request->input('phone_no');
            $client->save();
            //  console.log($client->save());
            if ($client) {
                Session::flash('statuscode', 'success');
                return redirect('consent/clients')->with('status', 'Client was successfully consented in the system!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not consent client please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }

    public function consent_test(Request $request)
    {
        try {

            $client = Client::where('clinic_number', $request->clinic_number)
                ->update([
                    'consent_date' => date("Y-m-d", strtotime($request->consent_date)),
                    'smsenable' => $request->smsenable,
                    'language_id' => $request->language_id,
                    'motivational_enable' => $request->motivational_enable,
                    'txt_time' => date("H", strtotime($request->txt_time)),
                    'phone_no' => $request->phone_no,

                ]);
            if ($client) {
                Session::flash('statuscode', 'success');
                return redirect('consent/clients')->with('status', 'Client was successfully consented in the system!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not consent client please try again later.');
            }
        } catch (Exception $e) {
            return back();
        }
    }
}
