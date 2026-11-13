<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;

class EmployeeRepository {
    public function getEmployees(): \Illuminate\Support\Collection
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->with('roles', function ($query) {
                $query->select(['roles.name']);
            })
            ->orderBy('name')
            ->get();
    }

    public function getEmployeeRoles(): \Illuminate\Support\Collection
    {
        return Role::query()
            ->whereNot('name', 'Admin')
            ->select(['id', 'name'])
            ->groupBy(['id', 'name'])
            ->get();
    }

    public function getAvailableEmployeesByRole(int $roleId, Carbon $start, Carbon $end, ?int $shiftId): \Illuminate\Support\Collection
    {
        return User::query()
        ->select(['users.id', 'name'])
        ->whereHas('Roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId)
                ->select(['roles.id', 'name']);
        })
        ->whereDoesntHave('shifts', function ($query) use ($start, $end, $shiftId) {
            $query
            ->where(function ($query) use ($start, $end){
                $query->where('start_datetime', '>', $start)
                ->where('start_datetime' , '<', $end)
                ->orWhere('end_datetime', '>', $start)
                ->where('end_datetime', '<', $end);
            })
            ->whereNot('shifts.id', $shiftId);
            
        })
        ->get();
    }
}
