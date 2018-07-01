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
        $filtered_ids = array_column($filtered_media_array, "link");
        DB::table("gallery_items")
            ->whereIn("link", $filtered_ids)
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
        $urls = array_column($media_array, "url");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("url", $urls)
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

    private static function getInsertMedia($media)
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

    private static function getOldMediaArray($media_array) {
        $links = array_column($media_array, "link");
        $database_media_array = DB::table("gallery_items")
            ->whereIn("link", $links)
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
                if ($media->link === $filter_media->link) {
                    $found = true;
                    $media->id = $filter_media->id;
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
            'url' => $media->url,
            'type' => $media->type,
            'link' => $media->link,
            'status' => $media->status,
            'uploaded_time' => $media->uploaded_time,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }
 
}
 