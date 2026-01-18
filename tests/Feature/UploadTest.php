<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('upload'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the upload page', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('upload'));

    $response->assertOk();
});
