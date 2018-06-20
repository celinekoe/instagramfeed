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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media_data = Instagram::getMediaData();
        $this->updateDatabase($media_data->media_array);
        $updated_media_array = $this->getUpdatedMediaArray($media_data->media_array);
        return view('admin', ["tag" => $media_data->tag, "media_array" => $updated_media_array, "next_url" => $media_data->next_url]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function more(Request $request)
    {
        $url = $this->getUrl($request);
        $media_data = Instagram::getMoreMediaData($url);
        $this->updateDatabase($media_data->media_array);
        $updated_media_array = $this->getUpdatedMediaArray($media_data->media_array);
        return response()->json(["media_array" => $updated_media_array, "next_url" => $media_data->next_url]);
    }

    public function getUrl($request) 
    {
        return $request->base_url . 
            "?access_token=" . $request->access_token .
            "&count=" . $request->count .
            "&max_tag_id=" . $request->max_tag_id; 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return
     */
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
            'status' => "pending",
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }

    private function getUpdatedMediaArray($media_array)
    {
        $updated_media_array = [];
        $urls = array_column($media_array, "url");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("url", $urls)
            ->get()
            ->toArray();
        foreach ($media_array as $media) {
            foreach ($database_media_array as $database_media) {
                if ($media->url === $database_media->url) {
                    $updated_media_object = $this->getUpdatedMediaObject($media, $database_media);
                    array_push($updated_media_array, $updated_media_object);
                }
            }
        }
        return $updated_media_array;
    }

    private static function getUpdatedMediaObject($media, $database_media)
    {
        $updated_media_object = new \stdClass;
        $updated_media_object->url = $media->url;
        $updated_media_object->type = $media->type;
        $updated_media_object->status = $database_media->status;
        return $updated_media_object;
    }

}
