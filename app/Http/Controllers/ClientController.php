<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Group;
use App\Models\Clinic;
use App\Models\Language;
use App\Models\Condition;
use App\Models\County;
use App\Models\Gender;
use App\Models\Marital;
use Session;
use Auth;

class ClientController extends Controller
{
    public function index()
    {
        $gender = Gender::all();
        $marital = Marital::all();
        $treatment = Condition::all();
        $grouping = Group::all();
        $clinics = Clinic::all();
        $county = County::where('status', '=', 'Active')->pluck('name', 'id');
        $language = Language::all()->where('status', '=', 'Active');
        return view('clients.new_client', compact('gender', 'marital', 'clinics', 'treatment', 'language', 'grouping', 'county'));
    }
    public function add_client(Request $request)
    {
        try
        {
            // $request->validate([
            //     'clinic_number' => 'required|numeric|digits:10',
            //     'f_name' => 'required',
            //     'l_name' => 'required',
            //     'dob' => 'required',
            //     'gender' => 'required',
            //     'marital' => 'required',
            //     'client_status' => 'required',
            //     'enrollment_date' => 'required',
            //     'art_date' => 'required',
            //     'language_id' => 'required',
            //     'smsenable' => 'required',
            //     'motivational_enable' => 'required',
            //     'txt_time' => 'required',
            //     'status' => 'required',
            //     'group_id' => 'required',
            // ]);
        $new_client = new Client;

       // $validate_client = Client::where('clinic_number', $request->clinic_number)
        $new_client->clinic_number = $request->clinic_number;
        $new_client->f_name = $request->first_name;
        $new_client->m_name = $request->middle_name;
        $new_client->l_name = $request->last_name;
        $new_client->dob = $request->birth;
        $new_client->gender = $request->gender;
        $new_client->marital = $request->marital;
        $new_client->client_status = $request->treatment;
        $new_client->enrollment_date = date("Y-m-d", strtotime($request->enrollment_date));
        $new_client->art_date = date("Y-m-d", strtotime($request->art_date));
        $new_client->phone_no = $request->phone;
        $new_client->language_id = $request->language;
        $new_client->smsenable = $request->smsenable;
        $new_client->motivational_enable = $request->motivational_enable;
        $new_client->txt_time = date("H", strtotime($request->txt_time));
        $new_client->status = $request->status;
        $new_client->group_id = $request->group;
        $new_client->clinic_id = $request->clinic;
        $new_client->client_type = "New";
        $new_client->mfl_code = Auth::user()->facility_id;
        $new_client->facility_id = Auth::user()->facility_id;
        $new_client->locator_county = $request->county;
        $new_client->locator_sub_county = $request->subcounty;
        $new_client->locator_ward = $request->ward;
        $new_client->locator_location = $request->location;
        $new_client->locator_village = $request->village;

        $validate_ccc = Client::where('clinic_number', $request->clinic_number)
        ->first();

        if ($validate_ccc) {
            Session::flash('statuscode', 'error');

            return redirect('add/client')->with('status', 'Clinic Number already exist in the system!');
        }
        if ($new_client->save()) {
            Session::flash('statuscode', 'success');

            return redirect('Reports/facility_home')->with('status', 'Client has been registered successfully!');
        } else {
            Session::flash('statuscode', 'error');
            return back()->with('error', 'An error has occurred please try again later.');
        }
    } catch (Exception $e) {

        return back();
    }
    }
}
