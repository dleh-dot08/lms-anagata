<?php

if (!function_exists('convertToEmbed')) {
    function convertToEmbed($url)
    {
        if (str_contains($url, 'youtube.com')) {
            return preg_replace(
                '/watch\?v=([a-zA-Z0-9_-]+)/',
                'embed/$1',
                $url
            );
        }

        if (str_contains($url, 'drive.google.com')) {
            if (preg_match('/\/file\/d\/(.*?)\//', $url, $matches)) {
                return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
            }
        }

        return $url;
    }
}

if (!function_exists('convertToPreview')) {
    function convertToPreview($url)
    {
        return convertToEmbed($url);
    }
}
