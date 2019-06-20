<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function search(Request $request) {
        $searchTerm = $request->input('q');
        $limit = intval($request->input('limit', '25'));
        $offset = intval($request->input('offset', '0'));
        $searchLog = [
            'ip' => $request->ip(),
            'action' => 'search',
            'data' => $searchTerm
        ];

        if ($user = $request->user()) {
            $searchLog['user_id'] = $user->id;
        }
        
        Log::create($searchLog);

        $giphyUrl = env('GIPHY_URL') . env('GIPHY_GIF_URI') . '/search?';
        $queries = http_build_query([
            'api_key' => env('GIPHY_KEY'),
            'q' => $searchTerm,
            'limit' => $limit,
            'offset' => $offset,
        ]);
        $result = \Requests::get("{$giphyUrl}{$queries}");

        if ($result->status_code === 200 && $result->success) {
            return response($result->body)
                ->header('Content-Type', 'application/json');
        } else {
            return response()->json([
                'error' => 'Something wrong happenned, please try again later'
            ], 500);
        }
    }

    public function history(Request $request) {
        $itemsPerPage = $request->input('limit', 15);

        return response()->json(
            Log::where('user_id', $request->user()->id)
                ->paginate($itemsPerPage)
        );
    }
}
