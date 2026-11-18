<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptShiftRequest;
use App\Http\Requests\GetShiftsRequest;
use App\Repositories\ShiftRepository;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class CrewController extends Controller
{
    public function __construct(
        private ShiftRepository $shiftRepository
    ) {}

    public function index(): Response
    {
        $weekStartDateTime = now()->startOfWeek();

        return Inertia::render('crew/Shifts', [
            'activePage' => 'shifts',
            'navigationItems' => $this->getNavigationItems(),
            'currentUserId' => \Auth::id(),
            'shifts' => $this->shiftRepository->getUserShiftsForWeek($weekStartDateTime, \Auth::id()),
            'weekStartDateTime' => $weekStartDateTime,
        ]);
    }

    public function getShifts(GetShiftsRequest $request): array
    {
        $newDate = Carbon::parse($request->input('oldDate'));
        $direction = $request->input('direction');

        if ($direction === 'next') {
            $newDate = $newDate->addWeek()->startOfWeek();
        } else if ($direction === 'previous') {
            $newDate = $newDate->subWeek()->startOfWeek();
        }

        return [
            'shifts' => $this->shiftRepository->getUserShiftsForWeek($newDate, \Auth::id()),
            'newWeekStartDate' => $newDate,
        ];
    }

    public function findShifts(): Response
    {
        $weekStartDateTime = now()->startOfWeek();

        return Inertia::render('crew/FindShifts', [
            'activePage' => 'findShifts',
            'navigationItems' => $this->getNavigationItems(),
            'currentUserId' => \Auth::id(),
            'shifts' => $this->shiftRepository->getAvailableShiftsForWeek($weekStartDateTime, \Auth::id()),
            'weekStartDateTime' => $weekStartDateTime,
        ]);
    }

    public function getAvailableShifts(GetShiftsRequest $request): array
    {
        $newDate = Carbon::parse($request->input('oldDate'));
        $direction = $request->input('direction');

        if ($direction === 'next') {
            $newDate = $newDate->addWeek()->startOfWeek();
        } else if ($direction === 'previous') {
            $newDate = $newDate->subWeek()->startOfWeek();
        }

        return [
            'shifts' => $this->shiftRepository->getAvailableShiftsForWeek($newDate, \Auth::id()),
            'newWeekStartDate' => $newDate,
        ];
    }

    public function acceptShift(AcceptShiftRequest $request): array
    {
        return $this->shiftRepository->acceptShift($request);
    }

    private function getNavigationItems(): array
    {
        return [
            'shifts' => [
                'name' => 'shifts',
                'label' => 'My Shifts',
                'route' => route('crew'),
            ],
            'findShifts' => [
                'name' => 'findShifts',
                'label' => 'Find shifts',
                'route' => route('crewFindShifts'),
            ],
        ];
    }
}
