<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

class EmployeeRepository {
    public function getEmployeeRoles(): \Illuminate\Support\Collection
    {
        return Role::query()
            ->whereNot('name', 'Admin')
            ->select(['id', 'name'])
            ->groupBy(['id', 'name'])
            ->get();
    }

    public function getAvailableEmployeesByRole(int $roleId, Carbon $start, Carbon $end): \Illuminate\Support\Collection
    {
        return User::query()
        ->select(['users.id', 'name'])
        ->whereHas('Roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId)
                ->select(['roles.id', 'name']);
        })
        ->whereDoesntHave('shifts', function ($query) use ($start, $end) {
            $query->where('start_datetime', '>', $start)
            ->where('start_datetime' , '<', $end)
            ->orWhere('end_datetime', '>', $start)
            ->where('end_datetime', '<', $end);
        })
        ->get();
    }
}