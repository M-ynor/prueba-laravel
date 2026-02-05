<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Suppress deprecation warnings in tests
        error_reporting(E_ALL & ~E_DEPRECATED);
    }
}
