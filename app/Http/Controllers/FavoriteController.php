<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Favorite;

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
        $gif = $request->input('gif');
        $gifId = $request->input('gif_id');
        
        $favorite = Favorite::create([
            'gif_id' => $gifId,
            'gif' => $gif,
            'user_id' => $request->user()->id
        ]);

        return response()->json($favorite);
    }

    public function delete(Request $request, $id) {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->find($id);
        
        if ($favorite) {
            $favorite->delete();
            return response()->json($favorite);
        }

        return response()->json([
            'error' => 'Not found'
        ], 404);
    }
}
