<?php

namespace App\Http\Controllers;


use App\Models\Dashboard;
use App\Models\Facility;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Donor;
use App\Models\ClientPerformance;
use App\Models\Consented;
use App\Models\Appointments;
use App\Models\TodayAppointment;
use App\Models\Message;
use App\Models\County;
use App\Models\SubCounty;
use App\Models\MainDashboardBar;
use App\Models\ClientRegistration;
use App\Models\PartnerFacility;
use App\Models\BarClient;
use App\Models\BarAppointment;
use App\Models\FutureApp;
use Carbon\Carbon;
use DB;
use Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache; // <= call this!



use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {

        return view('dashboard.dashboardv1');
    }

    public function user_level()
    {
        $facility_all = Facility::all()->where('code', Auth::user()->facility_id);

        if (Auth::user()->access_level == 'Partner')
        {
        $partner_all = Partner::all()->where('id', Auth::user()->partner_id);
        }
        if (Auth::user()->access_level == 'Donor')
        {
        $donor_all = Donor::all()->where('id', Auth::user()->donor_id);
        }

        return view('layouts.large-vertical-sidebar.header', compact('facility_all', 'partner_all', 'donor_all'));
    }

    public function get_client_data()
    {
        $data                = [];

        if (Auth::user()->access_level == 'Partner') { //user is partner
            $selected_partners = [Auth::user()->partner_id];
        }
        if (Auth::user()->access_level == 'Facility') { //user is facility
            $selected_facilities = [Auth::user()->facility_id];
        }
        if (Auth::user()->access_level == 'County') { // user is a county user
            $selected_counties = [Auth::user()->county_id];
        }

        $partners_with_data = ClientRegistration::select('partner_id')->groupBy('partner_id');

        $counties_with_data = ClientRegistration::select('county_id')->groupBy('county_id');


        $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');
        $all_counties = County::select('id', 'name')->distinct('id')->whereIn('id', $counties_with_data)->get();


        $all_clients_number = ClientPerformance::whereNotNull('actual_clients');
        $pec_client_sum = ClientRegistration::select('total_percentage')->sum('total_percentage');
        $pec_client_count = ClientRegistration::whereNotNull('total_percentage');
        $all_target_clients = ClientPerformance::whereNotNull('target_clients');
        $all_consented_clients = ClientRegistration::whereNotNull('consented');
        $all_future_appointments = FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code');
        $number_of_facilities = ClientPerformance::whereNotNull('mfl_code');

        $registered_clients = MainDashboardBar::select(\DB::raw("SUM(clients) as count"))
            ->groupBy('MONTH')
            ->orderBy('MONTH', 'asc')
            ->get()->toArray();
        $registered_clients = array_column($registered_clients, 'count');

        $consented_clients = MainDashboardBar::select(\DB::raw("SUM(consented) as count"))
            ->groupBy('MONTH')
            ->orderBy('MONTH', 'asc')
            ->get()->toArray();
        $consented_clients = array_column($consented_clients, 'count');
        $month_count = MainDashboardBar::select('MONTH as months')
            ->groupBy('MONTH')
            ->orderBy('MONTH', 'asc')
            ->get()->toArray();
        $month_count = array_column($month_count, 'months');

        $chart_consent = array($month_count);
        foreach ($month_count as $index => $month) {
            $chart_consent[$month] = $consented_clients[$index];
        }
        $chart_registered = array($month_count);
        foreach ($month_count as $index => $month) {
            $chart_registered[$month] = $registered_clients[$index];
        }

        $registered_clients_count = ClientRegistration::select(\DB::raw("SUM(clients) as count"))
            ->pluck('count');
        $consented_clients_count = ClientRegistration::select(\DB::raw("SUM(consented) as count"))
            ->pluck('count');

        // dd($registered_clients_count);


        $data["all_clients_number"]        = $all_clients_number->sum('actual_clients');
        $data["pec_client_count"]        = $pec_client_count->avg('total_percentage');
        $data["all_target_clients"]         = $all_target_clients->sum('target_clients');
        $data["all_consented_clients"]        = $all_consented_clients->sum('consented');
        $data["all_future_appointments"]        = $all_future_appointments->count();
        $data["number_of_facilities"]         = $number_of_facilities->count();
        $data["all_partners"]         = $all_partners;
        $data["all_counties"]         = $all_counties;

        //return view('dashboard.dashboardv1', compact('data'));

        return view('dashboard.dashboardv1', compact(
            'all_partners',
            'all_counties',
            'chart_consent',
            'chart_registered',
            'month_count',
            'all_clients_number',
            'all_target_clients',
            'all_consented_clients',
            'all_future_appointments',
            'number_of_facilities',
            'pec_client_count',
            'registered_clients_count',
            'consented_clients_count',
            'registered_clients'
        ));

        return $data;
    }

    public function main_graph_dashboard()
    {

        $data                = [];

        //cache()->forget('');
        

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
    
            $clients_number = Cache::remember('all_clients_number', 21600, function () {
                return ClientPerformance::whereNotNull('actual_clients')->get();
            });

            $all_clients_number = Cache::remember('all_clients_sum', 21600, function () use ($clients_number) {
                return $clients_number->sum('actual_clients');
            });

            $target_clients = Cache::remember('all_target_clients', 21600, function () {
                return ClientPerformance::whereNotNull('target_clients')->get();
            });

            $all_target_clients = Cache::remember('all_target_sum', 21600, function () use ($target_clients) {
                return $target_clients->sum('target_clients');
            });

            $facilities = Cache::remember('number_of_facilities', 21600, function () {
                return ClientPerformance::whereNotNull('mfl_code')->get();
            });

            $number_of_facilities = Cache::remember('facilities_sum', 21600, function () use ($facilities) {
                return $facilities->count();
            });

            $consented_clients = Cache::remember('all_consented_clientss', 21600, function () {
                return ClientRegistration::whereNotNull('consented')->get();
            });

            $all_consented_clients = Cache::remember('all_consented_sum', 21600, function () use ($consented_clients) {
                return $consented_clients->sum('consented');
            });

            $pec_client_sum_o = Cache::remember('pec_client_sums', 21600, function () {
                return ClientRegistration::select('total_percentage')->get();
            });

            $pec_client_sum = Cache::remember('pec_client_s', 21600, function () use ($pec_client_sum_o) {
                return $pec_client_sum_o->sum('total_percentage');
            });

            $pec_client_count_o = Cache::remember('pec_client_count', 21600, function () {
                return ClientRegistration::whereNotNull('total_percentage')->get();
            });

            $pec_client_count = Cache::remember('pec_client_count_c', 21600, function () use ($pec_client_count_o) {
                return $pec_client_count_o->avg('total_percentage');
            });

            $all_future_appointments_o = Cache::remember('all_future_appointmentsss', 21600, function () {
                return FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code')->get();
            });

            $all_future_appointments = Cache::remember('future_appointments', 21600, function () use ($all_future_appointments_o) {
                return $all_future_appointments_o->count();
            });

            $bar_appointments_data = Cache::remember('bar_appointments_data', 21600, function () {
                return BarAppointment::all();
            });

            $bar_clients_data = Cache::remember('bar_clients_data', 21600, function () {
                return BarClient::all();
            });

            $registered_clients_count_o = Cache::remember('registered_clients_count', 21600, function () {
                return ClientRegistration::select('clients')->get();
            });

            $registered_clients_count = Cache::remember('registered_clients_count_c', 21600, function () use ($registered_clients_count_o) {
                return $registered_clients_count_o->sum('clients');
            });

            $consented_clients_count_o = Cache::remember('consented_clients_count', 21600, function () {
                return ClientRegistration::select('consented')->get();
            });

            $consented_clients_count = Cache::remember('consented_clients_count_c', 21600, function () use ($consented_clients_count_o) {
                return $consented_clients_count_o->sum('consented');
            });
        }


        if (Auth::user()->access_level == 'Partner'){

            $all_partners = Partner::where('status', '=', 'Active')
            ->where('id', Auth::user()->partner_id)
            ->pluck('name', 'id');

            //$all_counties = County::select('id', 'name')->distinct('id')->whereIn('id', $counties_with_data)->get();


            $clients_number = Cache::remember('all_partner_clients_number', 21600, function () {
                return ClientPerformance::whereNotNull('actual_clients')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $all_clients_number = Cache::remember('all_partner_clients_sum', 21600, function () use ($clients_number) {
                return $clients_number->sum('actual_clients');
            });

            $pec_client_sum_o = Cache::remember('pec_partner_client_sum', 21600, function () {
                return ClientRegistration::select('total_percentage')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $pec_client_sum = Cache::remember('pec_partner_client_avg', 21600, function () use ($pec_client_sum_o) {
                return $pec_client_sum_o->avg('total_percentage');
            });

            $pec_client_count_o = Cache::remember('pec_client_count', 21600, function () {
                return ClientRegistration::whereNotNull('total_percentage')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $pec_client_count = Cache::remember('pec_client_count_c', 21600, function () use ($pec_client_count_o) {
                return $pec_client_count_o->avg('total_percentage');
            });

            $target_clients = Cache::remember('all_partner_target_clients', 21600, function () {
                return ClientPerformance::whereNotNull('target_clients')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $all_target_clients = Cache::remember('all_partner_target_clients_sum', 21600, function () use ($target_clients) {
                return $target_clients->sum('target_clients');
            });

            $consented_clients = Cache::remember('all_partner_consented_clients', 21600, function () {
                return ClientRegistration::whereNotNull('consented')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $all_consented_clients = Cache::remember('all_partner_consented_clients_sum', 21600, function () use ($consented_clients) {
                return $consented_clients->sum('consented');
            });

            $all_future_appointments_o = Cache::remember('all_partner_future_appointments', 21600, function () {
                return FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code')
                ->where('tbl_partner_facility.partner_id', Auth::user()->partner_id)
                ->get();
            });

            $all_future_appointments = Cache::remember('all_partner_future_appointments_count', 21600, function () use ($all_future_appointments_o) {
                return $all_future_appointments_o->count();
            });

            $facilities = Cache::remember('partner_number_of_facilities', 21600, function () {
                return ClientPerformance::whereNotNull('mfl_code')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $number_of_facilities = Cache::remember('partner_number_of_facilities_count', 21600, function () use ($facilities) {
                return $facilities->count();
            });

            $registered_clients_count_o = Cache::remember('partner_registered_clients_count', 21600, function () {
                return ClientRegistration::select('clients')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });

            $registered_clients_count = Cache::remember('partner_registered_clients_count_sum', 21600, function () use ($registered_clients_count_o) {
                return $registered_clients_count_o->sum('clients');
            });

            $consented_clients_count_o = Cache::remember('consented_clients_count', 21600, function () {
                return ClientRegistration::select('consented')
                ->where('partner_id', Auth::user()->partner_id)
                ->get();
            });
            
            $consented_clients_count = Cache::remember('partner_consented_clients_sum', 21600, function () use ($consented_clients_count_o) {
                return $consented_clients_count_o->sum('consented');
            });
            
            $bar_appointments_data = Cache::remember('partner_bar_appointments_data', 21600, function () {
                return BarAppointment::all()->where('partner_id', Auth::user()->partner_id);
            });
            
            $bar_clients_data = Cache::remember('partner_bar_clients_data', 21600, function () {
                return BarClient::all()->where('partner_id', Auth::user()->partner_id);
            });
        }


        $data["all_clients_number"]        = $all_clients_number;
        $data["pec_client_count"]        = $pec_client_count;
        $data["all_target_clients"]         = $all_target_clients;
        $data["all_consented_clients"]        = $all_consented_clients;
        $data["all_future_appointments"]        = $all_future_appointments;
        $data["number_of_facilities"]         = $number_of_facilities;
        $data["all_partners"]         = $all_partners;
        $data["bar_appointments_data"]         = $bar_appointments_data;
        $data["bar_clients_data"]         = $bar_clients_data;
        $data["registered_clients_count"]         = $registered_clients_count;
        $data["consented_clients_count"]         = $consented_clients_count;


        return view('dashboard.dashboardv1', compact(
            'all_partners',
            'all_clients_number',
            'all_target_clients',
            'all_consented_clients',
            'all_future_appointments',
            'number_of_facilities',
            'pec_client_count',
            'registered_clients_count',
            'bar_clients_data',
            'bar_appointments_data',
            'consented_clients_count'
        ));
    }

    public function filter_dashboard(Request $request)
    {

        $data                = [];

        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        if (Auth::user()->access_level == 'Partner') { //user is partner
            $selected_partners = [Auth::user()->partner_id];
        }
        if (Auth::user()->access_level == 'Facility') { //user is facility
            $selected_facilities = [Auth::user()->facility_id];
        }
        if (Auth::user()->access_level == 'County') { // user is a county user
            $selected_counties = [Auth::user()->county_id];
        }


        // $all_clients_number = ClientPerformance::whereNotNull('actual_clients');
        // $pec_client_sum = ClientRegistration::select('total_percentage')->sum('total_percentage');
        // $pec_client_count = ClientRegistration::whereNotNull('total_percentage');
        // $all_target_clients = ClientPerformance::whereNotNull('target_clients');
        // $all_consented_clients = ClientRegistration::whereNotNull('consented');
        // $all_future_appointments = FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code');
        // $number_of_facilities = ClientPerformance::whereNotNull('mfl_code');
        // $registered_clients_count = ClientRegistration::select('clients');
        // $consented_clients_count = ClientRegistration::select('consented');

        if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $all_clients_number = Cache::remember('all_clients_number', 21600, function () {
                return ClientPerformance::whereNotNull('actual_clients');
            });

            $pec_client_sum = Cache::remember('pec_client_sums', 21600, function () {
                return ClientRegistration::select('total_percentage');
            });

            $pec_client_count = Cache::remember('pec_client_count', 21600, function () {
                return ClientRegistration::whereNotNull('total_percentage');
            });

            $all_target_clients = Cache::remember('all_target_clients', 21600, function () {
                return ClientPerformance::whereNotNull('target_clients');
            });

            $all_consented_clients  = Cache::remember('all_consented_clientss', 21600, function () {
                return ClientRegistration::whereNotNull('consented');
            });

            $all_future_appointments = Cache::remember('all_future_appointmentsss', 21600, function () {
                return FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code');
            });

            $number_of_facilities = Cache::remember('number_of_facilities', 21600, function () {
                return ClientPerformance::whereNotNull('mfl_code');
            });

            $registered_clients_count = Cache::remember('registered_clients_count', 21600, function () {
                return ClientRegistration::select('clients');
            });

            $consented_clients_count = Cache::remember('consented_clients_count', 21600, function () {
                return ClientRegistration::select('consented');
            });
        
        }

        if (Auth::user()->access_level == 'Partner') {

            $all_clients_number = Cache::remember('all_partner_clients_number', 21600, function () {
                return ClientPerformance::whereNotNull('actual_clients');
            });

            $pec_client_sum = Cache::remember('pec_partner_client_sum', 21600, function () {
                return ClientRegistration::select('total_percentage');
            });

            $pec_client_count = Cache::remember('pec_partner_client_count', 21600, function () {
                return ClientRegistration::whereNotNull('total_percentage');
            });

            $all_target_clients = Cache::remember('all_partner_target_clients', 21600, function () {
                return ClientPerformance::whereNotNull('target_clients');
            });

            $all_consented_clients = Cache::remember('all_partner_consented_clients', 21600, function () {
                return ClientRegistration::whereNotNull('consented');
            });

            $all_future_appointments = Cache::remember('all_partner_future_appointments', 21600, function () {
                return FutureApp::join('tbl_partner_facility', 'tbl_future_appointments_query.mfl_code', '=', 'tbl_partner_facility.mfl_code');
            });

            $number_of_facilities = Cache::remember('partner_number_of_facilities', 21600, function () {
                return ClientPerformance::whereNotNull('mfl_code');
            });

            $registered_clients_count = Cache::remember('partner_registered_clients_count', 21600, function () {
                return ClientRegistration::select('clients');
            });

            $consented_clients_count = Cache::remember('partner_consented_clients_count', 21600, function () {
                return ClientRegistration::select('consented');
            });
        
        }
        //return [$all_clients_number, $consented_clients_count];


        if (!empty($selected_partners)) {
            $all_clients_number = $all_clients_number->where('partner_id', $selected_partners);
            $pec_client_count = $pec_client_count->where('partner_id', $selected_partners);
            $all_target_clients = $all_target_clients->where('partner_id', $selected_partners);
            $all_consented_clients = $all_consented_clients->where('partner_id', $selected_partners);
            $number_of_facilities = $number_of_facilities->where('partner_id', $selected_partners);
            $registered_clients_count = $registered_clients_count->where('partner_id', $selected_partners);
            $consented_clients_count = $consented_clients_count->where('partner_id', $selected_partners);
            $all_future_appointments = $all_future_appointments->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $all_clients_number = $all_clients_number->where('county_id', $selected_counties);
            $pec_client_count = $pec_client_count->where('county_id', $selected_counties);
            $all_target_clients = $all_target_clients->where('county_id', $selected_counties);
            $all_consented_clients = $all_consented_clients->where('county_id', $selected_counties);
            $number_of_facilities = $number_of_facilities->where('county_id', $selected_counties);
            $registered_clients_count = $registered_clients_count->where('county_id', $selected_counties);
            $consented_clients_count = $consented_clients_count->where('county_id', $selected_counties);
            $all_future_appointments = $all_future_appointments->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $all_clients_number = $all_clients_number->where('sub_county_id', $selected_subcounties);
            $pec_client_count = $pec_client_count->where('sub_county_id', $selected_subcounties);
            $all_target_clients = $all_target_clients->where('sub_county_id', $selected_subcounties);
            $all_consented_clients = $all_consented_clients->where('sub_county_id', $selected_subcounties);
            $number_of_facilities = $number_of_facilities->where('sub_county_id', $selected_subcounties);
            $registered_clients_count = $registered_clients_count->where('sub_county_id', $selected_subcounties);
            $consented_clients_count = $consented_clients_count->where('sub_county_id', $selected_subcounties);
            $all_future_appointments = $all_future_appointments->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $all_clients_number = $all_clients_number->where('mfl_code', $selected_facilites);
            $pec_client_count = $pec_client_count->where('mfl_code', $selected_facilites);
            $all_target_clients = $all_target_clients->where('mfl_code', $selected_facilites);
            $all_consented_clients = $all_consented_clients->where('mfl_code', $selected_facilites);
            $number_of_facilities = $number_of_facilities->where('mfl_code', $selected_facilites);
            $registered_clients_count = $registered_clients_count->where('mfl_code', $selected_facilites);
            $consented_clients_count = $consented_clients_count->where('mfl_code', $selected_facilites);
            $all_future_appointments = $all_future_appointments->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }


        $data["all_clients_number"]        = $all_clients_number->sum('actual_clients');
        $data["pec_client_count"]        = $pec_client_count->avg('total_percentage');
        $data["all_target_clients"]         = $all_target_clients->sum('target_clients');
        $data["all_consented_clients"]        = $all_consented_clients->sum('consented');
        $data["all_future_appointments"]        = $all_future_appointments->count();
        $data["number_of_facilities"]         = $number_of_facilities->count();
        $data["registered_clients_count"]       = $registered_clients_count->sum('clients');
        $data["consented_clients_count"]        = $consented_clients_count->sum('consented');

        return $data;
    }


    public function get_dashboard_counties(Request $request)
    {
        $partner_ids = array();
        $strings_array = $request->partners;
        if (!empty($strings_array)) {
            foreach ($strings_array as $each_id) {
                $partner_ids[] = (int) $each_id;
            }
        }
        $counties_with_data = ClientRegistration::select('county_id')->distinct('county_id')->groupBy('county_id')->get();

        if (!empty($partner_ids)) {
            $all_counties = County::join('tbl_sub_county', 'county.id', '=', 'tbl_sub_county.county_id')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->select('tbl_county.id as id', 'tbl_county.name as name')
                ->distinct('tbl_county.id')
                ->whereIn('tbl_partner_facility.partner_id', $partner_ids)
                ->whereIn('tbl_county.id', $counties_with_data)
                ->groupBy('tbl_county.id', 'tbl_county.name')
                ->get('tbl_county.id');
        } else {
            $all_counties = County::join('tbl_sub_county', 'county.id', '=', 'tbl_sub_county.county_id')
                ->join('tbl_partner_facility', 'tbl_sub_county.id', '=', 'tbl_partner_facility.sub_county_id')
                ->select('tbl_county.id as id', 'tbl_county.name as name')
                ->distinct('tbl_county.id')
                ->whereIn('tbl_county.id', $counties_with_data)
                ->groupBy('tbl_county.id', 'tbl_county.name')
                ->get();
        }
        return $all_counties;
    }

    public function get_counties($id)
    {

        $counties = PartnerFacility::join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
            ->where("tbl_partner_facility.partner_id", $id)
            ->pluck("tbl_county.name", "tbl_county.id");
        return json_encode($counties);
    }

    public function get_dashboard_sub_counties($id)
    {
        $subcounties = PartnerFacility::join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
            ->where("tbl_partner_facility.county_id", $id)
            ->pluck("tbl_sub_county.name", "tbl_sub_county.id");

            if (Auth::user()->access_level == 'Partner'){
                $subcounties = PartnerFacility::join('tbl_sub_county', 'tbl_partner_facility.sub_county_id', '=', 'tbl_sub_county.id')
            ->where("tbl_partner_facility.county_id", $id)
            ->where("tbl_partner_facility.partner_id", '=', Auth::user()->partner_id)
            ->pluck("tbl_sub_county.name", "tbl_sub_county.id");
            }
        return json_encode($subcounties);
    }

    public function get_dashboard_facilities($id)
    {
        $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->where("tbl_partner_facility.sub_county_id", $id)
           // ->where("tbl_partner_facility.partner_id", $id)
            ->pluck("tbl_master_facility.name", "tbl_master_facility.code");

            if (Auth::user()->access_level == 'Partner'){
                $facilities = PartnerFacility::join('tbl_master_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->where("tbl_partner_facility.sub_county_id", $id)
            ->where("tbl_partner_facility.partner_id", '=', Auth::user()->partner_id)
            ->pluck("tbl_master_facility.name", "tbl_master_facility.code");
            }

        return json_encode($facilities);
    }


    public function facility_dashboard()
    {
        // clients count


        if (Auth::user()->access_level == 'Facility') {

            $clients_count = Client::whereNotNull('clinic_number')
                ->select(\DB::raw("COUNT(id) as count"))
                ->where('mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();
            $consented_count = Client::where('smsenable', '=', 'Yes')
                ->select(\DB::raw("COUNT(id) as count"))
                ->where('mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();
            $appointment_count = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->whereNotNull('tbl_appointment.id')
                ->select(\DB::raw("COUNT(tbl_appointment.id) as count"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();
            $messages_count = Message::join('tbl_client', 'tbl_client.id', '=', 'tbl_clnt_outgoing.clnt_usr_id')
                ->where('tbl_clnt_outgoing.recepient_type', '=', 'Client')
                ->select(\DB::raw("COUNT(tbl_clnt_outgoing.id) as count"))
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->pluck('count')->first();

            // today's appointments
            $today_appointment = TodayAppointment::select('clinic_no', 'file_no', 'client_name', 'client_phone_no', 'appntmnt_date', 'appointment_type')
                ->where('mfl_code', Auth::user()->facility_id)
                ->get();
            $missed_appoitment = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name', 'tbl_appointment.app_msg', 'tbl_appointment.no_calls', 'tbl_appointment.home_visits', 'tbl_appointment.no_msgs')
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Missed')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            $defaulted_appoitment = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name', 'tbl_appointment.app_msg', 'tbl_appointment.no_calls', 'tbl_appointment.home_visits', 'tbl_appointment.no_msgs')
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'Defaulted')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
            $ltfu_appoitment = Appointments::join('tbl_client', 'tbl_client.id', '=', 'tbl_appointment.client_id')
                ->join('tbl_appointment_types', 'tbl_appointment_types.id', '=', 'tbl_appointment.app_type_1')
                ->select('tbl_client.clinic_number', 'tbl_client.file_no', DB::raw("CONCAT(`tbl_client`.`f_name`, ' ', `tbl_client`.`m_name`, ' ', `tbl_client`.`l_name`) as full_name"), 'tbl_client.phone_no', 'tbl_appointment.appntmnt_date', 'tbl_appointment_types.name', 'tbl_appointment.app_msg', 'tbl_appointment.no_calls', 'tbl_appointment.home_visits', 'tbl_appointment.no_msgs')
                ->whereNotNull('tbl_appointment.id')
                ->where('tbl_appointment.app_status', '=', 'LTFU')
                ->where('tbl_appointment.active_app', '=', 1)
                ->where('tbl_client.mfl_code', Auth::user()->facility_id)
                ->get();
        }


        return view('dashboard.facility_dashboard', compact('clients_count', 'consented_count', 'appointment_count', 'messages_count', 'today_appointment', 'missed_appoitment', 'defaulted_appoitment', 'ltfu_appoitment'));
    }


    public function client_distribution_graphs()
    {
        $gender_male = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('gender', '=', 2)
            ->pluck('count');

        $gender_female = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('gender', '=', 2)
            ->pluck('count');

        $gender_unavailable = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('gender', '=', 5)
            ->pluck('count');

        $language_swahili = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('language_id', '=', 1)
            ->pluck('count');

        $language_english = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('language_id', '=', 2)
            ->pluck('count');

        $language_nolanguage = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('language_id', '=', 5)
            ->pluck('count');

        $marital_single = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 1)
            ->pluck('count');

        $marital_monogamous = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 2)
            ->pluck('count');

        $marital_divorced = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 3)
            ->pluck('count');

        $marital_widowed = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 4)
            ->pluck('count');

        $marital_cohabating = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 5)
            ->pluck('count');

        $marital_unavailable = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 6)
            ->pluck('count');

        $marital_notapplicable = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 7)
            ->pluck('count');

        $marital_polygamous = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('marital', '=', 8)
            ->pluck('count');

        $client_type_new = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('client_type', '=', 'New')
            ->pluck('count');

        $client_type_transfer = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('client_type', '=', 'Transfer')
            ->pluck('count');

        $client_entry_point_mobile = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('entry_point', '=', 'Mobile')
            ->pluck('count');

        $client_entry_point_web = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('entry_point', '=', 'Web')
            ->pluck('count');

        $client_entry_point_il = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('entry_point', '=', 'IL')
            ->pluck('count');

        $group_adults = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 1)
            ->pluck('count');

        $group_adolescents = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 2)
            ->pluck('count');

        $group_peads = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 3)
            ->pluck('count');

        $group_art_clients = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 4)
            ->pluck('count');

        $group_prenatal = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 5)
            ->pluck('count');

        $group_postalnatal = Client::whereNotNull('clinic_number')
            ->select(\DB::raw("COUNT(id) as count"))
            ->where('group_id', '=', 10)
            ->pluck('count');

        // dd($client_entry_point_mobile);

        return view('dashboard.client_registration_distribution', compact(
            'gender_male',
            'gender_female',
            'gender_unavailable',
            'language_swahili',
            'language_english',
            'language_nolanguage',
            'group_postalnatal',
            'group_prenatal',
            'group_art_clients',
            'group_peads',
            'group_adolescents',
            'group_adults',
            'client_entry_point_il',
            'client_entry_point_web',
            'client_entry_point_mobile',
            'client_type_transfer',
            'client_type_new',
            'marital_polygamous',
            'marital_notapplicable',
            'marital_unavailable',
            'marital_cohabating',
            'marital_widowed',
            'marital_divorced',
            'marital_monogamous',
            'marital_single'
        ));
    }

    public function active_facilities()
    {
        $all_active_facilities = ClientPerformance::join('tbl_county', 'tbl_county.id', '=', 'vw_client_performance_monitor.county_id')
            ->join('tbl_sub_county', 'tbl_sub_county.id', '=', 'vw_client_performance_monitor.sub_county_id')
            ->join('tbl_partner', 'tbl_partner.id', '=', 'vw_client_performance_monitor.partner_id')
            ->select('vw_client_performance_monitor.facility', 'vw_client_performance_monitor.mfl_code', 'vw_client_performance_monitor.actual_clients', 'tbl_county.name as county', 'tbl_sub_county.name as sub_county', 'tbl_partner.name as partner')
            ->get();

        return view('facilities.active_facilities', compact('all_active_facilities'));
    }

    public function client_dashboard()
    {
        if (Auth::user()->access_level == 'Admin') {

            $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        // registration by age group
        $consented_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $registered_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        //registration by marital status
        $single_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '1')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $monogamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '2')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $divorced_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '3')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $widowed_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '4')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $cohabating_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '5')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $unavailable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '6')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $notapplicable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '7')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $polygamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '8')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $single_registered = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '1')
        ->pluck('count');
        $monogamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '2')
        ->pluck('count');
        $divorced_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '3')
        ->pluck('count');
        $widowed_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '4')
        ->pluck('count');
        $cohabating_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '5')
        ->pluck('count');
        $unavailable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '6')
        ->pluck('count');
        $notapplicable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '7')
        ->pluck('count');
        $polygamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '8')
        ->pluck('count');

        }

        if (Auth::user()->access_level == 'Donor') {

            $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        // registration by age group
        $consented_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $consented_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $registered_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        $registered_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
        ->pluck('count');

        //registration by marital status
        $single_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '1')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $monogamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '2')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $divorced_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '3')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $widowed_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '4')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $cohabating_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '5')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $unavailable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '6')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $notapplicable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '7')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');
        $polygamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '8')
        ->where('smsenable', '=', 'Yes')
        ->pluck('count');

        $single_registered = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '1')
        ->pluck('count');
        $monogamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '2')
        ->pluck('count');
        $divorced_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '3')
        ->pluck('count');
        $widowed_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '4')
        ->pluck('count');
        $cohabating_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '5')
        ->pluck('count');
        $unavailable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '6')
        ->pluck('count');
        $notapplicable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '7')
        ->pluck('count');
        $polygamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
        ->where('marital', '=', '8')
        ->pluck('count');

        }

        if (Auth::user()->access_level == 'Partner') {
            $all_partners = Partner::where('status', '=', 'Active')
            ->where('id', Auth::user()->partner_id)
            ->pluck('name', 'id');
            // registration by age group
            $consented_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $consented_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $consented_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $consented_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $consented_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $registered_nine = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end)) AS count"))
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $registered_forteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end)) AS count"))
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $registered_nineteen = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end)) AS count"))
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $registered_twenty_four = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end)) AS count"))
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $registered_over_twenty_five = Client::select(\DB::raw("count((case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end)) AS count"))
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            //registration by marital status
            $single_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '1')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $monogamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '2')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $divorced_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '3')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $widowed_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '4')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $cohabating_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '5')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $unavailable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '6')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $notapplicable_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '7')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $polygamous_consented = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '8')
            ->where('smsenable', '=', 'Yes')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            $single_registered = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '1')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $monogamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '2')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $divorced_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '3')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $widowed_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '4')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $cohabating_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '5')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $unavailable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '6')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $notapplicable_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '7')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');
            $polygamous_registered  = Client::select(\DB::raw("COUNT(marital) as count"))
            ->where('marital', '=', '8')
            ->where('partner_id', Auth::user()->partner_id)
            ->pluck('count');

            }


        return view('dashboard.clients_dashboard', compact(
            'all_partners',
            'consented_nine',
            'consented_forteen',
            'consented_nineteen',
            'consented_twenty_four',
            'consented_over_twenty_five',
            'registered_nine',
            'registered_forteen',
            'registered_nineteen',
            'registered_twenty_four',
            'registered_over_twenty_five',
            'single_consented',
            'monogamous_consented',
            'divorced_consented',
            'widowed_consented',
            'cohabating_consented',
            'unavailable_consented',
            'notapplicable_consented',
            'polygamous_consented',
            'single_registered',
            'monogamous_registered',
            'divorced_registered',
            'widowed_registered',
            'cohabating_registered',
            'unavailable_registered',
            'notapplicable_registered',
            'polygamous_registered'
        ));
    }
    public function filter_client_dashboard(Request $request)
    {
        $data                = [];
        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        // registration by age group
        $consented_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $consented_forteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $consented_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $consented_twenty_four = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $consented_over_twenty_five = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $registered_nine = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 0) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 9)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code');

        $registered_forteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 10) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 14)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code');

        $registered_nineteen = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 15) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 19)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code');

        $registered_twenty_four = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 20) and ((year(curdate()) - year(`tbl_client`.`dob`)) <= 24)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code');

        $registered_over_twenty_five = Client::select(\DB::raw("case when (((year(curdate()) - year(`tbl_client`.`dob`)) >= 25)) then `tbl_client`.`id` end"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code');

        //registration by marital status
        $single_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '1')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $monogamous_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '2')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $divorced_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '3')
        ->where('smsenable', '=', 'Yes');
        $widowed_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '4')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $cohabating_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('marital', '=', '5')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $unavailable_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '6')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $notapplicable_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '7')
        ->where('tbl_client.smsenable', '=', 'Yes');
        $polygamous_consented = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '8')
        ->where('tbl_client.smsenable', '=', 'Yes');

        $single_registered = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '1');
        $monogamous_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '2');
        $divorced_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('marital', '=', '3');
        $widowed_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '4');
        $cohabating_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '5');
        $unavailable_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '6');
        $notapplicable_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '7');
        $polygamous_registered  = Client::select(\DB::raw("tbl_client.marital"))
        ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        ->where('tbl_client.marital', '=', '8');

        if (!empty($selected_partners)) {
            $consented_nine = $consented_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $consented_forteen = $consented_forteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $consented_nineteen = $consented_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $consented_twenty_four = $consented_twenty_four->where('tbl_partner_facility.partner_id', $selected_partners);
            $consented_over_twenty_five = $consented_over_twenty_five->where('tbl_partner_facility.partner_id', $selected_partners);
            $registered_nine = $registered_nine->where('tbl_partner_facility.partner_id', $selected_partners);
            $registered_forteen = $registered_forteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $registered_nineteen = $registered_nineteen->where('tbl_partner_facility.partner_id', $selected_partners);
            $registered_twenty_four = $registered_twenty_four->where('tbl_partner_facility.partner_id', $selected_partners);
            $registered_over_twenty_five = $registered_over_twenty_five->where('tbl_partner_facility.partner_id', $selected_partners);
            $single_consented = $single_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $monogamous_consented = $monogamous_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $divorced_consented = $divorced_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $widowed_consented = $widowed_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $cohabating_consented = $cohabating_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $unavailable_consented = $unavailable_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $notapplicable_consented = $notapplicable_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $polygamous_consented = $polygamous_consented->where('tbl_partner_facility.partner_id', $selected_partners);
            $single_registered = $single_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $monogamous_registered = $monogamous_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $divorced_registered = $divorced_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $widowed_registered = $widowed_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $cohabating_registered = $cohabating_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $unavailable_registered = $unavailable_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $notapplicable_registered = $notapplicable_registered->where('tbl_partner_facility.partner_id', $selected_partners);
            $polygamous_registered = $polygamous_registered->where('tbl_partner_facility.partner_id', $selected_partners);
        }

        if (!empty($selected_counties)) {
            $consented_nine = $consented_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $consented_forteen = $consented_forteen->where('tbl_partner_facility.county_id', $selected_counties);
            $consented_nineteen = $consented_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $consented_twenty_four = $consented_twenty_four->where('tbl_partner_facility.county_id', $selected_counties);
            $consented_over_twenty_five = $consented_over_twenty_five->where('tbl_partner_facility.county_id', $selected_counties);
            $registered_nine = $registered_nine->where('tbl_partner_facility.county_id', $selected_counties);
            $registered_forteen = $registered_forteen->where('tbl_partner_facility.county_id', $selected_counties);
            $registered_nineteen = $registered_nineteen->where('tbl_partner_facility.county_id', $selected_counties);
            $registered_twenty_four = $registered_twenty_four->where('tbl_partner_facility.county_id', $selected_counties);
            $registered_over_twenty_five = $registered_over_twenty_five->where('tbl_partner_facility.county_id', $selected_counties);
            $single_consented = $single_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $monogamous_consented = $monogamous_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $divorced_consented = $divorced_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $widowed_consented = $widowed_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $cohabating_consented = $cohabating_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $unavailable_consented = $unavailable_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $notapplicable_consented = $notapplicable_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $polygamous_consented = $polygamous_consented->where('tbl_partner_facility.county_id', $selected_counties);
            $single_registered = $single_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $monogamous_registered = $monogamous_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $divorced_registered = $divorced_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $widowed_registered = $widowed_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $cohabating_registered = $cohabating_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $unavailable_registered = $unavailable_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $notapplicable_registered = $notapplicable_registered->where('tbl_partner_facility.county_id', $selected_counties);
            $polygamous_registered = $polygamous_registered->where('tbl_partner_facility.county_id', $selected_counties);
        }

        if (!empty($selected_subcounties)) {
            $consented_nine = $consented_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $consented_forteen = $consented_forteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $consented_nineteen = $consented_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $consented_twenty_four = $consented_twenty_four->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $consented_over_twenty_five = $consented_over_twenty_five->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $registered_nine = $registered_nine->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $registered_forteen = $registered_forteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $registered_nineteen = $registered_nineteen->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $registered_twenty_four = $registered_twenty_four->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $registered_over_twenty_five = $registered_over_twenty_five->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $single_consented = $single_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $monogamous_consented = $monogamous_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $divorced_consented = $divorced_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $widowed_consented = $widowed_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $cohabating_consented = $cohabating_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $unavailable_consented = $unavailable_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $notapplicable_consented = $notapplicable_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $polygamous_consented = $polygamous_consented->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $single_registered = $single_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $monogamous_registered = $monogamous_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $divorced_registered = $divorced_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $widowed_registered = $widowed_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $cohabating_registered = $cohabating_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $unavailable_registered = $unavailable_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $notapplicable_registered = $notapplicable_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $polygamous_registered = $polygamous_registered->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }

        if (!empty($selected_facilites)) {
            $consented_nine = $consented_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $consented_forteen = $consented_forteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $consented_nineteen = $consented_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $consented_twenty_four = $consented_twenty_four->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $consented_over_twenty_five = $consented_over_twenty_five->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $registered_nine = $registered_nine->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $registered_forteen = $registered_forteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $registered_nineteen = $registered_nineteen->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $registered_twenty_four = $registered_twenty_four->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $registered_over_twenty_five = $registered_over_twenty_five->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $single_consented = $single_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $monogamous_consented = $monogamous_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $divorced_consented = $divorced_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $widowed_consented = $widowed_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $cohabating_consented = $cohabating_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $unavailable_consented = $unavailable_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $notapplicable_consented = $notapplicable_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $polygamous_consented = $polygamous_consented->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $single_registered = $single_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $monogamous_registered = $monogamous_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $divorced_registered = $divorced_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $widowed_registered = $widowed_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $cohabating_registered = $cohabating_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $unavailable_registered = $unavailable_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $notapplicable_registered = $notapplicable_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $polygamous_registered = $polygamous_registered->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }

        $data["consented_nine"]        = $consented_nine->count();
        $data["consented_forteen"]        = $consented_forteen->count();
        $data["consented_nineteen"]        = $consented_nineteen->count();
        $data["consented_twenty_four"]        = $consented_twenty_four->count();
        $data["consented_over_twenty_five"]        = $consented_over_twenty_five->count();
        $data["registered_nine"]        = $registered_nine->count();
        $data["registered_forteen"]        = $registered_forteen->count();
        $data["registered_nineteen"]        = $registered_nineteen->count();
        $data["registered_twenty_four"]        = $registered_twenty_four->count();
        $data["registered_over_twenty_five"]        = $registered_over_twenty_five->count();
        $data["single_consented"]        = $single_consented->count();
        $data["monogamous_consented"]        = $monogamous_consented->count();
        $data["divorced_consented"]        = $divorced_consented->count();
        $data["widowed_consented"]        = $widowed_consented->count();
        $data["cohabating_consented"]        = $cohabating_consented->count();
        $data["unavailable_consented"]        = $unavailable_consented->count();
        $data["notapplicable_consented"]        = $notapplicable_consented->count();
        $data["polygamous_consented"]        = $polygamous_consented->count();
        $data["single_registered"]        = $single_registered->count();
        $data["monogamous_registered"]        = $monogamous_registered->count();
        $data["divorced_registered"]        = $divorced_registered->count();
        $data["widowed_registered"]        = $widowed_registered->count();
        $data["cohabating_registered"]        = $cohabating_registered->count();
        $data["unavailable_registered"]        = $unavailable_registered->count();
        $data["notapplicable_registered"]        = $notapplicable_registered->count();
        $data["polygamous_registered"]        = $polygamous_registered->count();

        return $data;

    }
}
