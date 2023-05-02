<?php

namespace App\Http\Controllers;

date_default_timezone_set('Africa/Nairobi');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '2024M');

use Illuminate\Http\Request;
use App\Models\HighRisk;
use GuzzleHttp\Client;
use App\Models\PartnerFacility;
use App\Models\Content;
use App\Models\ClientOutgoing;
use App\Models\HighRiskNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HighRiskController extends Controller
{
    protected  $current_date;
    protected $httpresponse;

    public function __construct()
    {
        set_time_limit(0);
        $this->current_date = date("Y-m-d");
    }

    private function getAccessToken()
    {
        $accessToken = Cache::get('access_token');
        if (!$accessToken) {
            $client = new Client();
            $response = $client->post('https://auth2.kenyahmis.org:8443/connect/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'cdc',
                    'client_secret' => '7f11e3b4-5741-11ec-bf63-0242ac130002',
                    'scope' => 'pdapiv1'
                ]
            ]);
            $responseBody = json_decode($response->getBody(), true);
            $accessToken = $responseBody['access_token'];
            $expiresIn = $responseBody['expires_in'];
            Cache::put('access_token', $accessToken, now()->addSeconds($expiresIn - 60)); // store token for 1 minute less than expiration time
        }
        return $accessToken;
    }
    public function get_high_risk_clients()
    {
        // Set the endpoint URL
        // Create a GuzzleHttp client
        $client = new \GuzzleHttp\Client();



        //  $access_token = json_decode($response->getBody())->access_token;

        // Get the list of facilities
        $facilities = PartnerFacility::select('mfl_code')->orderBy('mfl_code', 'desc')->get();

        // Get an array of facility codes
        $get_facilities = $facilities->pluck('mfl_code')->toArray();

        // API endpoint URL
        $url = 'https://data.kenyahmis.org:9783/api/Dataset';

        // Set the page size
        $pageSize = 50;

        foreach ($get_facilities as $final_facility) {
            // Set the page number
            $pageNumber = 1;

            do {
                // Set the API parameters
                $params = [
                    'code' => 'FND',
                    'name' => 'predictions',
                    'siteCode' => $final_facility,
                    'pageNumber' => $pageNumber,
                    'pageSize' => $pageSize,
                ];

                // Build the API query string
                $queryString = http_build_query($params);
                $fullUrl = $url . '?' . $queryString;

                // Make the API call
                $response = $client->get($fullUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->getAccessToken(),
                    ],
                    'timeout' => 0,
                ]);

                // Decode the JSON response
                $data = json_decode($response->getBody(), true);

                // Loop through the extract and insert into the database
                // $records = array_filter($data['extract'], function ($record) {
                //     return $record['Description'] == 'High Risk';
                // });
                $all = array_filter($data['extract'], function ($record) {
                    return true;
                });
                $dataToInsert = array_map(function ($record) use ($final_facility) {
                    return [
                        'ccc_number' => $record['PatientCccNumber'],
                        'mfl_code' => $record['code'],
                        'risk_score' => $record['risk_score'],
                        'evaluation_date' => $record['EvaluationDate'],
                        'risk_description' => $record['Description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $all);



                $chunks = array_chunk($dataToInsert, 2000);

                foreach ($chunks as $chunk) {
                    $ccNumbers = array_column($chunk, 'ccc_number');

                    $existingRecords = DB::table('tbl_high_risk')->whereIn('ccc_number', $ccNumbers)->get();

                    foreach ($chunk as $insert) {
                        $existingRecord = $existingRecords->firstWhere('ccc_number', $insert['ccc_number']);

                        if ($existingRecord) {
                            DB::table('tbl_high_risk')->where('id', $existingRecord->id)->update($insert);
                        } else {
                            if ($insert['risk_description'] == 'High Risk') {
                                DB::table('tbl_high_risk')->insert($insert);
                            }
                        }
                    }
                }


                $pageNumber++;

                // looping while there are more pages of data
            } while ($data['pageNumber'] < $data['pageCount']);
        }
    }

    // public function getAllData()
    // {
    //     // Set the endpoint URL
    //     // Create a GuzzleHttp client
    //     $client = new \GuzzleHttp\Client();



    //     //  $access_token = json_decode($response->getBody())->access_token;

    //     // Get the list of facilities
    //     $facilities = PartnerFacility::select('mfl_code')->orderBy('mfl_code', 'desc')->get();

    //     // Get an array of facility codes
    //     $get_facilities = $facilities->pluck('mfl_code')->toArray();

    //     // API endpoint URL
    //     $url = 'https://data.kenyahmis.org:9783/api/Dataset';

    //     // Set the page size
    //     $pageSize = 50;

    //     foreach ($get_facilities as $final_facility) {
    //         // Set the page number
    //         $pageNumber = 1;

    //         do {
    //             // Set the API parameters
    //             $params = [
    //                 'code' => 'FND',
    //                 'name' => 'predictions',
    //                 'siteCode' => '27408',
    //                 'pageNumber' => $pageNumber,
    //                 'pageSize' => $pageSize,
    //             ];

    //             // Build the API query string
    //             $queryString = http_build_query($params);
    //             $fullUrl = $url . '?' . $queryString;

    //             // Make the API call
    //             $response = $client->get($fullUrl, [
    //                 'headers' => [
    //                     'Authorization' => 'Bearer ' . $this->getAccessToken(),
    //                 ],
    //                 'timeout' => 0,
    //             ]);

    //             // Decode the JSON response
    //             $data = json_decode($response->getBody(), true);
    //             // Loop through the extract and insert into the database
    //             // $records = array_filter($data['extract'], function ($record) {
    //             //     return $record['Description'] == 'High Risk';
    //             // });
    //             $all = array_filter($data['extract'], function ($record) {
    //                 return true;
    //             });
    //             $dataToInsert = array_map(function ($record) use ($final_facility) {
    //                 return [
    //                     'ccc_number' => $record['PatientCccNumber'],
    //                     'mfl_code' => $record['code'],
    //                     'risk_score' => $record['risk_score'],
    //                     'evaluation_date' => $record['EvaluationDate'],
    //                     'risk_description' => $record['Description'],
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }, $all);



    //             $chunks = array_chunk($dataToInsert, 2000);

    //             foreach ($chunks as $chunk) {
    //                 $ccNumbers = array_column($chunk, 'ccc_number');

    //                 $existingRecords = DB::table('tbl_high_risk')->whereIn('ccc_number', $ccNumbers)->get();

    //                 foreach ($chunk as $insert) {
    //                     $existingRecord = $existingRecords->firstWhere('ccc_number', $insert['ccc_number']);

    //                     if ($existingRecord) {
    //                         DB::table('tbl_high_risk')->where('id', $existingRecord->id)->update($insert);
    //                     } else {
    //                         if ($insert['risk_description'] == 'High Risk') {

    //                             if (DB::table('tbl_high_risk')->insert($insert)) {
    //                                 echo 'success';
    //                             } else {
    //                                 echo 'tired';
    //                             }
    //                         }
    //                     }
    //                 }
    //             }


    //             $pageNumber++;

    //             // looping while there are more pages of data
    //         } while ($data['pageNumber'] < $data['pageCount']);
    //     }
    // }


    private function send_message($source, $destination, $msg)
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

    public function task()
    {
        try {
            $client = HighRiskNotification::all();


            foreach ($client as $value) {
                $client_name = $value->client_name;
                $risk_description = $value->risk_description;
                $language_id = $value->language_id;
                $phone_no = $value->phone_no;
                $txt_time = $value->txt_time + 1;
                $smsenable = $value->smsenable;
                $appntmnt_date = $value->appntmnt_date;
                $consented = $value->consented;
                $appointment_id = $value->appointment_id;
                $client_id = $value->client_id;
                $no_of_days = $value->no_of_days;

                if (DB::table('tbl_clnt_outgoing')
                    ->where('message_type_id', 9)
                    ->where('clnt_usr_id', $client_id)
                    ->where(function ($query) {
                        $query->whereDate('created_at',  $this->current_date)
                            ->orwhereDate('updated_at',  $this->current_date);
                    })
                    ->doesntExist()
                ) {
                    if ($no_of_days == 30 || $no_of_days == 21) {
                        $logic_flow_id = 23;
                    } else {
                        $logic_flow_id = 22;
                    }
                    $message = DB::table('tbl_content')
                        ->where('message_type_id', 9)
                        ->where('identifier', $logic_flow_id)
                        ->where('language_id', $language_id)
                        ->get()
                        ->take(1);

                    //check if a notification is already sent. If not send it.

                    foreach ($message as $sms) {

                        $content = $sms->content;
                        $content_id = $sms->id;

                        $today = date("Y-m-d H:i:s");
                        $new_msg = str_replace("XXX", $client_name, $content);


                        $status = "Not Sent";
                        $responded = "No";


                        if ($smsenable === 'Yes' || $smsenable === 'YES' && trim($new_msg) != '') {
                            $source = 40149;
                            $outgoing = array(
                                'destination' => $phone_no,
                                'msg' => $new_msg,
                                'responded' => $responded,
                                'status' => $status,
                                'message_type_id' => 9,
                                'source' => $source,
                                'clnt_usr_id' => $client_id,
                                'appointment_id' => $appointment_id,
                                'no_of_days' => $no_of_days,
                                'recepient_type' => 'Client',
                                'content_id' => $content_id,
                                'created_at' => $today,
                                'created_by' => '1'
                            );

                            $this->sms_outgoing_insert($outgoing);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    private function sms_outgoing_insert($record)
    {
        try {
            $sms = new ClientOutgoing;
            $sms->destination = $record['destination'];
            $sms->msg = $record['msg'];
            $sms->responded = $record['responded'];
            $sms->status = $record['status'];
            $sms->message_type_id = $record['message_type_id'];
            $sms->source = $record['source'];
            $sms->clnt_usr_id = $record['clnt_usr_id'];
            $sms->appointment_id = $record['appointment_id'];
            $sms->no_of_days = $record['no_of_days'];
            $sms->recepient_type = $record['recepient_type'];
            $sms->content_id = $record['content_id'];
            $sms->created_by = $record['created_by'];
            $sms->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function sender()
    {
        try {
            //get all outgoing smses
            $messages = ClientOutgoing::where('message_type_id', 9)->get();

            foreach ($messages as $message) {
                $clnt_outgoing_id = $message->id;
                $source = $message->source;
                $destination = $message->destination;
                $msg = $message->msg;
                $status = $message->status;
                $responded = $message->responded;
                $content_id = $message->content_id;
                $message_type_id = $message->message_type_id;
                $clnt_usr_id = $message->clnt_usr_id;
                $created_at = $message->created_at;
                $recepient_type = $message->recepient_type;

                if ($status == "Not Sent") {

                    //check if a similar message already sent.
                    if (DB::table('tbl_clnt_outgoing')
                        ->where('msg', 'like', '%' . $msg . '%')
                        ->where('destination', $destination)
                        ->where('status', 'Sent')
                        ->whereRaw('created_at between (CURDATE() - INTERVAL 1 DAY) AND (CURDATE() + INTERVAL 1 DAY) ')
                        ->doesntExist()
                    ) //Message has not been sent, send the  current message
                    {

                        //Number process , Append conutry code prefix on the  phone no if its not appended e.g 0712345678 => 254712345678
                        $mobile = substr($destination, -9);
                        $len = strlen($mobile);
                        if ($len < 10) {
                            $destination = "254" . $mobile;
                        }

                        //call sms service
                        $result = $this->send_message($source, $destination, $msg);

                        $status = $result['status'];
                        $messageid = '';
                        $cost = 0;

                        foreach ($result as $row) {
                            if ($status == 'success') {
                                foreach ($result['data'] as $data) {
                                    foreach ($data['Recipients'] as $Recipient) {
                                        $messageid = $Recipient['messageId'];
                                        $cost = $Recipient['cost'];
                                    }
                                }
                            }

                            //update the sent message with the sms cost and send status
                            $sms = ClientOutgoing::find($clnt_outgoing_id);
                            $sms->status = $status;
                            $sms->cost = $cost;
                            $sms->message_id = $messageid;
                            $sms->save();
                            //dd($result);

                        }
                    } else //delete the current duplicate message
                    {
                        ClientOutgoing::destroy($clnt_outgoing_id);
                    }
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
