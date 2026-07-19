<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\IsbnLookup;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnLookupTest extends TestCase
{
    public function test_fetch_returns_metadata_for_valid_isbn(): void
    {
        Http::fake([
            'openlibrary.org/*' => Http::response([
                'title' => 'Test Book',
                'authors' => [['name' => 'John Doe']],
                'publishers' => ['Publisher Inc'],
                'publish_date' => '2020',
                'number_of_pages' => 300,
                'covers' => [12345],
            ], 200),
        ]);

        $result = IsbnLookup::fetch('9780306406157');

        $this->assertNotNull($result);
        $this->assertSame('Test Book', $result['title']);
        $this->assertSame('John Doe', $result['author']);
        $this->assertSame('Publisher Inc', $result['publisher']);
        $this->assertSame(2020, $result['publish_year']);
        $this->assertSame(300, $result['pages']);
        $this->assertStringContainsString('covers.openlibrary.org', $result['cover_image']);
    }

    public function test_fetch_returns_null_for_invalid_isbn(): void
    {
        $result = IsbnLookup::fetch('123');

        $this->assertNull($result);
    }

    public function test_fetch_returns_null_for_not_found(): void
    {
        Http::fake([
            'openlibrary.org/*' => Http::response([], 404),
        ]);

        $result = IsbnLookup::fetch('9780306406157');

        $this->assertNull($result);
    }

    public function test_fetch_returns_null_on_network_error(): void
    {
        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $result = IsbnLookup::fetch('9780306406157');

        $this->assertNull($result);
    }
}