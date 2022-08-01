<?php

namespace App\Console\Commands;

use AfricasTalking\SDK\AfricasTalking;

use App\Http\Controllers\SenderController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Client;
use App\Models\Appointments;
use App\Models\Facility;
use App\Models\Ward;
use App\Models\Content;
use App\Models\ClientOutgoing;
use DB;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

class ClientReferral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:referral';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Transfered Clients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function send_message($source, $destination, $msg)
    {
        $key = env('SMS_SERVICE_KEY', '');
        $host = env('SMS_SERVICE_HOST', '');

        $this->httpresponse = Http::withoutVerifying()
            ->withHeaders(['api-token' => "$key"])
            ->post("$host", [
                'destination' => $destination,
                'msg' => $msg,
                'sender_id' => $destination,
                'gateway' => $source,

            ]);

        return json_decode($this->httpresponse->getBody(), true);
    }

    public function handle()
    {
        $client = Client::join('tbl_master_facility', 'tbl_client.mfl_code', '=', 'tbl_master_facility.code')
            ->leftJoin('tbl_ward', 'tbl_master_facility.Ward_id', '=', 'tbl_ward.id')
            ->join('tbl_appointment', 'tbl_client.id', '=', 'tbl_appointment.client_id')
            ->select(
                'tbl_client.clinic_number',
                'tbl_client.id as client_id',
                'tbl_client.phone_no',
                'tbl_client.client_type',
                'tbl_client.f_name',
                'tbl_client.language_id',
                'tbl_master_facility.name as facility',
                'tbl_ward.name as location',
                DB::raw('(CASE WHEN tbl_appointment.appntmnt_date > CURDATE() THEN tbl_appointment.appntmnt_date
                WHEN tbl_client.language_id = "1" THEN "huna siku ya cliniki"
                ELSE "no appointment" END) as appointment_date')
            )
            ->where('tbl_client.client_type', '=', 'Transfer')
            ->whereNotNull('tbl_client.phone_no')
            ->whereDate('tbl_client.updated_at', [now()->subMinutes(30), now()])
            ->groupBy('tbl_client.id')
            ->get();



        foreach ($client as $value) {
            $phone_no = $value->phone_no;
            $client_id = $value->client_id;
            $client_type = $value->client_type;
            $client_name = $value->f_name;
            $facility = $value->facility;
            $location = $value->location;
            $language = $value->language_id;
            $appointment_date = $value->appointment_date;

            $check_existence = ClientOutgoing::select('*')->where('message_type_id', '=', '2')
                ->where('clnt_usr_id', '=', $client_id)
                ->whereDate('created_at', '=', Carbon::Now())
                ->limit(1)->count();

            if ($check_existence > 0) {
                echo 'Message already sent to the client';
            } else {
                if (!empty($phone_no)) {

                    if ($language !== 2 || $language !== 1) {
                        $get_message = Content::select('*')->where('identifier', '=', '20')->where('language_id', '=', '2')->get();
                    } else {
                        $get_message = Content::select('*')->where('identifier', '=', '20')->where('language_id', '=', $language)->get();
                    }

                    foreach ($get_message as $value) {
                        $message = $value->content;
                        $content_id = $value->id;
                        $source = '40149';

                        $new_message = str_replace("XXX", $client_name, $message);
                        $facility_name = str_replace('FFF', $facility, $new_message);
                        $location_name = str_replace('LLL', $location, $facility_name);
                        $final_message = str_replace('YYY', $appointment_date, $location_name);

                        $save_outgoing = new ClientOutgoing;

                        $save_outgoing->destination = $phone_no;
                        $save_outgoing->msg = $final_message;
                        $save_outgoing->source = '40149';
                        $save_outgoing->responded = 'No';
                        $save_outgoing->status = 'Not Sent';
                        $save_outgoing->message_type_id = '2';
                        $save_outgoing->clnt_usr_id = $client_id;
                        $save_outgoing->recepient_type = 'Client';
                        $save_outgoing->content_id = $content_id;
                        // $save_outgoing->created_at = date("Y-m-d H:i:s");
                        $save_outgoing->created_by = '1';

                        if ($save_outgoing->save()) {
                            // $sender = new SenderController;
                            $sender = $this->send_message($source, $phone_no, $final_message);

                            echo json_encode($sender);
                        } else {
                            echo 'Could not send the message';
                        }
                    }
                } else {
                    echo 'Can not send to an empty phone number';
                }
            }
        }

        dd("Clients : {$client}");


        // return 0;
    }
}
