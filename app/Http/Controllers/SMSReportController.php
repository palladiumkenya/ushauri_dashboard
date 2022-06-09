<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientOutgoing;
use App\Models\Partner;
use DB;

class SMSReportController extends Controller
{
    public function index()
    {
        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        return view('sms.sms_report', compact('all_partners'));
    }

    public function success_sms()
    {
        $all_partners = Partner::where('status', '=', 'Active')->pluck('name', 'id');

        $delivered_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw('count(tbl_clnt_outgoing.callback_status) as total'))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Success')
            ->groupBy('tbl_partner.name')
            // ->groupBy(DB::raw("DATE_FORMAT(tbl_clnt_outgoing.created_at, '%m-%Y')"))
            ->get();
        // dd($delivered_partners);
        $failed_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw('count(tbl_clnt_outgoing.callback_status) as total'))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->groupBy('tbl_partner.name')
            ->get();

        $cost_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->join('tbl_partner', 'tbl_partner_facility.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 0) as total_cost"))
            ->groupBy('tbl_partner.name')
            ->get();

        $cost_counties = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
            ->select('tbl_county.name', DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 0) as total_cost"))
            ->groupBy('tbl_county.name')
            ->get();

        //dd($cost_counties);

        $success = ClientOutgoing::select("callback_status")
            ->where('callback_status', '=', 'Success')
            ->count();


        $failed_blacklist = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->count();
        $failed_absent = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'AbsentSubscriber')
            ->count();

        $failed_inactive = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->count();

        $failed_deliveryfailure = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->count();

        // $rejected_blacklist = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInBlacklist')
        //     ->count();

        // $rejected_inactive = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInactive')
        //     ->count();

        // $rejected_deliveryfailure = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'DeliveryFailure')
        //     ->count();

        // cost calculation for all the status
        $success_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Success')
            ->pluck('total_cost');
        $failed_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->pluck('total_cost');
        $failed_absent_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'AbsentSubscriber')
            ->pluck('total_cost');
        $failed_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->pluck('total_cost');
        $failed_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->pluck('total_cost');

        // $rejected_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInBlacklist')
        //     ->pluck('total_cost');
        // $rejected_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInactive')
        //     ->pluck('total_cost');
        // $rejected_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'DeliveryFailure')
        //     ->pluck('total_cost');

