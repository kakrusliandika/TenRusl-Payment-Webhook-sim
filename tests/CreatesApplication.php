<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Bootstrap aplikasi untuk testing.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Path default skeleton Laravel: ../bootstrap/app.php
        $app = require __DIR__.'/../bootstrap/app.php';

        // Jalankan bootstrap kernel agar konfigurasi, provider, dll. siap dipakai test
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
