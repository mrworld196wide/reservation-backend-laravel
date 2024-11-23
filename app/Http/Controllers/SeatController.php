<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    // Reserve Seats
    public function reserveSeats(Request $request)
    {
        $validated = $request->validate([
            'numSeats' => 'required|integer|min:1|max:7', // Validate the number of seats to reserve
            'bookedBy' => 'required|string', // Validate the name of the person booking
        ]);

        $numSeats = $validated['numSeats'];
        $bookedBy = $validated['bookedBy'];

        // Check if the number of seats is greater than available seats
        $availableSeatsCount = Seat::where('is_reserved', false)->count();

        if ($availableSeatsCount < $numSeats) {
            return response()->json([
                'error' => "Only $availableSeatsCount seats are available, cannot reserve $numSeats seats"
            ], 400);
        }

        // Find rows with available seats
        $rows = Seat::where('is_reserved', false)
            ->selectRaw('row_number, COUNT(*) as count')
            ->groupBy('row_number')
            ->havingRaw('count >= ?', [$numSeats])
            ->orderBy('row_number')
            ->get();

        $reservedSeats = [];

        // Try to reserve seats within one row if available
        foreach ($rows as $row) {
            $seats = Seat::where('row_number', $row->row_number)
                ->where('is_reserved', false)
                ->limit($numSeats)
                ->get();

            if ($seats->count() >= $numSeats) {
                $reservedSeats = $seats->pluck('id');
                break;
            }
        }

        // If not enough seats in one row, take them from different rows
        if (count($reservedSeats) === 0) {
            $seats = Seat::where('is_reserved', false)->limit($numSeats)->get();
            $reservedSeats = $seats->pluck('id');
        }

        // Update reserved seats in the database
        Seat::whereIn('id', $reservedSeats)
            ->update([
                'is_reserved' => true,
                'booked_by' => $bookedBy,
            ]);

        // Get the reserved seat details
        $reservedSeatDetails = Seat::whereIn('id', $reservedSeats)->get();

        return response()->json([
            'success' => true,
            'reservedSeats' => $reservedSeatDetails->map(function ($seat) {
                return [
                    'rowNumber' => $seat->row_number,
                    'seatNumber' => $seat->seat_number,
                ];
            })
        ]);
    }

    // Count Seats
    public function countSeats()
    {
        $availableSeatsCount = Seat::where('is_reserved', false)->count();
        $bookedSeatsCount = Seat::where('is_reserved', true)->count();

        return response()->json([
            'availableSeatsCount' => $availableSeatsCount,
            'bookedSeatsCount' => $bookedSeatsCount
        ]);
    }

    // Get All Seats
    public function getAllSeats()
    {
        $seats = Seat::all();

        // Add actual seat position (similar to your 'CoachPosition' logic)
        $seatsWithPosition = $seats->map(function ($seat) {
            return [
                'CoachPosition' => (7 * ($seat->row_number - 1)) + $seat->seat_number,
                'isReserved' => $seat->is_reserved
            ];
        });

        return response()->json($seatsWithPosition);
    }
}
