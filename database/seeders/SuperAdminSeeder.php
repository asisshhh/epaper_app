<?php


namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        AdminUser::updateOrCreate(
            ['email' => 'superadmin@defigo.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@defigo.com',
                'password' => Hash::make('@Defigo_#admin07'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}