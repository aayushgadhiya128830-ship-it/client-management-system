<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['Amit Shah',     'amit@example.com',   '9876543210', 'ABC Pvt. Ltd.',       'Rajkot',    'active'],
            ['Bhavik Patel',  'bhavik@example.com', '9123456780', 'XYZ Solutions',       'Ahmedabad', 'active'],
            ['Dhruv Mehta',   'dhruv@example.com',  '9988776655', 'TechCorp',            'Surat',     'active'],
            ['Isha Desai',    'isha@example.com',   '9012345678', 'Global Systems',      'Vadodara',  'inactive'],
            ['Jayesh Joshi',  'jayesh@example.com', '9765432109', 'Innovative Labs',     'Rajkot',    'active'],
            ['Kavya Sharma',  'kavya@example.com',  '9823456789', 'NextGen Pvt. Ltd.',   'Morbi',     'active'],
            ['Rohan Soni',    'rohan@example.com',  '9654321098', 'Sunrise Enterprises', 'Jamnagar',  'inactive'],
            ['Sneha Verma',   'sneha@example.com',  '9543210987', 'DreamWorks',          'Rajkot',    'active'],
        ];

        foreach ($clients as [$name, $email, $phone, $company, $city, $status]) {
            Client::updateOrCreate(
                ['email' => $email],
                [
                    'name'         => $name,
                    'phone'        => $phone,
                    'company_name' => $company,
                    'city'         => $city,
                    'address'      => "{$company}, {$city}, Gujarat, India",
                    'status'       => $status,
                ]
            );
        }
    }
}
