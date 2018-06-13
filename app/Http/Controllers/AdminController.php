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
        $temp_media_array = Instagram::getMediaData()->media_array;
        $this->updateDatabase($temp_media_array);
        $media_array = $this->getMediaArray();
        return view('admin', ["media_array" => $media_array]);
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
        foreach ($media_array as $media)
        {
            $insert_media_array[] = $this->getInsertMedia($media);
        }
        $insert_media_array = array_reverse($insert_media_array);
        $insert_media_index = $this->getInsertMediaIndex($insert_media_array);
        $insert_media_array = array_slice($insert_media_array, $insert_media_index);
        return $insert_media_array;
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

    private function getInsertMediaIndex($insert_media_array)
    {
        $insert_media_index = 0;
        $latest_media = $this->getLatestMedia();
        if (isset($latest_media)) {
            $insert_media_index = array_search($latest_media->url, array_column($insert_media_array, "url")) + 1;
        }
        return $insert_media_index;
    }

    private function getLatestMedia()
    {
        $latest_media = DB::table('gallery_items')
            ->latest('id')
            ->first();
        return $latest_media;
    }

    private function getMediaArray()
    {
        $media_array = DB::table('gallery_items')->get()->toArray();
        $media_array = array_reverse($media_array);
        return $media_array;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return
     */
    public function update(Request $request)
    {
        $actionType = $request->action_type;
        $urls = json_decode($request->urls);
        DB::table('gallery_items')
            ->whereIn('url', $urls)
            ->update([
                'status' => $actionType, 
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
    }
}
