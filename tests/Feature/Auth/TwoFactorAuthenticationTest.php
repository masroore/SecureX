<?php

use App\Models\Users\User;
use function Pest\Livewire\livewire;
use App\Http\Livewire\Auth\TwoStep;

beforeEach(function () {
    $this->user = User::factory()->tfa_key()->create([
        'two_factor_enabled' => true
    ]);
});

test('two_factor_challenge_view_can_be_rendered', function() {

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Two-Factor Authentication (2FA)');
})->group('tfa');

test('two_factor_challenge_contains_livewire_component', function() {

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSeeLivewire('auth.two-step');
})->group('tfa');

test('two_factor_challenge_fails_without_code', function() {

    livewire(TwoStep::class)
        ->call('authenticateCode')
        ->assertHasErrors(['otp' => 'required']);
})->group('tfa');

test('two_factor_challenge_code_must_be_numeric', function() {

    livewire(TwoStep::class)
        ->set('otp', 'abce')
        ->call('authenticateCode')
        ->assertHasErrors(['otp' => 'digits']);
})->group('tfa');

test('two_factor_challenge_code_must_be_6_digits_exactly', function() {

    livewire(TwoStep::class)
        ->set('otp', '12345')
        ->call('authenticateCode')
        ->assertHasErrors(['otp' => 'digits']);
})->group('tfa');

test('two_factor_challenge_is_not_asked_for_authenticated_user', function() {

    session()->put('two_factor_authenticated', time());

    $response = $this->actingAs($this->user)->get('/dashboard');

    $response->assertSee('Dashboard');

    $response->assertStatus(200);
})->group('tfa');
