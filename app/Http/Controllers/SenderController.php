<?php

namespace App\Http\Controllers;

use AfricasTalking\SDK\AfricasTalking;

use Illuminate\Http\Request;

class SenderController extends Controller
{
    public function send($to, $message)
    {
        $username = "Ushauri_KE";
        $apiKey = '972bdb6f53893725b09eaa3581a264ebf77b0e816ef5e9cb9f29e0c7738e41c1';
        $AT       = new AfricasTalking($username, $apiKey);

        // Get one of the services
        $sms      = $AT->sms();
        // Use the service
        $send   = $sms->send([
            'from' => '40149',
            'to'      => $to,
            'message' => $message
        ]);
        return $send['status'];
    }
}
