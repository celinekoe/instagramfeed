<?php

namespace App\Http\Controllers;

use App\Traits\Instagram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Instagram;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tag = "test";
        $page_size = 20;
        $media_array = $this->getMediaArrayFromDatabase($page_size);
        return view('admin', ["tag" => $tag, "media_array" => $media_array]);
    }

    public function more(Request $request)
    {
        $page_count = $request->page_count;
        $page_size = 20;
        $media_array = $this->getMoreMediaArrayFromDatabase($page_count, $page_size);
        return response()->json(["media_array" => $media_array]);
    }

    public function refresh(Request $request)
    {
        $tag = "test";
        $media_data = Instagram::getAllMediaData($tag);
        $this->updateDatabase($media_data->media_array);
        return response()->json();
    }

    public function update(Request $request)
    {
        $action_type = $request->action_type;
        $urls = json_decode($request->urls);
        DB::table('gallery_items')
            ->whereIn('url', $urls)
            ->update([
                'status' => $action_type, 
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
    }

    private static function getMediaArrayFromDatabase($page_size)
    {
        $database_media_array = DB::table("gallery_items")
            ->orderBy("created_at")
            ->take($page_size)
            ->get()
            ->toArray();
        return $database_media_array;
    }

    private static function getMoreMediaArrayFromDatabase($page_count, $page_size)
    {
        $database_media_array = DB::table("gallery_items")
            ->orderBy("created_at")
            ->skip($page_count * $page_size)
            ->take($page_size)
            ->get()
            ->toArray();
        return $database_media_array;
    }

    private function updateDatabase($media_array)
    {
        $insert_media_array = $this->getInsertMediaArray($media_array);
        DB::table('gallery_items')->insert(
            $insert_media_array
        );
    }

    private function getInsertMediaArray($media_array)
    {
        $filtered_media_array = $this->getFilteredMediaArray($media_array);  
        $insert_media_array = [];     
        foreach ($filtered_media_array as $filtered_media)
        {
            $insert_media_array[] = $this->getInsertMedia($filtered_media);
        }
        return $insert_media_array;
    }

    private function getFilteredMediaArray($media_array) {
        $urls = array_column($media_array, "url");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("url", $urls)
            ->get()
            ->toArray();
        return $this->filterMediaArray($media_array, $database_media_array);
    }

    private function filterMediaArray($media_array, $filter_media_array)
    {
        $filtered_media_array = [];
        foreach ($media_array as $media) {
            $found = false;
            foreach ($filter_media_array as $filter_media) {
                if ($media->url === $filter_media->url) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                array_push($filtered_media_array, $media);
            }
        }
        return $filtered_media_array;
    }

    private function getInsertMedia($media)
    {
        return [
            'url' => $media->url,
            'type' => $media->type,
            'link' => $media->link,
            'status' => "pending",
            'uploaded_time' => $media->uploaded_time,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }

}
