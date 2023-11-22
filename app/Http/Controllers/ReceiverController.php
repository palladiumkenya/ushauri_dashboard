<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use\App\Models\UshauriInbox;
use\App\Models\UshauriOutbox;
use Log;


class ReceiverController extends Controller
{
    public function index(Request $request)
    {
        if ($request->to == '40149') {

            // dd($request->all());

            $inbox = new UshauriInbox;

            $inbox->destination = $request->to;
            $inbox->source = $request->from;
            $inbox->msg = $request->text;
            $inbox->receivedtime = $request->date;
            $inbox->reference = $request->id;
            $inbox->LinkId = $request->linkId;

            $inbox->save();

            $lastID1 = $inbox->id;
            $task = 1;
            $this->task($task, $lastID1);
        }
    }

    function task($task, $LastInsertId)
    {
        Log::info("ID: " . $LastInsertId . ", TASK: " . $task);
        switch ($task) {
            case 1:

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, "http://ushaurinode.localhost/receiver/$LastInsertId");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_exec($ch);

                curl_close($ch);
                echo 'Done task 1';
                break;
            default:
                break;
        }
    }

    public function ushauri_callback(Request $request)
    {

        //check incoming id and update single sms with telco status

        $updateDetails = [
            'callback_status' => $request->get('status'),
            'failure_reason' => $request->get('failureReason')
        ];

        // return $request;

        $sms = UshauriOutbox::where('message_id', $request->id)->first();
        if ($sms) {
            $sms = UshauriOutbox::where('message_id', $request->id)
                ->update($updateDetails);
        }
    }
}
