<?php

namespace App\Repositories;

use App\Http\Requests\SaveEmployeeRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function getEmployee(int $id): User
    {
        return User::query()
            ->select(['id', 'name', 'email'])
            ->with('roles', function ($query) {
                $query->select(['roles.id', 'roles.name']);
            })
            ->findOrFail($id);
    }

    public function saveEmployee(SaveEmployeeRequest $request): array
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            if (isset($data['id'])) {
                $employee = User::query()
                    ->select(['id', 'name', 'email'])
                    ->findOrFail($data['id']);
            } else {
                $employee = new User();
                $employee->setAttribute('password', Hash::make(Str::random(64)));
            }

            $employee->fill([
                'name' => $data['name'],
                'email' => $data['email'],
            ])->save();

            UserRole::query()
                ->where('user_id', $employee->getAttribute('id'))
                ->delete();

            $userRoles = [];
            $timestamp = now();

            foreach ($data['roles'] as $roleId) {
                $userRoles[] = [
                    'user_id' => $employee->getAttribute('id'),
                    'role_id' => $roleId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            UserRole::query()->insert($userRoles);

            return [
                'success' => true,
                'message' => '',
            ];
        });
    }

    public function getAllRoles(): \Illuminate\Support\Collection
    {
        return Role::query()
            ->select(['id', 'name'])
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
