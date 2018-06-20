<?php
 
namespace App\Traits;

use GuzzleHttp\Client;

trait Instagram {
 
    /**
     * Return media in an array.
     *
     * @return 
     */
    public static function getMediaData()
    {
        $tag = "test";
        $count = 8;
        $access_token = "3307217163.955a82a.2c34b9f5809c44568e54933e2919e8a7"; // should be env variable
        $url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?access_token=" . $access_token . "&count=" . $count;
        $body = Instagram::getBody($url);
        $media_data = new \stdClass;
        $media_data->next_url = Instagram::getNextUrl($body->pagination);
        $media_data->media_array = Instagram::getMediaArray($body->data);
        $media_data->tag = $tag;
        return $media_data;
    }

    /**
     * Return media in an array.
     *
     * @return 
     */
    public static function getMoreMediaData($next_url)
    {
        $count = 8;
        $body = Instagram::getBody($next_url);
        $media_data = new \stdClass;
        $media_data->next_url = Instagram::getNextUrl($body->pagination);
        $media_data->media_array = Instagram::getMediaArray($body->data);
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
            $link = $media->link;
            if (isset($media->carousel_media)) {
                $carousel_media_array = Instagram::getCarouselMediaArray($media->carousel_media, $link);
                $media_array = array_merge($media_array, $carousel_media_array);
            } else {
                $media_object = Instagram::getMediaObject($media, $link);
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
    private static function getCarouselMediaArray($carousel_media_array, $link)
    {
        $new_carousel_media_array = [];
        foreach ($carousel_media_array as $carousel_media) {
            $media_object = Instagram::getMediaObject($carousel_media, $link);
            array_push($new_carousel_media_array, $media_object);
        }
        $new_carousel_media_array = array_reverse($new_carousel_media_array);
        return $new_carousel_media_array;
    }

    private static function getMediaObject($media, $link)
    {
        $media_object = new \stdClass;
        if (isset($media->videos)) {
            $media_object->url = $media->videos->standard_resolution->url;
            $media_object->type = "video";
            $media_object->link = $link;
        } else if (isset($media->images)) {
            $media_object->url = $media->images->standard_resolution->url;
            $media_object->type = "image";
            $media_object->link = $link;
        }
        return $media_object;
    }
 
}
 