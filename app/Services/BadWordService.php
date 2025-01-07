<?php

namespace App\Services;

class BadWordService
{
    protected $badWords;

    public function __construct()
    {
        // Load bad words from the config
        $this->badWords = config('badwords.list');
    }

    public function maskBadWords($text)
    {
        $filteredText = preg_replace_callback(
            $this->createSearchPattern($this->badWords),
            function ($matches) {
                // Check if matches exist and are not empty
                if (!empty($matches[0])) {
                    return $matches[0][0] . str_repeat('*', strlen($matches[0]) - 1);
                }
                return $matches[0]; // Fallback if no matches
            },
            $text
        );

        return $filteredText !== null ? $filteredText : $text;
    }

    protected function createSearchPattern($badWords)
    {
        return array_map(function ($word) {
            return '/\b' . preg_quote($word, '/') . '\b/i'; // Use preg_quote to escape special characters
        }, $badWords);
    }
}