<?php

namespace App\Repositories;

use App\Models\UserShift;
use App\Models\Shift;
use App\Models\User;
use App\Http\Requests\AcceptShiftRequest;
use App\Http\Requests\AddOrUpdateShiftRequest;
use App\Http\Requests\DeleteShiftRequest;
use App\Models\UserShiftConfirmation;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftRepository {
    public function getShiftsForWeek(Carbon $date): \Illuminate\Support\Collection
    {
        return Shift::query()
            ->where('start_datetime', '>=', $date->copy()->startOfWeek())
            ->where('end_datetime', '<=', $date->copy()->endOfWeek())
            ->with('users', function ($query) {
                $query->select(['users.id', 'name']);
            })
            ->get();
    }

    public function getUserShiftsForWeek(Carbon $date, int $userId): \Illuminate\Support\Collection
    {
        return Shift::query()
            ->where('start_datetime', '<', $date->copy()->startOfWeek()->addWeek())
            ->where('end_datetime', '>', $date->copy()->startOfWeek())
            ->whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->with('users', function ($query) {
                $query->select(['users.id', 'name']);
            })
            ->get();
    }

    public function getAvailableShiftsForWeek(Carbon $date, int $userId): \Illuminate\Support\Collection
    {
        return Shift::query()
            ->where('start_datetime', '<', $date->copy()->startOfWeek()->addWeek())
            ->where('end_datetime', '>', $date->copy()->startOfWeek())
            ->whereIn('shift_role', UserRole::query()
                ->select('role_id')
                ->where('user_id', $userId)
            )
            ->where('max_crew', '>', UserShift::query()
                ->selectRaw('COUNT(*)')
                ->whereColumn('user_shifts.shift_id', 'shifts.id')
            )
            ->whereNotExists(UserShift::query()
                ->selectRaw('1')
                ->join('shifts as assigned_shifts', 'user_shifts.shift_id', '=', 'assigned_shifts.id')
                ->where('user_shifts.user_id', $userId)
                ->whereColumn('assigned_shifts.start_datetime', '<', 'shifts.end_datetime')
                ->whereColumn('assigned_shifts.end_datetime', '>', 'shifts.start_datetime')
            )
            ->with('users', function ($query) {
                $query->select(['users.id', 'name']);
            })
            ->get();
    }

    public function acceptShift(AcceptShiftRequest $request): array
    {
        $userId = $request->user()->getAuthIdentifier();
        $shiftId = $request->integer('id');

        return DB::transaction(function () use ($userId, $shiftId) {
            // Serialize acceptances for both the crew member and the shift.
            User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
            $shift = Shift::query()->lockForUpdate()->findOrFail($shiftId);

            $hasRole = UserRole::query()
                ->where('user_id', $userId)
                ->where('role_id', $shift->getAttribute('shift_role'))
                ->exists();

            if (!$hasRole) {
                return [
                    'success' => false,
                    'message' => 'You do not have the required role for this shift.',
                ];
            }

            $isAssigned = UserShift::query()
                ->where('user_id', $userId)
                ->where('shift_id', $shiftId)
                ->exists();

            if ($isAssigned) {
                return [
                    'success' => false,
                    'message' => 'You are already assigned to this shift.',
                ];
            }

            $crewCount = UserShift::query()->where('shift_id', $shiftId)->count();

            if ($crewCount >= $shift->getAttribute('max_crew')) {
                return [
                    'success' => false,
                    'message' => 'This shift is fully staffed.',
                ];
            }

            $hasOverlap = UserShift::query()
                ->join('shifts', 'user_shifts.shift_id', '=', 'shifts.id')
                ->where('user_shifts.user_id', $userId)
                ->where('shifts.start_datetime', '<', $shift->getAttribute('end_datetime'))
                ->where('shifts.end_datetime', '>', $shift->getAttribute('start_datetime'))
                ->exists();

            if ($hasOverlap) {
                return [
                    'success' => false,
                    'message' => 'You are already assigned to another shift during this time.',
                ];
            }

            UserShift::query()->create([
                'user_id' => $userId,
                'shift_id' => $shiftId,
            ]);

            return [
                'success' => true,
                'message' => '',
            ];
        });
    }

    public function deleteShift(DeleteShiftRequest $request): array
    {
        $shiftId = $request->input('id');

        return DB::transaction(function () use ($shiftId) {
            $shift = Shift::query()->findOrFail($shiftId);

            UserShiftConfirmation::query()
                ->whereIn('user_shift_id', UserShift::query()->where('shift_id', $shiftId)->select('id'))
                ->delete();

            UserShift::query()
                ->where('shift_id', $shiftId)
                ->delete();

            $shift->delete();

            return [
                'success' => true,
                'message' => '',
            ];
        });
    }

    public function addShiftOrUpdate(AddOrUpdateShiftRequest $request): array
    {
        $shiftId = $request->input('id');
        $isUpdate = !is_null($shiftId);

        // check wether all users can be assigned to this shift first
        $users = $request->input('employees');

        $start_datetime = $request->input('start_datetime');
        $end_datetime = $request->input('end_datetime');

        // if the amount of users is more than the max, return the error
        if (($usersCount = count($users)) > ($maxCrew = $request->input('max_crew'))) {
            return [
                        'success' => false,
                        'message' => "This shift has a maximum crew size of {$maxCrew}. You selected {$usersCount}."
                    ];
        }
        

        if (count($users) > 0) {
            $eligibleUserIds = UserRole::query()
                ->where('role_id', $request->input('shift_role'))
                ->whereIn('user_id', array_column($users, 'id'))
                ->pluck('user_id');

            // Check role eligibility and availability before saving any changes.
            foreach($users as $user) {
                if (!$eligibleUserIds->contains($user['id'])) {
                    $name = $user['name'];

                    return [
                        'success' => false,
                        'message' => "{$name} does not have the required role for this shift.",
                    ];
                }

                $isAlreadyPlannedIn = UserShift::query()
                    ->join('shifts', function($query) use ($user, $start_datetime, $end_datetime, $shiftId) {
                        $query->on('user_shifts.shift_id', 'shifts.id')
                            ->where('user_shifts.user_id', $user['id'])
                            ->whereNot('user_shifts.shift_id', $shiftId)
                            ->where('shifts.start_datetime', '<', $end_datetime)
                            ->where('shifts.end_datetime', '>', $start_datetime);
                    })
                    ->exists();

                if ($isAlreadyPlannedIn) {
                    $name = $user['name'];
                    return [
                        'success' => false,
                        'message' => "{$name} is already assigned to another shift during this time."
                    ];
                }
            }
        }

        // create the shift
        $shift = Shift::query()
            ->updateOrCreate(
            [
                'id' => $shiftId
            ],    
            [
                'title' => $request->input('title'),
                'description' => $request->input('description') ?? '',
                'shift_role' => $request->input('shift_role'),
                'max_crew' => $request->input('max_crew'),
                'start_datetime' =>$request->input('start_datetime'),
                'end_datetime' => $request->input('end_datetime'),
            ]);

        // if it is an update call, remove all linked users first
        if ($isUpdate) {
            UserShift::query()
                ->where('shift_id', $request->input('id'))
                ->delete();
        }

        // add the users to the shift
        foreach($users as $user) {
            UserShift::query()
                ->create([
                    'user_id' => $user['id'],
                    'shift_id' => $shift->getAttribute('id')
                ]);
        }

        return [
            'success' => true,
            'message' => ''
        ];
    }
}
