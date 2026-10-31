<?php

use Inertia\Testing\AssertableInertia;
use App\Models\User;

test('successful login', function () {
    $password = 'test';
    $account = User::factory()->create(['password' => \Hash::make($password)]);

    $this->post(route('authenticate', [
        'email' => $account->getAttribute('email'),
        'password' => $password
    ]));

    $this->assertTrue(\Auth::check());
    $this->assertEquals($account->getAttribute('id'), \Auth::user()->getAttribute('id'));
});

test('account not found', function () {
    $password = 'test';
    $account = User::factory()->create(['password' => \Hash::make($password)]);

    $this->post(route('authenticate', [
        'email' => $account->getAttribute('email') . 'fail',
        'password' => $password
    ]));

    $this->assertNotTrue(\Auth::check());
    $this->assertEquals(null, \Auth::user()?->getAttribute('id'));
});

test('wrong password', function () {
    $password = 'test';
    $account = User::factory()->create(['password' => \Hash::make($password)]);

    $this->post(route('authenticate', [
        'email' => $account->getAttribute('email'),
        'password' => $password . '$fail'
    ]));

    $this->assertNotTrue(\Auth::check());
    $this->assertEquals(null, \Auth::user()?->getAttribute('id'));
});
