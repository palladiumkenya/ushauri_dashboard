<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DirectoryLog;

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
    public function directoryLog(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $resultCount = $request->input('result_count');

        // Save the log
        DirectoryLog::create([
            'search_term' => $searchTerm,
            'result_count' => $resultCount,
        ]);

        return response()->json(['status' => 'success']);
    }
}
