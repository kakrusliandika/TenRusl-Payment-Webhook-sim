<?php

declare(strict_types=1);

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Pest Bootstrap
|--------------------------------------------------------------------------
| Di sini kita "bind" TestCase dan trait untuk seluruh folder test.
| Penting: TestCase cukup didefinisikan SEKALI untuk folder yang sama.
| Kalau ada file lain yang juga memanggil uses(TestCase::class) di dalam
| scope Feature/Unit, Pest akan error: "already uses the test case".
*/

// Semua test di tests/Feature dan tests/Unit pakai Laravel TestCase
uses(TestCase::class)->in('Feature', 'Unit');

// Semua Feature test otomatis pakai RefreshDatabase
uses(RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Helpers / Expectations (opsional)
|--------------------------------------------------------------------------
| Tambahkan helper global khusus test di sini bila perlu.
*/

// expect()->extend('toBeNonEmptyString', function () {
//     expect($this->value)->toBeString()->not->toBe('');
// });
