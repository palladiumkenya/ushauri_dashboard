<?php

namespace App\Http\Controllers;

date_default_timezone_set('Africa/Nairobi');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SendSMS;
use App\Models\Sender;
use App\Models\Client;
use App\Models\Group;
use App\Models\Gender;
use App\Models\Facility;
use App\Models\Time;
use App\Models\User;
use App\Models\PartnerFacility;
use Carbon\Carbon;
use Session;
use Auth;


class BroadcastController extends Controller
{

    public function broadcast_form()
    {
        $facilities = Facility::all();

        $u_facilities = Facility::where('code', Auth::user()->facility_id)->get();

        $p_facilities = Facility::join('tbl_partner_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->select(
                'tbl_partner_facility.mfl_code',
                'tbl_partner_facility.partner_id',
                'tbl_master_facility.name'
            )
            ->where('partner_id', Auth::user()->partner_id)
            ->get();

        $groups = Group::all()->where('status', '=', 'Active');

        $genders = Gender::all()->where('status', '=', 'Active');
        $time = Time::all();

        $data = array(
            'facilities' => $facilities,
            'groups' => $groups,
            'genders' => $genders,
            'time' => $time
        );

        $p_data = array(
            'facilities' => $p_facilities,
            'groups' => $groups,
            'genders' => $genders,
            'time' => $time
        );

        $u_data = array(
            'facilities' => $u_facilities,
            'groups' => $groups,
            'genders' => $genders,
            'time' => $time
        );

        if (Auth::user()->access_level == 'Facility') {

            return view('broadcast.facility_broadcast')->with($u_data);
        } else if (Auth::user()->access_level == 'Partner') {

            return view('broadcast.partner_broadcast')->with($p_data);
        } else if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            return view('broadcast.broadcast')->with($data);
        }
    }

    public function broadcast_page()
    {
        return view('broadcast.broadcast_page');
    }

    public function broadcast_user()
    {
        $facilities = Facility::join('tbl_users', 'tbl_users.facility_id', '=', 'tbl_master_facility.code')->select('tbl_master_facility.code', 'tbl_master_facility.name')->groupBy('tbl_master_facility.name')->get();

        $p_facilities = Facility::join('tbl_partner_facility', 'tbl_partner_facility.mfl_code', '=', 'tbl_master_facility.code')
            ->select(
                'tbl_partner_facility.mfl_code',
                'tbl_partner_facility.partner_id',
                'tbl_master_facility.name'
            )
            ->where('partner_id', Auth::user()->partner_id)
            ->get();
        $data = array(
            'facilities' => $facilities
        );
        $p_data = array(
            'p_facilities' => $p_facilities
        );

        if (Auth::user()->access_level == 'Partner') {

            return view('broadcast.broadcast_userpartner')->with($p_data);
        } else if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            return view('broadcast.broadcast_user')->with($data);
        }
    }

    public function sendSMS_user(Request $request)
    {
        if (Auth::user()->access_level == 'Partner') {

            // $request->validate([
            //     'mfl_code' => 'required',
            //     'message' => 'required'
            // ]);

            $users = User::where('status', '=', 'Active')->where('access_level', '=', 'Facility')->where('partner_id', Auth::user()->partner_id)->get();

            // if ($users->count() == 0)
            //     continue;


            foreach ($users as $user) {

                $dest = $user->phone_no;

                $msg = $request->message;

                SendSMS::dispatch($dest, $msg);
            }

            Session::flash('statuscode', 'success');
            return back()->with('status', 'Message Successfully Sent To The Users.');

            // return back();

        } else if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            // $request->validate([
            //     'mfl_code' => 'required',
            //     'message' => 'required'
            // ]);

            $users = User::where('status', '=', 'Active')->where('access_level', '=', 'Facility')->get();

            // if ($users->count() == 0)
            //     continue;

            foreach ($users as $user) {

                $dest = $user->phone_no;

                $msg = $request->message;

                SendSMS::dispatch($dest, $msg);
            }

            Session::flash('statuscode', 'success');
            return back()->with('status', 'Message Successfully Sent To The Users.');
        }
    }

    public function sendSMS(Request $request)
    {

        if (Auth::user()->access_level == 'Facility') {

            $request->validate([
                'groups' => 'required',
                'genders' => 'required',
                'message' => 'required'
            ]);

            $facility = Facility::where('code', Auth::user()->facility_id)->pluck('code')->first();

            foreach ($request['groups'] as $group_id) {

                $group = Group::find($group_id);

                if (is_null($group))
                    continue;

                foreach ($request['genders'] as $gender_id) {

                    $gender = Gender::find($gender_id);

                    $clients = Client::where('mfl_code', $facility)->where('group_id', '=', $group->id)->where('gender', $gender->id)->get();

                    if ($clients->count() == 0)
                        continue;

                    foreach ($clients as $client) {

                        $dest = $client->phone_no;

                        $msg = $request->message;

                        SendSMS::dispatch($dest, $msg);

                        // $sender = new SenderController;

                        // $sender->send($dest, $msg);

                    }
                }
            }
            Session::flash('statuscode', 'success');
            return back()->with('status', 'Message Successfully Sent.');

            // return back();

        } else if (Auth::user()->access_level == 'Partner') {

            $request->validate([
                'mfl_code' => 'required',
                'groups' => 'required',
                'genders' => 'required',
                'message' => 'required'
            ]);


            foreach ($request['groups'] as $group_id) {

                $group = Group::find($group_id);

                if (is_null($group))
                    continue;

                foreach ($request['genders'] as $gender_id) {

                    $gender = Gender::find($gender_id);

                    $clients = Client::where('mfl_code', $request->mfl_code)->where('group_id', '=', $group->id)->where('gender', $gender->id)->get();

                    if ($clients->count() == 0)
                        continue;

                    foreach ($clients as $client) {

                        $dest = $client->phone_no;

                        $msg = $request->message;

                        SendSMS::dispatch($dest, $msg);
                    }
                }
            }
            Session::flash('statuscode', 'success');
            return back()->with('status', 'Message Successfully Sent.');

            // return back();

        } else if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Donor') {

            $request->validate([
                'groups' => 'required',
                'genders' => 'required',
                'message' => 'required'
            ]);

            foreach ($request['groups'] as $group_id) {

                $group = Group::find($group_id);

                if (is_null($group))
                    continue;

                foreach ($request['genders'] as $gender_id) {

                    $gender = Gender::find($gender_id);

                    $clients = Client::where('group_id', '=', $group->id)->where('gender', $gender->id)->get();

                    if ($clients->count() == 0)
                        continue;

                    foreach ($clients as $client) {

                        $dest = $client->phone_no;

                        $msg = $request->message;

                        if (Str::length($dest) >= 10) {

                            SendSMS::dispatch($dest, $msg);
                        }
                    }
                }
            }
            Session::flash('statuscode', 'success');
            return back()->with('status', 'Message Successfully Sent.');
            //return back();

        }
    }
}
