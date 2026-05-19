<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'=> 'Administrateur',
                'description'  => 'Accès complet à toute l\'application',
            ],
            [
                'name'=> 'Manager',
                'description'  => 'Gestion des projets et des équipes',
            ],
            [
                'name'=> 'Ressources Humaines',
                'description'  => 'Gestion administrative des employés',
            ],
            [
                'name'=> 'Employé',
                'description'  => 'Accès limité aux informations personnelles',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Roles créés avec succès!');
    }
}