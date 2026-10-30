<?php

use Inertia\Testing\AssertableInertia;

test('home page loads', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Home'));
});
