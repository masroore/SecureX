<?php

use App\Models\Users\User;

test('guests_cannot_see_the_confirm_password_screen', function() {
    
    $response = $this->get('/password/confirm');

    $response->assertRedirect('/login');
});

test('confirm_password_screen_can_be_rendered', function() {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/password/confirm');

    $response->assertStatus(200);
});

test('password_can_be_confirmed', function() {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/password/confirm', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password_is_not_confirmed_with_invalid_password', function() {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/password/confirm', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});