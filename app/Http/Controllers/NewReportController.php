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
use App\Models\Group;
use App\Models\Clinic;
use App\Models\Time;
use App\Models\Language;
use App\Models\Condition;
use App\Models\Marital;
use DB;
use Auth;
use Carbon\Carbon;
use Exception;
use Session;


class NewReportController extends Controller
{
    public function clients_list()
    {
        if (Auth::user()->access_level == 'Facility') {
            $gender = Gender::all();
            $marital = Marital::all();
            $treatment = Condition::all();
            $grouping = Group::all()->where('status', '=', 'Active');
            $clinics = Clinic::all();
            $time = Time::all();
            $county = County::where('status', '=', 'Active')->pluck('name', 'id');
            $language = Language::all()->where('status', '=', 'Active');

            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_groups', 'tbl_client.group_id', '=', 'tbl_groups.id')
                ->join('tbl_language', 'tbl_client.language_id', '=', 'tbl_language.id')
                ->join('tbl_condition', 'tbl_client.client_status', '=', 'tbl_condition.name')
                ->join('tbl_marital_status', 'tbl_client.marital', '=', 'tbl_marital_status.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select(
                    'tbl_client.id',
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_client.dob',
                    'tbl_client.created_at',
                    'tbl_client.smsenable',
                    DB::raw('DATE(tbl_client.enrollment_date) AS enrollment_date'),
                    DB::raw('DATE(tbl_client.art_date) AS art_date'),
                    'tbl_client.motivational_enable',
                    'tbl_client.phone_no',
                    'tbl_client.clinic_id',
                    'tbl_client.client_status',
                    'tbl_client.status',
                    'tbl_client.language_id',
                    'tbl_client.group_id',
                    'tbl_client.txt_time',
                    'tbl_condition.id as client_treatment',
                    'tbl_gender.id as gender',
                    'tbl_gender.name as gender_name',
                    'tbl_marital_status.id as marital',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_client.locator_county',
                    'tbl_client.locator_sub_county',
                    'tbl_client.locator_ward',
                    'tbl_client.locator_village',
                    'tbl_client.locator_location',
                    'tbl_client.upi_no'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->paginate(20000);
        }
        if (Auth::user()->access_level == 'Partner') {
            $gender = Gender::all();
            $marital = Marital::all();
            $treatment = Condition::all();
            $grouping = Group::all()->where('status', '=', 'Active');
            $clinics = Clinic::all();
            $time = Time::all();
            $county = County::where('status', '=', 'Active')->pluck('name', 'id');
            $language = Language::all()->where('status', '=', 'Active');

            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_groups', 'tbl_client.group_id', '=', 'tbl_groups.id')
                ->join('tbl_language', 'tbl_client.language_id', '=', 'tbl_language.id')
                ->join('tbl_condition', 'tbl_client.client_status', '=', 'tbl_condition.name')
                ->join('tbl_marital_status', 'tbl_client.marital', '=', 'tbl_marital_status.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select(
                    'tbl_client.id',
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_client.dob',
                    'tbl_client.created_at',
                    'tbl_client.smsenable',
                    DB::raw('DATE(tbl_client.enrollment_date) AS enrollment_date'),
                    DB::raw('DATE(tbl_client.art_date) AS art_date'),
                    'tbl_client.motivational_enable',
                    'tbl_client.phone_no',
                    'tbl_client.clinic_id',
                    'tbl_client.client_status',
                    'tbl_client.status',
                    'tbl_client.language_id',
                    'tbl_client.group_id',
                    'tbl_client.txt_time',
                    'tbl_condition.id as client_treatment',
                    'tbl_gender.id as gender',
                    'tbl_gender.name as gender_name',
                    'tbl_marital_status.id as marital',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_client.locator_county',
                    'tbl_client.locator_sub_county',
                    'tbl_client.locator_ward',
                    'tbl_client.locator_village',
                    'tbl_client.locator_location',
                    'tbl_client.upi_no'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->paginate(1000);
        }
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $gender = Gender::all();
            $marital = Marital::all();
            $treatment = Condition::all();
            $grouping = Group::all()->where('status', '=', 'Active');
            $clinics = Clinic::all();
            $time = Time::all();
            $county = County::where('status', '=', 'Active')->pluck('name', 'id');
            $language = Language::all()->where('status', '=', 'Active');

            $clients = Client::join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_gender', 'tbl_client.gender', '=', 'tbl_gender.id')
                ->join('tbl_groups', 'tbl_client.group_id', '=', 'tbl_groups.id')
                ->join('tbl_language', 'tbl_client.language_id', '=', 'tbl_language.id')
                ->join('tbl_condition', 'tbl_client.client_status', '=', 'tbl_condition.name')
                ->join('tbl_marital_status', 'tbl_client.marital', '=', 'tbl_marital_status.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->select(
                    'tbl_client.id',
                    'tbl_client.clinic_number',
                    'tbl_client.f_name',
                    'tbl_client.m_name',
                    'tbl_client.l_name',
                    'tbl_client.dob',
                    'tbl_client.created_at',
                    'tbl_client.smsenable',
                    DB::raw('DATE(tbl_client.enrollment_date) AS enrollment_date'),
                    DB::raw('DATE(tbl_client.art_date) AS art_date'),
                    'tbl_client.motivational_enable',
                    'tbl_client.phone_no',
                    'tbl_client.clinic_id',
                    'tbl_client.client_status',
                    'tbl_client.status',
                    'tbl_client.language_id',
                    'tbl_client.group_id',
                    'tbl_client.txt_time',
                    'tbl_condition.id as client_treatment',
                    'tbl_gender.id as gender',
                    'tbl_gender.name as gender_name',
                    'tbl_marital_status.id as marital',
                    'tbl_master_facility.code',
                    'tbl_master_facility.name as facility',
                    'tbl_partner.name as partner',
                    'tbl_county.name as county',
                    'tbl_client.locator_county',
                    'tbl_client.locator_sub_county',
                    'tbl_client.locator_ward',
                    'tbl_client.locator_village',
                    'tbl_client.locator_location',
                    'tbl_client.upi_no'
                )
                ->where('tbl_client.status', '=', 'Active')
                ->whereNull('tbl_client.hei_no')
                ->paginate(1000);
        }

        return view('new_reports.clients', compact('clients', 'gender', 'marital', 'clinics', 'time', 'treatment', 'language', 'grouping', 'county'));
    }
    public function edit_client(Request $request)
    {

        try {
            $client = Client::where('id', $request->id)
                ->update([
                    'clinic_number' => $request->clinic_number,
                    'f_name' => $request->first_name,
                    'm_name' => $request->middle_name,
                    'l_name' => $request->last_name,
                    'dob' => $request->birth,
                    'gender' => $request->gender,
                    'marital' => $request->marital,
                    'client_status' => $request->treatment,
                    'enrollment_date' => date("Y-m-d", strtotime($request->enrollment_date)),
                    'art_date' => date("Y-m-d", strtotime($request->art_date)),
                    'phone_no' => $request->phone,
                    'language_id' => $request->language,
                    'smsenable' => $request->smsenable,
                    'motivational_enable' => $request->motivational_enable,
                    'txt_time' => $request->txt_time,
                    'status' => $request->status,
                    'group_id' => $request->group,
                    'clinic_id' => $request->clinic,
                    'locator_county' => $request->county,
                    'locator_sub_county' => $request->subcounty,
                    'locator_ward' => $request->ward,
                    'locator_location' => $request->location,
                    'locator_village' => $request->village,
                ]);

            if ($client) {
                Session::flash('statuscode', 'success');
                return redirect('new/clients/list')->with('status', 'Client' . ' ' . $request->clinic_number . ' ' . 'details was successfully updated!');
            } else {
                Session::flash('statuscode', 'error');
                return back()->with('error', 'Could not update client details please try again later.');
            }
        } catch (Exception $e) {
            $code = $e->getCode();

            if ((string)$code === (string)"23000") {

                Session::flash('statuscode', 'success');
                return back()->with('status', 'Clinic Number' . ' ' . $request->clinic_number . ' ' . 'belongs to another client! ');
            } else {
                return back();
            }
        }
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
        if (Auth::user()->access_level == 'Sub County') {
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->select('tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county', 'tbl_sub_county.name as subcounty')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.sub_county_id', Auth::user()->subcounty_id)
                ->orderBy('tbl_appointment.created_at', 'DESC')
                ->groupBy('tbl_partner_facility.mfl_code')
                ->get();
        }
        if (Auth::user()->access_level == 'County') {
            $active_facilities = PartnerFacility::join('tbl_client', 'tbl_partner_facility.mfl_code', '=', 'tbl_client.mfl_code')
                ->join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
                ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
                ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
                ->join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
                ->select('tbl_master_facility.code', 'tbl_master_facility.name as facility', 'tbl_partner.name as partner', 'tbl_county.name as county', 'tbl_sub_county.name as subcounty')
                ->where('tbl_appointment.created_at', '>=', Carbon::now()->subMonths(6))
                ->where('tbl_partner_facility.county_id', Auth::user()->county_id)
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
    public function message_form()
    {
        return view('new_reports.message_form');
    }

    public function client_message(Request $request)
    {
        $selected_from = $request->date_from;
        $selected_to = $request->date_to;
        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
            ->join('tbl_clnt_outgoing', 'tbl_appointment.id', 'tbl_clnt_outgoing.appointment_id')
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
                ->where('tbl_appointment.appntmnt_date', '>=', date($request->date_from))
                ->where('tbl_appointment.appntmnt_date', '<=', date($request->date_to))
                ->get();
        }
        if (Auth::user()->access_level == 'Partner') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
            ->join('tbl_clnt_outgoing', 'tbl_appointment.id', 'tbl_clnt_outgoing.appointment_id')
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
                ->where('tbl_appointment.appntmnt_date', '>=', date($request->date_from))
                ->where('tbl_appointment.appntmnt_date', '<=', date($request->date_to))
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->get();
        }
        if (Auth::user()->access_level == 'Facility') {
            $client_messages = Appointments::join('tbl_client', 'tbl_appointment.client_id', 'tbl_client.id')
                ->join('tbl_clnt_outgoing', 'tbl_appointment.id', 'tbl_clnt_outgoing.appointment_id')
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
                ->whereDate('tbl_appointment.appntmnt_date', '>=', date($request->date_from))
                ->whereDate('tbl_appointment.appntmnt_date', '<=', date($request->date_to))
                ->where('tbl_partner_facility.mfl_code', Auth::user()->facility_id)
                ->get();
        }


        return view('new_reports.client_messages', compact('client_messages', 'selected_from', 'selected_to'));
    }
}
