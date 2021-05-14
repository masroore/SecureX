<?php

use App\Models\Users\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\User\PasswordResetRequested;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

test('authenticated_users_cannot_see_the_reset_password_link_screen', function() {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/password/reset');
    
    $response->assertRedirect(RouteServiceProvider::HOME);

});

test('reset_password_link_screen_can_be_rendered_for_guests', function() {

    $response = $this->get('/password/reset');

    $response->assertStatus(200);
});

test('reset_password_link_can_be_requested', function() {

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('password/email', [
        'email' => $user->email,
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    Notification::assertSentTo($user, PasswordResetRequested::class);
});

test('reset_password_screen_can_be_rendered', function() {
    
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/password/email', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/password/reset/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password_reset_requires_email_and_password', function() {

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/password/email', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/password/reset', [
            'token' => $notification->token,
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'The email field is required.', 
            'password' => 'The password field is required.'
        ]);

        return true;
    });
}); 

test('password_reset_requires_password_confirmation_to_be_same_as_password', function() {

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/password/email', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/password/reset', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'anotherPassword',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'The password confirmation does not match.'
        ]);

        return true;
    });
});

test('password_reset_fails_with_an_invalid_token', function() {

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/password/email', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/password/reset', [
            'token' => 'afd060079eb38b4b9ff2fef812344229f0e86b2c9576332',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'This password reset token is invalid.']);

        return true;
    });
}); 

test('password_can_be_reset_with_valid_token', function() {

    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post('/password/email', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/password/reset', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
}); 