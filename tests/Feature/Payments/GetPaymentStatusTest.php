<?php

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('gets payment status by id', function () {
    $p = Payment::factory()->pending()->create([
        'amount' => 12345,
        'currency' => 'IDR',
    ]);

    $res = $this->getJson('/api/v1/payments/' . $p->id);
    $res->assertOk()
        ->assertJsonFragment([
            'id' => (string) $p->id,
        ]);
});
