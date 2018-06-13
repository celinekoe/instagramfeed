<?php

namespace App\Http\Controllers;

use App\Traits\Instagram;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
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
        $tag = "test";
        $access_token = "3307217163.955a82a.2c34b9f5809c44568e54933e2919e8a7"; // should be env variable
        $media_array = Instagram::getMediaArray($tag, $access_token);
        $rejected_media_array = $this->getRejectedMediaArray();
        $filtered_media_array = $this->getFilteredMediaArray($media_array, $rejected_media_array);
        return view('gallery', ["media_array" => $filtered_media_array]);
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
                if ($media->url == $rejected_media->url) {
                    $found = true;
                    break;
                }
                // var_dump($media->url);
                if ($media->url == "https://scontent.cdninstagram.com/vp/ac0311e54293353d2b63198692c1f40e/5B21FD6E/t50.2886-16/34513053_962747810572461_2408429660214131513_n.mp4") {
                    // var_dump("test");
                }
            }
            if (!$found) {
                array_push($filtered_media_array, $media);
            }
        }
        return $filtered_media_array;
    }

}
