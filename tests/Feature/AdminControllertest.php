<?php

use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserShift;
use App\Models\UserShiftConfirmation;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia;

function createAdminControllerShift(Role $role, array $employees = [], array $attributes = []): Shift
{
    $shift = Shift::query()->create(array_replace([
        'title' => 'Existing shift',
        'description' => 'Existing shift description',
        'shift_role' => $role->getAttribute('id'),
        'max_crew' => 2,
        'start_datetime' => '2026-11-16 10:00:00',
        'end_datetime' => '2026-11-16 12:00:00',
    ], $attributes));

    foreach ($employees as $employee) {
        UserShift::query()->create([
            'user_id' => $employee->getAttribute('id'),
            'shift_id' => $shift->getAttribute('id'),
        ]);
    }

    return $shift;
}

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(Carbon::parse('2026-11-16 12:00:00'));

    $adminRole = Role::query()->forceCreate(['name' => 'Admin', 'color' => '#2563eb']);
    $this->role = Role::query()->forceCreate(['name' => 'Catering', 'color' => '#16a34a']);
    $this->otherRole = Role::query()->forceCreate(['name' => 'Shift leader', 'color' => '#ea580c']);

    $admin = User::factory()->create();
    $this->worker = User::factory()->create();
    $this->secondWorker = User::factory()->create();

    UserRole::query()->forceCreate([
        'user_id' => $admin->getAttribute('id'),
        'role_id' => $adminRole->getAttribute('id'),
    ]);

    foreach ([$this->worker, $this->secondWorker] as $worker) {
        UserRole::query()->forceCreate([
            'user_id' => $worker->getAttribute('id'),
            'role_id' => $this->role->getAttribute('id'),
        ]);
    }

    $this->actingAs($admin);

    $this->payload = [
        'title' => 'Lunch shift',
        'description' => 'Serve the lunch service',
        'max_crew' => 2,
        'shift_role' => $this->role->getAttribute('id'),
        'start_datetime' => '2026-11-16T10:00',
        'end_datetime' => '2026-11-16T12:00',
        'employees' => [[
            'id' => $this->worker->getAttribute('id'),
            'name' => $this->worker->getAttribute('name'),
        ]],
    ];
});

dataset('overlapping shift intervals', [
    'starts during an existing shift' => ['2026-11-16T11:00', '2026-11-16T13:00'],
    'ends during an existing shift' => ['2026-11-16T09:00', '2026-11-16T11:00'],
    'falls inside an existing shift' => ['2026-11-16T10:30', '2026-11-16T11:30'],
    'contains an existing shift' => ['2026-11-16T09:00', '2026-11-16T13:00'],
    'matches an existing shift' => ['2026-11-16T10:00', '2026-11-16T12:00'],
]);

dataset('adjacent shift intervals', [
    'ends when an existing shift starts' => ['2026-11-16T08:00', '2026-11-16T10:00'],
    'starts when an existing shift ends' => ['2026-11-16T12:00', '2026-11-16T14:00'],
]);

dataset('users without the required shift role', [
    'no roles' => [false],
    'a different role' => [true],
]);

test('a shift can be created successfully with its assigned users', function () {
    $payload = $this->payload;
    $payload['employees'][] = [
        'id' => $this->secondWorker->getAttribute('id'),
        'name' => $this->secondWorker->getAttribute('name'),
    ];

    $this->postJson(route('adminAddShift'), $payload)
        ->assertOk()
        ->assertJson(['success' => true, 'message' => '']);

    $shift = Shift::query()->sole();

    $this->assertDatabaseHas('shifts', [
        'id' => $shift->getAttribute('id'),
        'title' => $payload['title'],
        'description' => $payload['description'],
        'shift_role' => $this->role->getAttribute('id'),
        'max_crew' => 2,
        'start_datetime' => '2026-11-16 10:00:00',
        'end_datetime' => '2026-11-16 12:00:00',
    ]);
    $this->assertDatabaseCount('user_shifts', 2);

    foreach ([$this->worker, $this->secondWorker] as $worker) {
        $this->assertDatabaseHas('user_shifts', [
            'shift_id' => $shift->getAttribute('id'),
            'user_id' => $worker->getAttribute('id'),
        ]);
    }
});

