<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * @property \Illuminate\Foundation\Application $app
 * @property \Illuminate\Contracts\Console\Kernel $artisan
 * @property string $baseUrl
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
