<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BorrowRecordResource;
use App\Http\Resources\MemberResource;
use App\Services\SearchEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchController extends Controller
{
    public function __construct(private readonly SearchEngine $engine) {}

    public function books(Request $request): mixed
    {
        $result = $this->engine->searchBooks(
            (string) $request->input('q', ''),
            [
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
            ],
            (int) $request->input('per_page', 20)
        );

        return BookResource::collection($result);
    }

    public function members(Request $request): mixed
    {
        $result = $this->engine->searchMembers(
            (string) $request->input('q', ''),
            [
                'type' => $request->input('type'),
                'class_name' => $request->input('class_name'),
                'status' => $request->input('status'),
            ],
            (int) $request->input('per_page', 20)
        );

        return MemberResource::collection($result);
    }

    public function borrowRecords(Request $request): mixed
    {
        $result = $this->engine->searchBorrowRecords(
            (string) $request->input('q', ''),
            [
                'status' => $request->input('status'),
                'member_id' => $request->input('member_id'),
            ],
            (int) $request->input('per_page', 20)
        );

        return BorrowRecordResource::collection($result);
    }
}