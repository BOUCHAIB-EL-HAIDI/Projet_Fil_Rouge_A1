<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::firstOrCreate(
            ['name' => 'IT Department'],
            ['manager_id' => null]
        );
        $hr = Department::firstOrCreate(
            ['name' => 'HR Department'],
            ['manager_id' => null]
        );

        User::updateOrCreate(
            ['email' => 'demandeur@example.com'],
            [
                'name' => 'Demandeur User',
                'password' => 'password',
                'role' => 'demandeur',
                'post' => 'Technicien support',
                'department_id' => $it->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => 'password',
                'role' => 'manager',
                'post' => 'Chef de projet',
                'department_id' => $it->id,
            ]
        );

        $managerHr = User::updateOrCreate(
            ['email' => 'manager.hr@example.com'],
            [
                'name' => 'Manager RH',
                'password' => 'password',
                'role' => 'manager',
                'post' => 'Responsable du departement RH',
                'department_id' => $hr->id,
            ]
        );
        $hr->update(['manager_id' => $managerHr->id]);

        User::updateOrCreate(
            ['email' => 'directeur@example.com'],
            [
                'name' => 'Directeur User',
                'password' => 'password',
                'role' => 'directeur',
                'post' => 'Directeur général',
                'department_id' => $it->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'acheteur@example.com'],
            [
                'name' => 'Acheteur User',
                'password' => 'password',
                'role' => 'acheteur',
                'post' => 'Acheteur',
                'department_id' => $it->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo Staff',
                'password' => 'password',
                'role' => 'demandeur',
                'post' => 'Stagiaire Qualité',
                'department_id' => $it->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
                'role' => 'demandeur',
                'post' => 'Stagiaire',
                'department_id' => $hr->id,
            ]
        );
    }
}
