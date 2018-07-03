<?php
 
namespace App\Traits;

use GuzzleHttp\Client;

trait Instagram {

    /**
     * Return media in an array.
     *
     * @return 
     */
    public static function getAllMediaData($tag)
    {
        $max_count = 500;
        $access_token = "3307217163.955a82a.2c34b9f5809c44568e54933e2919e8a7"; // should be env variable
        $url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?access_token=" . $access_token;
        $body = Instagram::getBody($url);
        $next_url = Instagram::getNextUrl($body->pagination);
        $media_array = Instagram::getMediaArray($body->data);
        $count = count($media_array);
        while ($next_url !== "" && $count < $max_count) {
            $temp_url = $next_url;
            $temp_body = Instagram::getBody($temp_url);
            $temp_next_url = Instagram::getNextUrl($temp_body->pagination);
            $temp_media_array = Instagram::getMediaArray($temp_body->data);
            
            foreach ($temp_media_array as $temp_media) {
                array_push($media_array, $temp_media);
            }
            
            $next_url = $temp_next_url;
            $count = count($media_array);
        }
        
        $media_data = new \stdClass;
        $media_data->media_array = $media_array;   
        $media_data->tag = $tag;
        return $media_data;
    }

    private static function getBody($url) 
    {
        $data = [];
        $client = new Client(); 
        $response = $client->get($url);
        $body = json_decode($response->getBody());
        return $body;
    }

    private static function getNextUrl($pagination) 
    {
        $next_url = "";
        if (isset($pagination->next_url)) {
            $next_url = $pagination->next_url;
        }
        return $next_url;
    }

    private static function getMediaArray($data) 
    {
        $media_array = [];
        foreach ($data as $media) {
            $post_id = $media->id;
            $link = $media->link;
            $uploaded_time = $media->created_time;
            if (isset($media->carousel_media)) {
                $carousel_media_array = Instagram::getCarouselMediaArray($media->carousel_media, $post_id, $link, $uploaded_time);
                $media_array = array_merge($media_array, $carousel_media_array);
            } else {
                $media_object = Instagram::getMediaObject($media, $post_id, 1, $link, $uploaded_time);
                array_push($media_array, $media_object);
            }
        }
        return $media_array;
    }

    /**
     * Return carousel media in an array.
     *
     * @return array
     */
    private static function getCarouselMediaArray($carousel_media_array, $post_id, $link, $uploaded_time)
    {
        $new_carousel_media_array = [];
        $count = 1;
        foreach ($carousel_media_array as $carousel_media) {
            $media_object = Instagram::getMediaObject($carousel_media, $post_id, $count, $link, $uploaded_time);
            array_push($new_carousel_media_array, $media_object);
            $count++;
        }
        $new_carousel_media_array = array_reverse($new_carousel_media_array);
        return $new_carousel_media_array;
    }

    private static function getMediaObject($media, $post_id, $carousel_no, $post_url, $uploaded_time)
    {
        $media_object = new \stdClass;
        if (isset($media->videos)) {
            $media_object->post_id = $post_id;
            $media_object->carousel_no = $carousel_no;
            $media_object->url = $media->videos->standard_resolution->url;
            $media_object->type = "video";
            $media_object->post_url = $post_url;
            $media_object->uploaded_time = $uploaded_time;
        } else if (isset($media->images)) {
            $media_object->post_id = $post_id;
            $media_object->carousel_no = $carousel_no;
            $media_object->url = $media->images->standard_resolution->url;
            $media_object->type = "image";
            $media_object->post_url = $post_url;
            $media_object->uploaded_time = $uploaded_time;
        }
        return $media_object;
    }
 
}
 