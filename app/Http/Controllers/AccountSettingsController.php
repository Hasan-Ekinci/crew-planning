<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAccountSettingsRequest;
use App\Repositories\EmployeeRepository;
use Inertia\Inertia;
use Inertia\Response;

class AccountSettingsController extends Controller
{
    public function __construct(
        private EmployeeRepository $employeeRepository
    ) {}

    public function index(): Response
    {
        $account = \Auth::user();
        $roles = $account->getRelationValue('roles');
        $isAdminDashboard = in_array('Admin', $roles->pluck('name')->toArray());

        return Inertia::render('AccountSettings', [
            'isAdminDashboard' => $isAdminDashboard,
            'navigationItems' => $this->getNavigationItems($isAdminDashboard),
            'account' => $account->only(['name', 'email']),
        ]);
    }

    public function save(SaveAccountSettingsRequest $request): array
    {
        return $this->employeeRepository->saveAccountSettings($request);
    }

    private function getNavigationItems(bool $isAdminDashboard): array
    {
        if (!$isAdminDashboard) {
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

        return [
            'shifts' => [
                'name' => 'shifts',
                'label' => 'Shifts',
                'route' => route('admin'),
            ],
            'employees' => [
                'name' => 'employees',
                'label' => 'Employees',
                'route' => route('adminEmployees'),
            ],
            'roles' => [
                'name' => 'roles',
                'label' => 'Roles',
                'route' => route('adminRoles'),
            ],
        ];
    }
}