test('a shift can be deleted with its assignments and confirmations', function () {
    $shift = createAdminControllerShift($this->role, [$this->worker]);
    $otherShift = createAdminControllerShift($this->role, [$this->secondWorker]);
    $assignment = UserShift::query()->where('shift_id', $shift->getAttribute('id'))->sole();
    $confirmation = UserShiftConfirmation::query()->forceCreate([
        'user_shift_id' => $assignment->getAttribute('id'),
        'start_datetime' => '2026-11-16 10:00:00',
        'end_datetime' => '2026-11-16 12:00:00',
        'break_minutes' => 15,
    ]);

    $this->deleteJson(route('adminDeleteShift'), ['id' => $shift->getAttribute('id')])
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('shifts', ['id' => $shift->getAttribute('id')]);
    $this->assertDatabaseMissing('user_shifts', ['shift_id' => $shift->getAttribute('id')]);
    $this->assertDatabaseMissing('user_shift_confirmations', ['id' => $confirmation->getAttribute('id')]);
    $this->assertDatabaseHas('users', ['id' => $this->worker->getAttribute('id')]);
    $this->assertDatabaseHas('shifts', ['id' => $otherShift->getAttribute('id')]);
    $this->assertDatabaseHas('user_shifts', [
        'shift_id' => $otherShift->getAttribute('id'),
        'user_id' => $this->secondWorker->getAttribute('id'),
    ]);
});

test('a shift can be updated without conflicting with its own assignment', function () {
    $shift = createAdminControllerShift($this->role, [$this->worker]);
    $payload = array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'title' => 'Updated shift',
        'description' => 'Updated description',
        'max_crew' => 1,
        'start_datetime' => '2026-11-16T11:00',
        'end_datetime' => '2026-11-16T13:00',
    ]);

    $this->putJson(route('adminUpdateShift'), $payload)
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseCount('shifts', 1);
    $this->assertDatabaseHas('shifts', [
        'id' => $shift->getAttribute('id'),
        'title' => 'Updated shift',
        'description' => 'Updated description',
        'max_crew' => 1,
        'start_datetime' => '2026-11-16 11:00:00',
        'end_datetime' => '2026-11-16 13:00:00',
    ]);
    $this->assertDatabaseCount('user_shifts', 1);
    $this->assertDatabaseHas('user_shifts', [
        'shift_id' => $shift->getAttribute('id'),
        'user_id' => $this->worker->getAttribute('id'),
    ]);
});

