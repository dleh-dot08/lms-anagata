<?php

if (!function_exists('convertToEmbed')) {
    function convertToEmbed($url)
    {
        // YouTube
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            if (strpos($url, 'watch?v=') !== false) {
                return str_replace('watch?v=', 'embed/', $url);
            } elseif (strpos($url, 'youtu.be') !== false) {
                $videoId = basename($url);
                return "https://www.youtube.com/embed/{$videoId}";
            }
        }

        // Google Drive
        if (strpos($url, 'drive.google.com') !== false) {
            if (preg_match('/file\/d\/(.*?)\//', $url, $matches)) {
                return "https://drive.google.com/file/d/{$matches[1]}/preview";
            }
        }

        return $url;
    }
}
