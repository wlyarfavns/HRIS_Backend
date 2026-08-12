<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ShiftType;
use Illuminate\Database\Seeder;

class ShiftTypeSeeder extends Seeder
{
    public function run(): void
    {
        Company::all()->each(function (Company $company) {
            $defaults = [
                ['code' => 'P', 'name' => 'Shift Pagi', 'start_time' => '08:00', 'end_time' => '17:00', 'is_cross_day' => false, 'is_off' => false, 'color' => '#059669', 'description' => 'Jam operasional standar kantor & sales'],
                ['code' => 'S', 'name' => 'Shift Siang', 'start_time' => '13:00', 'end_time' => '22:00', 'is_cross_day' => false, 'is_off' => false, 'color' => '#d97706', 'description' => 'Dukungan operasional & customer service'],
                ['code' => 'M', 'name' => 'Shift Malam', 'start_time' => '22:00', 'end_time' => '07:00', 'is_cross_day' => true, 'is_off' => false, 'color' => '#7c3aed', 'description' => 'Cross-day shift logistik & IT monitoring'],
                ['code' => 'L', 'name' => 'Libur (Off)', 'start_time' => null, 'end_time' => null, 'is_cross_day' => false, 'is_off' => true, 'color' => '#64748b', 'description' => 'Hari istirahat mingguan / roster off'],
            ];

            foreach ($defaults as $d) {
                ShiftType::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $d['code']],
                    $d + ['company_id' => $company->id]
                );
            }
        });
    }
}