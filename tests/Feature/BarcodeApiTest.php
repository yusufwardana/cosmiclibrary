<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\BarcodeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BarcodeApiTest extends TestCase
{
    public function test_barcode_png_generates_image(): void
    {
        $response = $this->getJson('/api/barcode/ABC123?format=png&width=2&height=30');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_barcode_svg_generates_svg(): void
    {
        $response = $this->getJson('/api/barcode/ABC123?format=svg');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_barcode_html_generates_html(): void
    {
        $response = $this->getJson('/api/barcode/ABC123?format=html');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function test_barcode_clamps_dimensions(): void
    {
        $response = $this->getJson('/api/barcode/TEST?width=99&height=999');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_barcode_service_produces_png(): void
    {
        $png = BarcodeService::png('TEST123', 2, 30);

        $this->assertNotEmpty($png);
    }

    public function test_barcode_service_produces_svg(): void
    {
        $svg = BarcodeService::svg('TEST123', 2, 30);

        $this->assertNotEmpty($svg);
        $this->assertStringContainsString('<svg', $svg);
    }

    public function test_barcode_service_produces_html(): void
    {
        $html = BarcodeService::html('TEST123', 2, 30);

        $this->assertNotEmpty($html);
    }

    public function test_isbn_endpoint_returns_json(): void
    {
        Http::fake([
            'openlibrary.org/*' => Http::response([
                'title' => 'Test Book',
                'authors' => [['name' => 'Jane']],
                'publishers' => ['Acme'],
                'publish_date' => '2023',
                'number_of_pages' => 100,
                'covers' => [99999],
            ], 200),
        ]);

        $response = $this->getJson('/api/isbn/9780306406157');

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['title', 'author', 'publisher', 'publish_year', 'pages', 'cover_image', 'isbn']]);
        $this->assertSame('Test Book', $response->json('data.title'));
    }

    public function test_isbn_endpoint_returns_404_for_not_found(): void
    {
        Http::fake([
            'openlibrary.org/*' => Http::response([], 404),
        ]);

        $response = $this->getJson('/api/isbn/9999999999999');

        $response->assertStatus(404);
        $response->assertJsonStructure(['message']);
    }

    public function test_isbn_endpoint_returns_404_for_short_isbn(): void
    {
        $response = $this->getJson('/api/isbn/123');

        $response->assertStatus(404);
    }
}