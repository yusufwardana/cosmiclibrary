<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IsbnLookup
{
    private const BASE = 'https://openlibrary.org/isbn';

    /**
     * Fetch metadata for an ISBN from OpenLibrary.
     *
     * @return array<string, mixed>|null
     */
    public static function fetch(string $isbn): ?array
    {
        $clean = preg_replace('/[^0-9X]/i', '', strtoupper($isbn));
        if (strlen($clean) < 10) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get(self::BASE . "/{$clean}.json");
            if (! $response->successful()) {
                return null;
            }

            $data = $response->json() ?? [];
        } catch (\Throwable $e) {
            Log::warning('ISBN lookup failed', ['isbn' => $clean, 'error' => $e->getMessage()]);

            return null;
        }

        $title = $data['title'] ?? null;
        $authors = collect($data['authors'] ?? [])->pluck('name')->implode(', ') ?: null;
        $publishers = collect($data['publishers'] ?? [])->implode(', ') ?: null;
        $year = $data['publish_date'] ?? null;
        $pages = $data['number_of_pages'] ?? null;
        $cover = isset($data['covers'][0])
            ? "https://covers.openlibrary.org/b/id/{$data['covers'][0]}-M.jpg"
            : null;

        return [
            'title' => $title,
            'author' => $authors,
            'publisher' => $publishers,
            'publish_year' => is_numeric($year) ? (int) $year : null,
            'pages' => is_numeric($pages) ? (int) $pages : null,
            'cover_image' => $cover,
            'language' => $data['languages'][0]['key'] ?? null, // e.g. "/languages/eng"
            'isbn' => $clean,
        ];
    }
}