//         $rejected_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'UserInBlacklist')
//             ->pluck('total_cost');
//         $rejected_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'UserInactive')
//             ->pluck('total_cost');
//         $rejected_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'DeliveryFailure')
//             ->pluck('total_cost');
        $total_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->whereNotNull('callback_status')
            ->pluck('total_cost');


        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');

        return view('sms.sms_report', compact(
            'success',
            'all_partners',
            'failed_blacklist',
            'failed_absent',
            'failed_inactive',
            'failed_deliveryfailure',
            'success_cost',
            'failed_blacklist_cost',
            'failed_absent_cost',
            'failed_inactive_cost',
            'failed_deliveryfailure_cost',
            'delivered_partners',
            'failed_partners',
            'cost_partners',
            'cost_counties',
            'total_cost'
        ));
    }

    public function filter_sms(Request $request)
    {
        $data = [];

        $selected_partners = $request->partners;
        $selected_counties = $request->counties;
        $selected_subcounties = $request->subcounties;
        $selected_facilites = $request->facilities;

        $success = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("COUNT(tbl_clnt_outgoing.status)"))
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Success');

        $failed_blacklist = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_clnt_outgoing.status')
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInBlacklist');

        $failed_absent = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_clnt_outgoing.status')
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'AbsentSubscriber');

        $failed_inactive = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_clnt_outgoing.status')
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInactive');

        $failed_deliveryfailure = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select('tbl_clnt_outgoing.status')
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'DeliveryFailure');

        // $rejected_blacklist = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select('tbl_clnt_outgoing.status')
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInBlacklist');

        // $rejected_inactive = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select('tbl_clnt_outgoing.status')
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInactive');

        // $rejected_deliveryfailure = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select('tbl_clnt_outgoing.status')
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'DeliveryFailure');

        // cost calculation for all the status
        $success_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Success')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_blacklist_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInBlacklist')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_absent_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'AbsentSubscriber')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_inactive_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInactive')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_deliveryfailure_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->where('tbl_clnt_outgoing.failure_reason', '=', 'DeliveryFailure')
            ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
            ->pluck('total_cost');
        // $rejected_blacklist_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInBlacklist')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->pluck('total_cost');
        // $rejected_inactive_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'UserInactive')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->pluck('total_cost');
        // $rejected_deliveryfailure_cost = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
        //     ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
        //     ->select(\DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 2) as total_cost"))
        //     ->where('tbl_clnt_outgoing.callback_status', '=', 'Rejected')
        //     ->where('tbl_clnt_outgoing.failure_reason', '=', 'DeliveryFailure')
        //     ->whereDate('tbl_clnt_outgoing.updated_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.updated_at', '<=', date($request->to))
        //     ->pluck('total_cost');

        if (!empty($selected_partners)) {
            $success = $success->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_blacklist = $failed_blacklist->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_inactive = $failed_inactive->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_deliveryfailure = $failed_deliveryfailure->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_blacklist = $rejected_blacklist->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_inactive = $rejected_inactive->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_deliveryfailure = $rejected_deliveryfailure->where('tbl_partner_facility.partner_id', $selected_partners);
            $success_cost = $success_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_blacklist_cost = $failed_blacklist_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_inactive_cost = $failed_inactive_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            $failed_deliveryfailure_cost = $failed_deliveryfailure_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_blacklist_cost = $rejected_blacklist_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_inactive_cost = $rejected_inactive_cost->where('tbl_partner_facility.partner_id', $selected_partners);
            // $rejected_deliveryfailure_cost = $rejected_deliveryfailure_cost->where('tbl_partner_facility.partner_id', $selected_partners);
        }
        if (!empty($selected_counties)) {
            $success = $success->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_blacklist = $failed_blacklist->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_inactive = $failed_inactive->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_deliveryfailure = $failed_deliveryfailure->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_blacklist = $rejected_blacklist->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_inactive = $rejected_inactive->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_deliveryfailure = $rejected_deliveryfailure->where('tbl_partner_facility.county_id', $selected_counties);
            $success_cost = $success_cost->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_blacklist_cost = $failed_blacklist_cost->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_inactive_cost = $failed_inactive_cost->where('tbl_partner_facility.county_id', $selected_counties);
            $failed_deliveryfailure_cost = $failed_deliveryfailure_cost->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_blacklist_cost = $rejected_blacklist_cost->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_inactive_cost = $rejected_inactive_cost->where('tbl_partner_facility.county_id', $selected_counties);
            // $rejected_deliveryfailure_cost = $rejected_deliveryfailure_cost->where('tbl_partner_facility.county_id', $selected_counties);
        }
        if (!empty($selected_subcounties)) {
            $success = $success->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_blacklist = $failed_blacklist->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_inactive = $failed_inactive->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_deliveryfailure = $failed_deliveryfailure->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_blacklist = $rejected_blacklist->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_inactive = $rejected_inactive->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_deliveryfailure = $rejected_deliveryfailure->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $success_cost = $success_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_blacklist_cost = $failed_blacklist_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_inactive_cost = $failed_inactive_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            $failed_deliveryfailure_cost = $failed_deliveryfailure_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_blacklist_cost = $rejected_blacklist_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_inactive_cost = $rejected_inactive_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
            // $rejected_deliveryfailure_cost = $rejected_deliveryfailure_cost->where('tbl_partner_facility.sub_county_id', $selected_subcounties);
        }
        if (!empty($selected_facilites)) {
            $success = $success->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_blacklist = $failed_blacklist->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_inactive = $failed_inactive->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_deliveryfailure = $failed_deliveryfailure->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_blacklist = $rejected_blacklist->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_inactive = $rejected_inactive->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_deliveryfailure = $rejected_deliveryfailure->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $success_cost = $success_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_blacklist_cost = $failed_blacklist_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_inactive_cost = $failed_inactive_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            $failed_deliveryfailure_cost = $failed_deliveryfailure_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_blacklist_cost = $rejected_blacklist_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_inactive_cost = $rejected_inactive_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
            // $rejected_deliveryfailure_cost = $rejected_deliveryfailure_cost->where('tbl_partner_facility.mfl_code', $selected_facilites);
        }

        $data["success"]        = $success->count();
        $data["failed_blacklist"]        = $failed_blacklist->count();
        $data["failed_inactive"]        = $failed_inactive->count();
        $data["failed_deliveryfailure"]        = $failed_deliveryfailure->count();
        // $data["rejected_blacklist"]        = $rejected_blacklist->count();
        // $data["rejected_inactive"]        = $rejected_inactive->count();
        // $data["rejected_deliveryfailure"]        = $rejected_deliveryfailure->count();
        $data["success_cost"]        = $success_cost;
        $data["failed_blacklist_cost"]        = $failed_blacklist_cost;
        $data["failed_inactive_cost"]        = $failed_inactive_cost;
        $data["failed_deliveryfailure_cost"]        = $failed_deliveryfailure_cost;
        // $data["rejected_blacklist_cost"]        = $rejected_blacklist_cost;
        // $data["rejected_inactive_cost"]        = $rejected_inactive_cost;
        // $data["rejected_deliveryfailure_cost"]        = $rejected_deliveryfailure_cost;

        return $data;
    }

    public function filtering_sms(Request $request)
    {
        $delivered_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner', 'tbl_client.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw('count(tbl_clnt_outgoing.callback_status) as total'))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Success')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->groupBy('tbl_partner.name')
            // ->groupBy(DB::raw("DATE_FORMAT(tbl_clnt_outgoing.created_at, '%m-%Y')"))
            ->get();
        // dd($delivered_partners);
        $failed_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner', 'tbl_client.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw('count(tbl_clnt_outgoing.callback_status) as total'))
            ->where('tbl_clnt_outgoing.callback_status', '=', 'Failed')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->groupBy('tbl_partner.name')
            ->get();

        $cost_partners = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner', 'tbl_client.partner_id', '=', 'tbl_partner.id')
            ->select('tbl_partner.name', DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 0) as total_cost"))
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->groupBy('tbl_partner.name')
            ->get();

        $cost_counties = ClientOutgoing::join('tbl_client', 'tbl_clnt_outgoing.clnt_usr_id', '=', 'tbl_client.id')
            ->join('tbl_partner_facility', 'tbl_client.mfl_code', '=', 'tbl_partner_facility.mfl_code')
            ->join('tbl_county', 'tbl_partner_facility.county_id', '=', 'tbl_county.id')
            ->select('tbl_county.name', DB::raw("ROUND(SUM(SUBSTRING(tbl_clnt_outgoing.cost, 5)), 0) as total_cost"))
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->groupBy('tbl_county.name')
            ->get();

        //dd($cost_counties);

        $success = ClientOutgoing::select("callback_status")
            ->where('callback_status', '=', 'Success')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->count();


        $failed_blacklist = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->count();
        $failed_absent = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'AbsentSubscriber')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->count();

        $failed_inactive = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->count();

        $failed_deliveryfailure = ClientOutgoing::select('callback_status')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->count();

        // $rejected_blacklist = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInBlacklist')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->count();

        // $rejected_inactive = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInactive')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->count();

        // $rejected_deliveryfailure = ClientOutgoing::select('callback_status')
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'DeliveryFailure')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->count();

        // cost calculation for all the status
        $success_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Success')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_absent_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'AbsentSubscriber')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');
        $failed_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');

        // $rejected_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInBlacklist')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->pluck('total_cost');
        // $rejected_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'UserInactive')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->pluck('total_cost');
        // $rejected_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
        //     ->where('callback_status', '=', 'Rejected')
        //     ->where('failure_reason', '=', 'DeliveryFailure')
        //     ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
        //     ->pluck('total_cost');

//         $rejected_blacklist_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'UserInBlacklist')
//             ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
//             ->pluck('total_cost');
//         $rejected_inactive_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'UserInactive')
//             ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
//             ->pluck('total_cost');
//         $rejected_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
//             ->where('callback_status', '=', 'Rejected')
//             ->where('failure_reason', '=', 'DeliveryFailure')
//             ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
//             ->pluck('total_cost');
        $total_cost = ClientOutgoing::select(\DB::raw("ROUND(SUM(SUBSTRING(cost, 5)), 0) as total_cost"))
            ->whereNotNull('callback_status')
            ->whereDate('tbl_clnt_outgoing.created_at', '>=', date($request->from))->whereDate('tbl_clnt_outgoing.created_at', '<=', date($request->to))
            ->pluck('total_cost');


        return view('sms.sms_report', compact(
            'success',
            'failed_blacklist',
            'failed_absent',
            'failed_inactive',
            'failed_deliveryfailure',
            'success_cost',
            'failed_blacklist_cost',
            'failed_absent_cost',
            'failed_inactive_cost',
            'failed_deliveryfailure_cost',
            'delivered_partners',
            'failed_partners',
            'cost_partners',
            'cost_counties',
            'total_cost'
        ));
    }
}
