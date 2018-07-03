<?php
 
namespace App\Traits;

use App\Traits\Instagram;
use Illuminate\Support\Facades\DB;

trait Database {

    use Instagram;

    public static function updateDatabase()
    {
        $tag = "hhn8";
        $media_data = Instagram::getAllMediaData($tag);
        $media_array = $media_data->media_array;

        $filtered_media_array = Database::getOldMediaArray($media_array);
        $filtered_ids = array_column($filtered_media_array, "post_id");
        DB::table("gallery_items")
            ->whereIn("post_id", $filtered_ids)
            ->delete();

        $update_media_array = Database::getUpdateMediaArray($filtered_media_array);
        DB::table('gallery_items')
            ->insert(
                $update_media_array
            );

        $insert_media_array = Database::getInsertMediaArray($media_array);
        DB::table('gallery_items')
            ->insert(
                $insert_media_array
            );
    }

    private static function getOldMediaArray($media_array) {
        $post_ids = array_column($media_array, "post_id");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("post_id", $post_ids)
            ->get()
            ->toArray();
        return Database::filterOldMediaArray($media_array, $database_media_array);
    }

    private static function filterOldMediaArray($media_array, $filter_media_array)
    {
        $filtered_media_array = [];
        foreach ($media_array as $media) {
            $found = false;
            foreach ($filter_media_array as $filter_media) {
                if ($media->post_id === $filter_media->post_id && 
                    $media->carousel_no === $filter_media->carousel_no) {
                        $found = true;
                        $media->status = $filter_media->status;
                        break;
                }
            }
            if ($found) {
                array_push($filtered_media_array, $media);
            }
        }
        return $filtered_media_array;
    }

    private static function getUpdateMediaArray($filtered_media_array) 
    {
        $update_media_array = [];     
        foreach ($filtered_media_array as $filtered_media)
        {
            $update_media_array[] = Database::getUpdateMedia($filtered_media);
        }
        return $update_media_array;
    }

    private static function getUpdateMedia($media)
    {
        return [
            'post_id' => $media->post_id,
            'carousel_no' => $media->carousel_no,
            'url' => $media->url,
            'type' => $media->type,
            'post_url' => $media->post_url,
            'status' => $media->status,
            'uploaded_time' => $media->uploaded_time,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }

    private static function getInsertMediaArray($media_array)
    {
        $filtered_media_array = Database::getNewMediaArray($media_array);  
        $insert_media_array = [];     
        foreach ($filtered_media_array as $filtered_media)
        {
            $insert_media_array[] = Database::getInsertMedia($filtered_media);
        }
        return $insert_media_array;
    }

    private static function getNewMediaArray($media_array) {
        $post_ids = array_column($media_array, "post_id");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("post_id", $post_ids)
            ->get()
            ->toArray();
        return Database::filterNewMediaArray($media_array, $database_media_array);
    }

    private static function filterNewMediaArray($media_array, $filter_media_array)
    {
        $filtered_media_array = [];
        foreach ($media_array as $media) {
            $found = false;
            foreach ($filter_media_array as $filter_media) {
                if ($media->post_id === $filter_media->post_id) {
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

    private static function getInsertMedia($media)
    {
        return [
            'url' => $media->url,
            'post_id' => $media->post_id,
            'carousel_no' => $media->carousel_no,
            'type' => $media->type,
            'post_url' => $media->post_url,
            'status' => "pending",
            'uploaded_time' => $media->uploaded_time,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }
 
}
 