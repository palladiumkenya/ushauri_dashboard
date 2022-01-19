<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientOutgoing;

class SMSReportController extends Controller
{
    // Failed Blacklist sms function

    public function failed_sms_blacklist()
    {
        $failed_blacklist = ClientOutgoing::select('*')->where('failure_reason', '=', 'UserInBlacklist')
        ->get();
    }
    // Failed Inactive sms function

    public function failed_sms_inactive()
    {
        $failed_inactive = ClientOutgoing::select('*')->where('failure_reason', '=', 'UserInactive')
        ->get();
    }
    // Failed Inactive sms function

    public function failed_sms_deliveryfailure()
    {
        $failed_deliveryfailure = ClientOutgoing::select('*')->where('failure_reason', '=', 'DeliveryFailure')
        ->get();
    }
    // Success sms
    public function success_sms()
    {
        $success = ClientOutgoing::select('*')->where('callback_status', '=', 'Success')
        ->get();
    }

    // Rejected sms
    public function rejected_sms()
    {}
}
