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
        $get_cost = ClientOutgoing::select('cost')
        ->get();
        $all_cost = str_replace("KES", "", $get_cost);

        $success_cost = ClientOutgoing::select(DB::raw("SUM(cost) as count"))
            ->where('callback_status', '=', 'Success')
            ->pluck('count');

        $all_partners = Partner::where('status', '=', 'Active')
            ->pluck('name', 'id');
        return view('sms.sms_report', compact('success', 'all_partners', 'failed_blacklist', 'failed_inactive', 'failed_deliveryfailure', 'rejected_blacklist', 'rejected_inactive', 'rejected_deliveryfailure', 'success_cost'));
    }
}
