<?php

use App\Models\Users\User;
use function Pest\Livewire\livewire;
use function Pest\Faker\faker;
use App\Providers\RouteServiceProvider;
use App\Http\Livewire\Auth\Register;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;

test('registration_screen_cannot_be_rendered_if_app_is_in_private_mode', function() {

    setting()->set('app_mode', 'PRIVATE');
    setting()->save();

    $response = $this->get('/register');

    $response->assertRedirect('/login');
});

test('registration_screen_can_be_rendered_for_guests_in_public_mode', function() {

    setting()->set('app_mode', 'PUBLIC');
    setting()->save();

    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('authenticated_users_cannot_access_the_registration_screen', function() {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/register');

    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('registration_screen_contains_the_register_livewire_component', function() {

    $response = $this->get('/register');

    $response->assertSeeLivewire('auth.register');
});

test('registration_form_requires_first_name', function() {

    livewire(Register::class)
        ->set(['first_name' => ''])
        ->call('register')
        ->assertHasErrors(['first_name' => 'required']);
});

test('registration_form_requires_last_name', function() {

    livewire(Register::class)
        ->set(['last_name' => ''])
        ->call('register')
        ->assertHasErrors(['last_name' => 'required']);
});

test('registration_form_requires_email', function() {

    livewire(Register::class)
        ->set(['email' => ''])
        ->call('register')
        ->assertHasErrors(['email' => 'required']);
});

test('registration_form_requires_valid_email', function() {

    livewire(Register::class)
        ->set(['email' => 'invalidEmail'])
        ->call('register')
        ->assertHasErrors(['email' => 'email']);
});

test('registration_form_requires_unique_email', function() {

    $user = User::factory()->create();

    livewire(Register::class)
        ->set(['email' => $user->email])
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);
});

test('registration_form_requires_password', function() {

    livewire(Register::class)
        ->set(['password' => ''])
        ->call('register')
        ->assertHasErrors(['password' => 'required']);
});

test('registration_form_requires_password_have_min_8_char', function() {

    livewire(Register::class)
        ->set(['password' => '123456'])
        ->call('register')
        ->assertHasErrors(['password' => 'min']);
});

test('registration_form_requires_password_confirmation', function() {

    livewire(Register::class)
        ->set(['password_confirmation' => ''])
        ->call('register')
        ->assertHasErrors(['password_confirmation' => 'required']);
});

test('registration_form_requires_password_confirmation_to_match_password', function() {

    livewire(Register::class)
        ->set(['password' => 'somePassword', 'password_confirmation' => 'notMatchingPassword'])
        ->call('register')
        ->assertHasErrors(['password' => 'confirmed']);
});

test('registration_form_requires_agreeing_to_terms', function() {

    livewire(Register::class)
        ->set(['agree' => ''])
        ->call('register')
        ->assertHasErrors(['agree' => 'required']);
});

test('user_is_registered_successfully_if_valid_data_is_provided', function() {

    $initialDispatcher = Event::getFacadeRoot();

    Event::fake();

    Model::setEventDispatcher($initialDispatcher);

    livewire(Register::class)
        ->set([
            'first_name' => faker()->firstName,
            'last_name' => faker()->lastName,
            'email' => faker()->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'agree' => true
        ])
        ->call('register')
        ->assertRedirect(RouteServiceProvider::HOME);

    Event::assertDispatched(Registered::class);

    $this->assertAuthenticated();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('vaults', 1);
});