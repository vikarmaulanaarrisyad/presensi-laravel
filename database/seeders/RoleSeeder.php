<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new Role;
        $admin->name = 'admin';
        $admin->save();

        $guru = new Role;
        $guru->name = 'guru';
        $guru->save();
    }
}