test('initial shifts are loaded for the current week with their assigned users', function () {
    $mondayShift = createAdminControllerShift($this->role, [$this->worker]);
    $sundayShift = createAdminControllerShift($this->role, [$this->secondWorker], [
        'start_datetime' => '2026-11-22 14:00:00',
        'end_datetime' => '2026-11-22 16:00:00',
    ]);
    createAdminControllerShift($this->role, [], [
        'start_datetime' => '2026-11-15 10:00:00',
        'end_datetime' => '2026-11-15 12:00:00',
    ]);
    createAdminControllerShift($this->role, [], [
        'start_datetime' => '2026-11-23 10:00:00',
        'end_datetime' => '2026-11-23 12:00:00',
    ]);
    $workerId = $this->worker->getAttribute('id');

    $this->get(route('admin'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('admin/Shifts')
            ->where('activePage', 'shifts')
            ->where('weekStartDateTime', fn ($date) => Carbon::parse($date)->toDateTimeString() === '2026-11-16 00:00:00')
            ->has('shifts', 2)
            ->where('shifts', function ($shifts) use ($mondayShift, $sundayShift, $workerId) {
                $shifts = collect($shifts)->keyBy('id');

                return $shifts->has($mondayShift->getAttribute('id'))
                    && $shifts->has($sundayShift->getAttribute('id'))
                    && $shifts->get($mondayShift->getAttribute('id'))['users'][0]['id'] === $workerId;
            })
        );
});

test('fetching shifts returns the requested week', function (string $direction, string $expectedDate) {
    $shifts = [];

    foreach (['2026-11-09', '2026-11-16', '2026-11-23'] as $date) {
        $shifts[$date] = createAdminControllerShift($this->role, [], [
            'start_datetime' => "{$date} 10:00:00",
            'end_datetime' => "{$date} 12:00:00",
        ]);
    }

    $response = $this->getJson(route('adminGetShifts', [
        'oldDate' => '2026-11-16 00:00:00',
        'direction' => $direction,
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'shifts')
        ->assertJsonPath('shifts.0.id', $shifts[$expectedDate]->getAttribute('id'));

    expect(Carbon::parse($response->json('newWeekStartDate'))->toDateTimeString())
        ->toBe("{$expectedDate} 00:00:00");
})->with([
    'previous week' => ['previous', '2026-11-09'],
    'current week' => ['current', '2026-11-16'],
    'next week' => ['next', '2026-11-23'],
]);

test('users cannot be assigned to overlapping shifts when creating a shift', function (string $start, string $end) {
    createAdminControllerShift($this->role, [$this->worker]);

    $this->postJson(route('adminAddShift'), array_replace($this->payload, [
        'start_datetime' => $start,
        'end_datetime' => $end,
    ]))->assertOk()->assertJsonPath('success', false);

    $this->assertDatabaseCount('shifts', 1);
    $this->assertDatabaseCount('user_shifts', 1);
})->with('overlapping shift intervals');

test('users cannot be assigned to overlapping shifts when updating a shift', function (string $start, string $end) {
    createAdminControllerShift($this->role, [$this->worker]);
    $shift = createAdminControllerShift($this->role, [$this->worker], [
        'start_datetime' => '2026-11-16 14:00:00',
        'end_datetime' => '2026-11-16 16:00:00',
    ]);

    $this->putJson(route('adminUpdateShift'), array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'title' => 'Changed title',
        'start_datetime' => $start,
        'end_datetime' => $end,
    ]))->assertOk()->assertJsonPath('success', false);

    $this->assertDatabaseCount('shifts', 2);
    $this->assertDatabaseCount('user_shifts', 2);
    $this->assertDatabaseHas('shifts', [
        'id' => $shift->getAttribute('id'),
        'title' => 'Existing shift',
        'start_datetime' => '2026-11-16 14:00:00',
        'end_datetime' => '2026-11-16 16:00:00',
    ]);
})->with('overlapping shift intervals');

test('users can be assigned to adjacent shifts when creating a shift', function (string $start, string $end) {
    createAdminControllerShift($this->role, [$this->worker]);

    $this->postJson(route('adminAddShift'), array_replace($this->payload, [
        'start_datetime' => $start,
        'end_datetime' => $end,
    ]))->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseCount('shifts', 2);
    $this->assertDatabaseCount('user_shifts', 2);
})->with('adjacent shift intervals');

test('users can be assigned to adjacent shifts when updating a shift', function (string $start, string $end) {
    createAdminControllerShift($this->role, [$this->worker]);
    $shift = createAdminControllerShift($this->role, [$this->worker], [
        'start_datetime' => '2026-11-16 14:00:00',
        'end_datetime' => '2026-11-16 16:00:00',
    ]);

    $this->putJson(route('adminUpdateShift'), array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'start_datetime' => $start,
        'end_datetime' => $end,
    ]))->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseCount('shifts', 2);
    $this->assertDatabaseCount('user_shifts', 2);
})->with('adjacent shift intervals');

