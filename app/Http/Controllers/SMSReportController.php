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
        $success = ClientOutgoing::select(\DB::raw("COUNT(status) as count"))
            ->where('callback_status', '=', 'Success')
            ->pluck('count');

        $failed_blacklist = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->count();

        $failed_inactive = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->count();

        $failed_deliveryfailure = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->count();

        $rejected_blacklist = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->count();

        $rejected_inactive = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'UserInactive')
            ->count();

        $rejected_deliveryfailure = ClientOutgoing::select('*')
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->count();

        // cost calculation for all the status
        $success_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Success')
            ->pluck('total_cost');
        $failed_blacklist_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->pluck('total_cost');
        $failed_inactive_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'UserInactive')
            ->pluck('total_cost');
        $failed_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Failed')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->pluck('total_cost');
        $rejected_blacklist_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'UserInBlacklist')
            ->pluck('total_cost');
        $rejected_inactive_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'UserInactive')
            ->pluck('total_cost');
        $rejected_deliveryfailure_cost = ClientOutgoing::select(\DB::raw("SUM(SUBSTRING(cost, 5)) as total_cost"))
            ->where('callback_status', '=', 'Rejected')
            ->where('failure_reason', '=', 'DeliveryFailure')
            ->pluck('total_cost');

        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');

        return view('sms.sms_report', compact('success', 'all_partners', 'failed_blacklist', 'failed_inactive', 'failed_deliveryfailure', 'rejected_blacklist', 'rejected_inactive', 'rejected_deliveryfailure',
        'success_cost', 'failed_blacklist_cost', 'failed_inactive_cost', 'failed_deliveryfailure_cost', 'rejected_blacklist_cost', 'rejected_inactive_cost', 'rejected_deliveryfailure_cost'));
    }

    public function filter_sms()
    {}
}
