<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        
        $hashedPassword = Hash::make('rosset-swa@2026');

        foreach ($collection as $row) {
            
            $sn = trim($row['sn'] ?? '');
            if (empty($sn)) {
                continue;
            }

            $fullName = trim($row['name'] ?? '');

            
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0] ?? $fullName;
            $lastName = $nameParts[1] ?? '';

            User::updateOrCreate(
                ['membership_number' => $sn], 
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'school' => trim($row['school'] ?? ''),
                    'phone' => trim($row['phone'] ?? ''),
                  
                    'password' => $hashedPassword,
                    'status' => 'active',
                    'registration_fee_paid' => true,
                ]
            );
        }
    }
}