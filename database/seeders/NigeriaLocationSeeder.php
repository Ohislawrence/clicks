<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class NigeriaLocationSeeder extends Seeder
{
    /**
     * Seed Nigeria and all its 36 states + FCT.
     */
    public function run(): void
    {
        $nigeria = Country::firstOrCreate(
            ['code' => 'NG'],
            ['name' => 'Nigeria', 'is_active' => true]
        );

        $states = [
            ['name' => 'Abia', 'code' => 'AB', 'sort_order' => 1],
            ['name' => 'Adamawa', 'code' => 'AD', 'sort_order' => 2],
            ['name' => 'Akwa Ibom', 'code' => 'AK', 'sort_order' => 3],
            ['name' => 'Anambra', 'code' => 'AN', 'sort_order' => 4],
            ['name' => 'Bauchi', 'code' => 'BA', 'sort_order' => 5],
            ['name' => 'Bayelsa', 'code' => 'BY', 'sort_order' => 6],
            ['name' => 'Benue', 'code' => 'BE', 'sort_order' => 7],
            ['name' => 'Borno', 'code' => 'BO', 'sort_order' => 8],
            ['name' => 'Cross River', 'code' => 'CR', 'sort_order' => 9],
            ['name' => 'Delta', 'code' => 'DE', 'sort_order' => 10],
            ['name' => 'Ebonyi', 'code' => 'EB', 'sort_order' => 11],
            ['name' => 'Edo', 'code' => 'ED', 'sort_order' => 12],
            ['name' => 'Ekiti', 'code' => 'EK', 'sort_order' => 13],
            ['name' => 'Enugu', 'code' => 'EN', 'sort_order' => 14],
            ['name' => 'FCT (Abuja)', 'code' => 'FC', 'sort_order' => 15],
            ['name' => 'Gombe', 'code' => 'GO', 'sort_order' => 16],
            ['name' => 'Imo', 'code' => 'IM', 'sort_order' => 17],
            ['name' => 'Jigawa', 'code' => 'JI', 'sort_order' => 18],
            ['name' => 'Kaduna', 'code' => 'KD', 'sort_order' => 19],
            ['name' => 'Kano', 'code' => 'KN', 'sort_order' => 20],
            ['name' => 'Katsina', 'code' => 'KT', 'sort_order' => 21],
            ['name' => 'Kebbi', 'code' => 'KE', 'sort_order' => 22],
            ['name' => 'Kogi', 'code' => 'KO', 'sort_order' => 23],
            ['name' => 'Kwara', 'code' => 'KW', 'sort_order' => 24],
            ['name' => 'Lagos', 'code' => 'LA', 'sort_order' => 25],
            ['name' => 'Nasarawa', 'code' => 'NA', 'sort_order' => 26],
            ['name' => 'Niger', 'code' => 'NI', 'sort_order' => 27],
            ['name' => 'Ogun', 'code' => 'OG', 'sort_order' => 28],
            ['name' => 'Ondo', 'code' => 'ON', 'sort_order' => 29],
            ['name' => 'Osun', 'code' => 'OS', 'sort_order' => 30],
            ['name' => 'Oyo', 'code' => 'OY', 'sort_order' => 31],
            ['name' => 'Plateau', 'code' => 'PL', 'sort_order' => 32],
            ['name' => 'Rivers', 'code' => 'RI', 'sort_order' => 33],
            ['name' => 'Sokoto', 'code' => 'SO', 'sort_order' => 34],
            ['name' => 'Taraba', 'code' => 'TA', 'sort_order' => 35],
            ['name' => 'Yobe', 'code' => 'YO', 'sort_order' => 36],
            ['name' => 'Zamfara', 'code' => 'ZA', 'sort_order' => 37],
        ];

        foreach ($states as $state) {
            State::firstOrCreate(
                ['country_id' => $nigeria->id, 'code' => $state['code']],
                [
                    'name' => $state['name'],
                    'sort_order' => $state['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
