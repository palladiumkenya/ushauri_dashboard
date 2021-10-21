<?php

use AfricasTalking\SDK\AfricasTalking;
use App\Models\Sender;
use Illuminate\Support\Facades\Log;

function send_sms($to, $message)
{
    $username = "Ushauri_KE";
    $apiKey = "972bdb6f53893725b09eaa3581a264ebf77b0e816ef5e9cb9f29e0c7738e41c1";
    $AT       = new AfricasTalking($username, $apiKey);

    // Get one of the services
    $sms      = $AT->sms();
    // Use the service
    $send   = $sms->send([
                    'from' => '40149',
                    'to'      => $to,
                    'message' => $message
                ]);


    if ($send) {
        $sent = new Sender;
        $sent->number = $to;
        $sent->message = $message;
        foreach ($send['data'] as $data) {
            $dts = $data->Recipients;
            foreach ($dts as $dt) {
                date_default_timezone_set('UTC');
                $date = date('Y-m-d H:i:s', time());

                $sent->status = $dt->status;
                $sent->statusCode = $dt->statusCode;
                $sent->messageId = $dt->messageId;
                $sent->cost = $dt->cost;
                $sent->updated_at = $date;
                $sent->created_at = $date;
            }
        }
        $sent->save();

        Log::info($sent);
    }
}

