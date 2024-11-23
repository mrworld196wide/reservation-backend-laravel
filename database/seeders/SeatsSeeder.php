<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;

class SeatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, delete existing seats to avoid duplicates
        Seat::truncate();  // This will delete all existing rows

        // Seeding 80 seats: 11 rows of 7 seats and the last row of 3 seats
        $seats = [];

        for ($row = 1; $row <= 12; $row++) {
            // The last row has only 3 seats, others have 7
            $seatsInRow = ($row == 12) ? 3 : 7;

            for ($seat = 1; $seat <= $seatsInRow; $seat++) {
                $seats[] = [
                    'row_number' => $row,
                    'seat_number' => $seat,
                    'is_reserved' => false,  // Initially, seats are not reserved
                    'booked_by' => null,     // No one booked initially
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert the seats data into the database
        Seat::insert($seats);

        echo "Database seeded with seats!\n";


    }
}
