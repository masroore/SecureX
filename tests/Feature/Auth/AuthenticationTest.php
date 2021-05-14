<?php

use App\Models\Users\User;
use function Pest\Livewire\livewire;
use App\Providers\RouteServiceProvider;
use App\Http\Livewire\Auth\Login;

test('login_screen_can_be_rendered', function() {

    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('login_screen_contains_the_login_livewire_component', function() {

    $response = $this->get('/login');

    $response->assertSeeLivewire('auth.login');
});

test('users_cannot_authenticate_without_an_email_and_a_password', function() {

    livewire(Login::class)
        ->set(['email' => '', 'password' => ''])
        ->call('login')
        ->assertHasErrors(['email' => 'required', 'password' => 'required']);
});

test('users_cannot_authenticate_with_an_unregistered_email', function() {

    livewire(Login::class)
        ->set(['email' => 'unreg@email.com'])
        ->call('login')
        ->assertHasErrors(['email' => 'exists']);
});

test('users_cannot_authenticate_with_an_invalid_password', function() {

    $user = User::factory()->create();

    livewire(Login::class)
    ->set(['email' => $user->email, 'password' => 'invalidPass'])
    ->call('login')
    ->assertHasErrors('password');
});

test('users_can_authenticate_with_valid_credentials', function() {

    $user = User::factory()->create();

    livewire(Login::class)
    ->set(['email' => $user->email, 'password' => 'password'])
    ->call('login')
    ->assertRedirect(RouteServiceProvider::HOME);
    
    $this->assertAuthenticated();
});

test('users_without_active_status_cannot_access_dashboard', function() {

    $user = User::factory()->create(['status' => 'Banned', 'remark' => 'Test Ban']);

    livewire(Login::class)
        ->set(['email' => $user->email, 'password' => 'password'])
        ->call('login')
        ->assertRedirect('/login');

    $this->assertGuest();
});