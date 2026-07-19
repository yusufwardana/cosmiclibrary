<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\DdcLookup;
use Tests\TestCase;

class DdcLookupTest extends TestCase
{
    public function test_parse_returns_class_for_full_number(): void
    {
        $result = DdcLookup::parse('500.123');

        $this->assertNotNull($result);
        $this->assertSame('500', $result['class_code']);
        $this->assertSame('Science', $result['class_label']);
        $this->assertSame('specific', $result['level']);
        $this->assertSame('500', $result['subject_number']);
        $this->assertSame('123', $result['division_number']);
    }

    public function test_parse_returns_division_level(): void
    {
        $result = DdcLookup::parse('820.1');

        $this->assertNotNull($result);
        $this->assertSame('800', $result['class_code']);
        $this->assertSame('Literature', $result['class_label']);
        $this->assertSame('division', $result['level']);
    }

    public function test_parse_returns_class_level_for_hundreds(): void
    {
        $result = DdcLookup::parse('300');

        $this->assertNotNull($result);
        $this->assertSame('300', $result['class_code']);
        $this->assertSame('Social Sciences', $result['class_label']);
        $this->assertSame('class', $result['level']);
    }

    public function test_parse_returns_null_for_empty_input(): void
    {
        $this->assertNull(DdcLookup::parse(''));
        $this->assertNull(DdcLookup::parse('abc'));
    }

    public function test_classes_returns_ten_entries(): void
    {
        $this->assertCount(10, DdcLookup::classes());
    }
}