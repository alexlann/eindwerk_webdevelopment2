<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Goutte\Client;

class Scrape extends Model
{
    use HasFactory;

    public static function convertTimeString($days_between) {
        if($days_between > 365) {
            return $days_between = (int) ($days_between / 365) . __("y");
        } elseif($days_between > 30) {
            return $days_between = (int) ($days_between /30) . __("m");
        } elseif($days_between > 7) {
            return $days_between = (int) ($days_between / 7) . __("w");
        } else {
            return $days_between = $days_between . __("d");
        }
    }

    public static function createImageName($url) {
        $contents = file_get_contents($url);
        // get filename
        $fileName = substr($url, strrpos($url, '/') + 1);
        // get extension
        $splitFileName = explode(".", $fileName);
        $ext = end($splitFileName);
        // make random file name, with day-prefix
        $randomName = date('d') . '-' . Str::random(10) . "." . $ext;
        // path magic
        $filePath = 'uploads/' . date('Y/m/');
        $fullPath = $filePath . $randomName;
        // upload files in symbolic public folder (make accessable)
        /** @var Illuminate\Filesystem\FilesystemAdapter */
        $fileSystem = Storage::disk('public');
        $fileSystem->put($fullPath, $contents);

        return $fullPath;
    }

    public static function getNextPage($crawler, $filterTags) {
        $linkTag = $crawler->filter($filterTags)->first();
        if($linkTag->count() <= 0) return;
        $link = $linkTag->link();

        $client = new Client();
        $nextCrawler = $client->click($link);

        return $nextCrawler;
    }
}
