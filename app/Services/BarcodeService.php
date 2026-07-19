<?php

declare(strict_types=1);

namespace App\Services;

use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    public static function png(string $data, int $w = 2, int $h = 30): string
    {
        $g = new BarcodeGeneratorPNG();

        return $g->getBarcode($data, BarcodeGeneratorPNG::TYPE_CODE_128, $w, $h);
    }

    public static function svg(string $data, int $w = 2, int $h = 30): string
    {
        $g = new BarcodeGeneratorSVG();

        return $g->getBarcode($data, BarcodeGeneratorSVG::TYPE_CODE_128, $w, $h);
    }

    public static function html(string $data, int $w = 2, int $h = 30): string
    {
        $g = new BarcodeGeneratorHTML();

        return $g->getBarcode($data, BarcodeGeneratorHTML::TYPE_CODE_128, $w, $h);
    }
}