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
        $media_data = Instagram::getMediaData();
        $rejected_media_array = $this->getRejectedMediaArray();
        $filtered_media_array = $this->getFilteredMediaArray($media_data->media_array, $rejected_media_array);
        return view('gallery', ["media_array" => $filtered_media_array, "next_url" => $media_data->next_url]);
    }

    // public function index()
    // {
    //     $media_data = Instagram::getMediaData();
    //     $rejected_media_array = $this->getRejectedMediaArray();
    //     $filtered_media_array = $this->getFilteredMediaArray($media_data->media_array, $rejected_media_array);
    //     return response()->json(["media_array" => $filtered_media_array, "next_url" => $media_data->next_url]);
    // }

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
        $rejected_media_array = $this->getRejectedMediaArray();
        $filtered_media_array = $this->getFilteredMediaArray($media_data->media_array, $rejected_media_array);
        return response()->json(["media_array" => $filtered_media_array, "next_url" => $media_data->next_url]);
    }

    public function getUrl($request) 
    {
        return $request->base_url . 
            "?access_token=" . $request->access_token .
            "&count=" . $request->count .
            "&max_tag_id=" . $request->max_tag_id; 
    }

    private function getRejectedMediaArray()
    {
        $rejected_media_array = DB::table('gallery_items')
            ->where('status', "reject")
            ->get();
        return $rejected_media_array->toArray();
    }

    private function getFilteredMediaArray($media_array, $rejected_media_array)
    {
        $filtered_media_array = [];
        foreach ($media_array as $media) {
            $found = false;
            foreach ($rejected_media_array as $rejected_media) {
                if ($media->url === $rejected_media->url) {
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

}
