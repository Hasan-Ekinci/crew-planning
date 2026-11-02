<?php

namespace App\Http\Controllers;

use App\Http\Requests\getAvailableEmployeesByRoleRequest;
use App\Http\Requests\AddOrUpdateShiftRequest;
use App\Http\Requests\GetShiftsRequest;
use Inertia\Inertia;
use Inertia\Response;
use App\Repositories\EmployeeRepository;
use App\Repositories\ShiftRepository;
use Carbon\Carbon;

class AdminController extends Controller {


    public function __construct(
        private EmployeeRepository $employeeRepository,
        private ShiftRepository $shiftRepository
    ) {}

    public function index(): Response
    {
        return Inertia::render('admin/Shifts', [
            'activePage' => 'shifts',
            'navigationItems' => $this->getNavigationItems(),
            'shifts' => $this->shiftRepository->getShiftsForWeek(now()->startOfWeek()),
            'weekStartDateTime' => now()->startOfWeek()
        ]);
    }

    public function employees(): Response
    {
        return Inertia::render('admin/Employees', [
            'activePage' => 'employees',
            'navigationItems' => $this->getNavigationItems()
        ]);
    }

    public function roles(): Response
    {
        return Inertia::render('admin/Roles', [
            'activePage' => 'roles',
            'navigationItems' => $this->getNavigationItems()
        ]);
    }

    public function getRoles(): \Illuminate\Support\Collection
    {
        return $this->employeeRepository->getEmployeeRoles();
    }

    public function getEmployeesByRole(getAvailableEmployeesByRoleRequest $request): \Illuminate\Support\Collection
    {
        $roleId = $request->input('roleId');
        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));
        $shiftId = $request->input('shiftId');
        return $this->employeeRepository->getAvailableEmployeesByRole($roleId, $start, $end, $shiftId);
    }

    public function getShifts(GetShiftsRequest $request): array
    {
        $newDate = Carbon::parse($request->input('oldDate'));
        $direction = $request->input('direction');

        // move the new date into the correct direction
        if ($direction === 'next') {
            $newDate = $newDate->addWeek()->startOfWeek();
        } else if ($direction === 'previous') {
            $newDate = $newDate->subWeek()->startOfWeek();
        }

        return [
            'shifts' => $this->shiftRepository->getShiftsForWeek($newDate),
            'newWeekStartDate' => $newDate
        ];
    }

    public function AddShift(AddOrUpdateShiftRequest $request): array
    {
        return $this->shiftRepository->addShiftOrUpdate($request);
    }

    public function UpdateShift(AddOrUpdateShiftRequest $request): array
    {
        return $this->shiftRepository->addShiftOrUpdate($request);
    }

    private function getNavigationItems(): array
    {
        return [
            'shifts' => [
                'name' => 'shifts',
                'label' => 'Shifts',
                'route' => route('admin')
            ],
            'employees' => [
                'name' => 'employees',
                'label' => 'Employees',
                'route' => route('adminEmployees')
            ],
            'roles' => [
                'name' => 'roles',
                'label' => 'Roles',
                'route' => route('adminRoles')
            ],
        ];
    }
}
