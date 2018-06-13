<?php
 
namespace App\Traits;

use GuzzleHttp\Client;

trait Instagram {
 
    /**
     * Return media in an array.
     *
     * @return 
     */
    public static function getMediaArray($tag, $access_token)
    {
        $client = new Client(); 
        $response = $client->get("https://api.instagram.com/v1/tags/" . $tag . "/media/recent?access_token=" . $access_token . "&count=200");
        $body = json_decode($response->getBody());
        $media_array = [];
        foreach ($body->data as $media) {
            if (isset($media->carousel_media)) {
                $carousel_media_array = Instagram::getCarouselMediaArray($media->carousel_media);
                $media_array = array_merge($media_array, $carousel_media_array);
            } else {
                $media_object = Instagram::getMediaObject($media);
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
    private static function getCarouselMediaArray($carousel_media_array)
    {
        $new_carousel_media_array = [];
        foreach ($carousel_media_array as $carousel_media) {
            $media_object = Instagram::getMediaObject($carousel_media);
            array_push($new_carousel_media_array, $media_object);
        }
        $new_carousel_media_array = array_reverse($new_carousel_media_array);
        return $new_carousel_media_array;
    }

    /**
     * Return media.
     *
     * @return object
     */
    private static function getMediaObject($media)
    {
        $media_object = new \stdClass;
        if (isset($media->videos)) {
            $media_object->url = $media->videos->standard_resolution->url;
            $media_object->type = "video";
        } else if (isset($media->images)) {
            $media_object->url = $media->images->standard_resolution->url;
            $media_object->type = "image";
        }
        return $media_object;
    }
 
}
 