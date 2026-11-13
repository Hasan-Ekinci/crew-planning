<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AccountSettingsController extends Controller
{
    public function index(): Response
    {
        $account = \Auth::user();
        $roles = $account->getRelationValue('roles');
        $isAdminDashboard = in_array('Admin', $roles->pluck('name')->toArray());

        return Inertia::render('AccountSettings', [
            'isAdminDashboard' => $isAdminDashboard,
            'navigationItems' => $this->getNavigationItems($isAdminDashboard),
        ]);
    }

    private function getNavigationItems(bool $isAdminDashboard): array
    {
        if (!$isAdminDashboard) {
            return [];
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
