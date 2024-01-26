<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DirectoryController extends Controller
{
    public function directory(Request $request, $facility)
    {
        $mfl = implode('', range(0, 9));

        $apiUrl = env('ART_URL') . "directory/{$mfl}/{$facility}";

        $response = Http::get($apiUrl);

        if ($response->successful()) {

            $apiData = $response->json();
            return response()->json($apiData);
        } else {

            $errorData = $response->json();
            return response()->json(['error' => 'API request failed'], $response->status());
        }
    }
}
