<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('admin/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated admin users can visit the dashboard', function () {
    // Create user with role 'admin' explicitly
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $response = $this->get('admin/dashboard');
    $response->assertStatus(200);
});