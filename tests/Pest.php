<?php

declare(strict_types=1);

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Pest Bootstrap
|--------------------------------------------------------------------------
| File ini mengonfigurasi suite Pest agar seluruh test di
| tests/Feature dan tests/Unit otomatis mewarisi Tests\TestCase.
*/

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Helpers / Expectations (opsional)
|--------------------------------------------------------------------------
| Tambahkan helper global khusus test di sini bila perlu.
| Contoh kecil: expectation string non-kosong.
*/

// expect()->extend('toBeNonEmptyString', function () {
//     expect($this->value)->toBeString()->not->toBe('');
// });
