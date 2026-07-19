<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BarcodeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarcodeController extends Controller
{
    public function generate(Request $request, string $data): Response
    {
        $format = $request->query('format', 'png');
        $w = (int) $request->query('width', 2);
        $h = (int) $request->query('height', 30);

        $w = max(1, min(10, $w));
        $h = max(10, min(300, $h));

        return match ($format) {
            'svg' => response(BarcodeService::svg($data, $w, $h), 200, ['Content-Type' => 'image/svg+xml']),
            'html' => response(BarcodeService::html($data, $w, $h), 200, ['Content-Type' => 'text/html']),
            default => response(BarcodeService::png($data, $w, $h), 200, ['Content-Type' => 'image/png']),
        };
    }
}