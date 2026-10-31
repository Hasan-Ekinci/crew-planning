<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller {
    public function index(): Response
    {
        return Inertia::render('admin/Shifts', [
            'activePage' => 'shifts',
            'navigationItems' => $this->getNavigationItems()
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