<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // seed the users
        $admin = User::factory()->create([
            'name' => 'Admin user',
            'email' => 'admin@crew.com',
            'password' => \Hash::make('admin'),
        ]);

        $cateringMan = User::factory()->create([
            'name' => 'Catering Man',
            'email' => 'cman@crew.com',
            'password' => \Hash::make('test'),
        ]);

        $categringWoman = User::factory()->create([
            'name' => 'Catering Woman',
            'email' => 'cwoman@crew.com',
            'password' => \Hash::make('test'),
        ]);

        $shiftLeader = User::factory()->create([
            'name' => 'Shift Leader',
            'email' => 'shiftleader@example.com',
            'password' => \Hash::make('test'),
        ]);

        // seed the roles
        $adminRole = Role::create([
            'name' => 'Admin',
            'color' => '#4287f5'
        ]);

        $cateringRole = Role::create([
            'name' => 'Catering',
            'color' => '#02d938'
        ]);

        $shiftLeaderRole = Role::create([
            'name' => 'Catering',
            'color' => '#d92a02'
        ]);

        // assign the roles to the users
        UserRole::create([
            'user_id' => $admin->id,
            'role_id' => $adminRole->id
        ]);
        UserRole::create([
            'user_id' => $cateringMan->id,
            'role_id' => $cateringRole->id
        ]);
        UserRole::create([
            'user_id' => $categringWoman->id,
            'role_id' => $cateringRole->id
        ]);
        UserRole::create([
            'user_id' => $shiftLeader->id,
            'role_id' => $shiftLeaderRole->id
        ]);

    }
}
