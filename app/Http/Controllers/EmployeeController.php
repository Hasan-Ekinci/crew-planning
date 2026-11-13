<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEmployeeRequest;
use App\Repositories\EmployeeRepository;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeRepository $employeeRepository
    ) {}

    public function index(): Response
    {
        return Inertia::render('admin/Employees', [
            'activePage' => 'employees',
            'navigationItems' => $this->getNavigationItems(),
            'employees' => $this->employeeRepository->getEmployees(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Employees/EmployeeForm', [
            'activePage' => 'employees',
            'navigationItems' => $this->getNavigationItems(),
            'employee' => null,
            'roles' => $this->employeeRepository->getAllRoles(),
        ]);
    }

    public function edit(int $id): Response
    {
        return Inertia::render('admin/Employees/EmployeeForm', [
            'activePage' => 'employees',
            'navigationItems' => $this->getNavigationItems(),
            'employee' => $this->employeeRepository->getEmployee($id),
            'roles' => $this->employeeRepository->getAllRoles(),
        ]);
    }

    public function save(SaveEmployeeRequest $request): array
    {
        return $this->employeeRepository->saveEmployee($request);
    }

    private function getNavigationItems(): array
    {
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
