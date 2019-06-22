<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Favorite;
use App\Log;

class FavoriteController extends Controller
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
    public function getAll(Request $request) {
        $itemsPerPage = $request->input('limit', 15);

        return response()->json(
            Favorite::where('user_id', $request->user()->id)
                ->paginate($itemsPerPage)
        );
    }

    public function create(Request $request) {
        $gifId = $request->input('gif_id');

        if (!$gifId) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $giphyUrl = env('GIPHY_URL') . env('GIPHY_GIF_URI') . "/{$gifId}?";
        $queries = http_build_query([
            'api_key' => env('GIPHY_KEY')
        ]);
        $result = \Requests::get("{$giphyUrl}{$queries}");

        if ($result->status_code === 200 && $result->success) {
            $body = json_decode($result->body);
            $gif = json_encode($body->data);

            try {
                DB::beginTransaction();

                $favorite = Favorite::create([
                    'gif_id' => $gifId,
                    'gif' => $gif,
                    'user_id' => $request->user()->id
                ]);

                Log::create([
                    'ip' => $request->ip(),
                    'action' => 'add_favorite',
                    'user_id' => $request->user()->id
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }

            return response()->json($favorite);
        } else {
            return response()->json([
                'error' => 'Something wrong happenned, please try again later'
            ], 500);
        }
        

    }

    public function delete(Request $request, $id) {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('gif_id', $id)
            ->first();
        
        if ($favorite) {
            $favorite->delete();
            return response()->json($favorite);
        }

        return response()->json([
            'error' => 'Not found'
        ], 404);
    }

    public function isFavorite(Request $request, $id) {
        if ($id) {
            $favorite = Favorite::where('user_id', $request->user()->id)
                ->where('gif_id', $id)
                ->first();
            
            if ($favorite) {
                return response()->json([
                    'isFavorite' => true
                ]);
            }
            
            return response()->json([
                'isFavorite' => false
            ]);
        }

        return response()->json([
            'error' => 'Bad request'
        ], 400);
    }
}
