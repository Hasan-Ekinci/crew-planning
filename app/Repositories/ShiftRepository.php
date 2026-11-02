<?php

namespace App\Repositories;

use App\models\UserShift;
use App\models\Shift;
use App\Http\Requests\AddOrUpdateShiftRequest;
use Carbon\Carbon;

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
                        'message' => "Maximum werknemers ingesteld op {$maxCrew}. Je hebt er {$usersCount} geslecteerd"
                    ];
        }
        

        if (count($users) > 0) {

            // check for each user-id if the have a shift at that same timespan
            foreach($users as $user) {
                $isAlreadyPlannedIn = UserShift::query()
                    ->join('shifts', function($query) use ($user, $start_datetime, $end_datetime, $shiftId) {
                        $query->on('user_shifts.shift_id', 'shifts.id')
                            ->where('user_shifts.user_id', $user['id'])
                            ->whereNot('user_shifts.shift_id', $shiftId)
                            ->where(function ($query) use ($start_datetime, $end_datetime) {
                                $query->where('start_datetime', '>=', $start_datetime)
                                    ->where('start_datetime' , '<', $end_datetime)
                                    ->orWhere('end_datetime', '>=', $start_datetime)
                                    ->where('end_datetime', '<', $end_datetime);
                            });
                    })
                    ->count() > 0;

                if ($isAlreadyPlannedIn) {
                    $name = $user['name'];
                    return [
                        'success' => false,
                        'message' => "{$name} is op dat moment al ingepland"
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
