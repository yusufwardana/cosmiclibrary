<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_that_assertion_works(): void
    {
        $value = 1 + 1;
        $this->assertSame(2, $value);
    }
}
