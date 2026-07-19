<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IsbnLookup;
use Illuminate\Http\JsonResponse;

class IsbnController extends Controller
{
    public function show(string $isbn): JsonResponse
    {
        $data = IsbnLookup::fetch($isbn);

        if ($data === null) {
            return response()->json(['message' => 'ISBN tidak ditemukan atau gagal dijangkau.'], 404);
        }

        return response()->json(['data' => $data]);
    }
}