test('users can be removed from a shift while retaining the other assignments', function () {
    $shift = createAdminControllerShift($this->role, [$this->worker, $this->secondWorker]);

    $this->putJson(route('adminUpdateShift'), array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
    ]))->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseCount('user_shifts', 1);
    $this->assertDatabaseHas('user_shifts', [
        'shift_id' => $shift->getAttribute('id'),
        'user_id' => $this->worker->getAttribute('id'),
    ]);
    $this->assertDatabaseMissing('user_shifts', [
        'shift_id' => $shift->getAttribute('id'),
        'user_id' => $this->secondWorker->getAttribute('id'),
    ]);
});

test('all users can be removed from a shift', function () {
    $shift = createAdminControllerShift($this->role, [$this->worker, $this->secondWorker]);

    $this->putJson(route('adminUpdateShift'), array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'employees' => [],
    ]))->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseCount('user_shifts', 0);
    $this->assertDatabaseHas('shifts', ['id' => $shift->getAttribute('id')]);
});

test('a shift role can be changed when its assigned users have the new role', function () {
    UserRole::query()->forceCreate([
        'user_id' => $this->worker->getAttribute('id'),
        'role_id' => $this->otherRole->getAttribute('id'),
    ]);
    $shift = createAdminControllerShift($this->role, [$this->worker]);

    $this->putJson(route('adminUpdateShift'), array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'shift_role' => $this->otherRole->getAttribute('id'),
    ]))->assertOk()->assertJsonPath('success', true);

    $this->assertDatabaseHas('shifts', [
        'id' => $shift->getAttribute('id'),
        'shift_role' => $this->otherRole->getAttribute('id'),
    ]);
    $this->assertDatabaseHas('user_shifts', [
        'shift_id' => $shift->getAttribute('id'),
        'user_id' => $this->worker->getAttribute('id'),
    ]);
});

test('users without the required role cannot be assigned when creating a shift', function (bool $hasOtherRole) {
    $wrongRoleUser = User::factory()->create();

    if ($hasOtherRole) {
        UserRole::query()->forceCreate([
            'user_id' => $wrongRoleUser->getAttribute('id'),
            'role_id' => $this->otherRole->getAttribute('id'),
        ]);
    }

    $payload = $this->payload;
    $payload['employees'][] = [
        'id' => $wrongRoleUser->getAttribute('id'),
        'name' => $wrongRoleUser->getAttribute('name'),
    ];

    $this->postJson(route('adminAddShift'), $payload)
        ->assertOk()
        ->assertJsonPath('success', false);

    $this->assertDatabaseCount('shifts', 0);
    $this->assertDatabaseCount('user_shifts', 0);
})->with('users without the required shift role');

test('users without the required role cannot be assigned when updating a shift', function (bool $hasOtherRole) {
    $wrongRoleUser = User::factory()->create();

    if ($hasOtherRole) {
        UserRole::query()->forceCreate([
            'user_id' => $wrongRoleUser->getAttribute('id'),
            'role_id' => $this->otherRole->getAttribute('id'),
        ]);
    }

    $shift = createAdminControllerShift($this->role, [$this->worker]);
    $payload = array_replace($this->payload, [
        'id' => $shift->getAttribute('id'),
        'title' => 'Changed title',
    ]);
    $payload['employees'][] = [
        'id' => $wrongRoleUser->getAttribute('id'),
        'name' => $wrongRoleUser->getAttribute('name'),
    ];

    $this->putJson(route('adminUpdateShift'), $payload)
        ->assertOk()
        ->assertJsonPath('success', false);

    $this->assertDatabaseCount('shifts', 1);
    $this->assertDatabaseCount('user_shifts', 1);
    $this->assertDatabaseHas('shifts', [
        'id' => $shift->getAttribute('id'),
        'title' => 'Existing shift',
    ]);
    $this->assertDatabaseHas('user_shifts', [
        'shift_id' => $shift->getAttribute('id'),
        'user_id' => $this->worker->getAttribute('id'),
    ]);
})->with('users without the required shift role');
