<?php

namespace App\Http\Controllers;
date_default_timezone_set('Africa/Nairobi');
use Illuminate\Http\Request;
use App\Models\HighRisk;
use GuzzleHttp\Client;
use App\Models\PartnerFacility;

class HighRiskController extends Controller
{
    public function get_high_risk_clients(Request $request)
    {

    }

    public function getAllData()
    {
        $siteCode = PartnerFacility::select('mfl_code')->get();
        $facilities = array();

        foreach($siteCode as $code) {
            $site = $code->mfl_code;
            array_push($facilities, $site);
        }
        $results= array_unique($facilities);

        //$a =  implode(',', $results);
        $a = 14063;


        $pageCount = 1;
        $pageNumber = 1;
        $pageSize = 50;
        $totalItemCount = 0;

        $token = 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjZDMjBBQTk4MEMyRUNEQjNCQkVCMjUzNzZCNjVCRURDRDkxNDMwODgiLCJ0eXAiOiJhdCtqd3QiLCJ4NXQiOiJiQ0NxbUF3dXpiTzc2eVUzYTJXLTNOa1VNSWcifQ.eyJuYmYiOjE2Nzk5NDc5NDgsImV4cCI6MTY3OTk1MTU0OCwiaXNzIjoiaHR0cHM6Ly9hdXRoMi5rZW55YWhtaXMub3JnOjg0NDMiLCJhdWQiOiJwZGFwaXYxIiwiY2xpZW50X2lkIjoiY2RjIiwic2NvcGUiOlsicGRhcGl2MSJdfQ.x-4y_YaiwwY0hNX0lvUw9cB5CRZ-JT71-4RZFTCeJQlljYXup8usnzVr8Fxe-acGDXTdf1tJHadVH00Fp4EZphQwwuuAGZarJozWGFB_PTm4DMeNzMZhpGafmK0WhTvA6-w-cFkkyx6BF_En7JS-52fKw3gGGxJXhwqXcz638vRb31epunmp9BIh3HGNh2a_O13_p1aeRaadvKWWK3a796qzsopipBARHJ7_jvAtAYI6tU6yyCRcdi3EHhvatH5sj-4z6iPOgvzB_g5HYEmP_NbL6ejL7CNaC08U1hTvs6pAxBRH_hg5JicbEEW7u1LqZBSqJttew6gaK_iWWuq7Bg';

        // Loop through the pages until all data is retrieved
        do {

            $url = "https://data.kenyahmis.org:9783/api/Dataset?code=FND&name=predictions&pageNumber=$pageNumber&pageSize=$pageSize&siteCode=$a ";

            $ch = curl_init();

            // Set CURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $token
            ));

            // Execute the CURL request and get the response
            $response = curl_exec($ch);
            curl_close($ch);

            // Decode the JSON response
            $responseData = json_decode($response, true);
            $totalItemCount = $responseData['totalItemCount'];
            $pageNumber++;
            $pageCount = ceil($totalItemCount / $pageSize);

            // Process the data retrieved from the API
            foreach ($responseData['extract'] as $item) {
               // dd($item['PatientCccNumber']);
                $res = new HighRisk;
                $res->ccc_number = $item['PatientCccNumber'];
                $res->mfl_code = $item['code'];
                $res->risk_score = $item['risk_score'];
                $res->evaluation_date = $item['EvaluationDate'];
                $res->risk_description = $item['Description'];

                if ($res->save()) {
                    return response(['status' => 'Success']);
                } else {
                    return response(['status' => 'Error']);
                }
            }
        } while ($pageNumber <= $pageCount);
    }
}
