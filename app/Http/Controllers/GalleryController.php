<?php

namespace App\Http\Controllers;

use App\Traits\Instagram;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    use Instagram;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_size = 20;
        $media_array = $this->getMediaArrayFromDatabase($page_size);
        return view('gallery', ["media_array" => $media_array]);
    }

    // public function index()
    // {
    //     $page_size = 20;
    //     $media_array = $this->getMediaArrayFromDatabase($page_size);
    //     return response()->json(["media_array" => $media_array]);
    // }

    public function more(Request $request)
    {
        $page_count = $request->page_count;
        $page_size = 20;
        $media_array = $this->getMoreMediaArrayFromDatabase($page_count, $page_size);
        return response()->json(["media_array" => $media_array, "next_url" => ""]);
    }

    private static function getMediaArrayFromDatabase($page_size)
    {
        $database_media_array = DB::table("gallery_items")
            ->where('status', '!=' , "reject")
            ->orWhereNull('status')
            ->orderBy("uploaded_time", "desc")
            ->take($page_size)
            ->get()
            ->toArray();
        return $database_media_array;
    }

    private static function getMoreMediaArrayFromDatabase($page_count, $page_size)
    {
        $database_media_array = DB::table("gallery_items")
            ->where('status', '!=' , "reject")
            ->orWhereNull('status')
            ->orderBy("uploaded_time", "desc")
            ->skip($page_count * $page_size)
            ->take($page_size)
            ->get()
            ->toArray();
        return $database_media_array;
    }


}
