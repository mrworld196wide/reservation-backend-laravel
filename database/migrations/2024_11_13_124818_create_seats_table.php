<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();  // auto-increment ID
            $table->integer('row_number');  // rowNumber from schema
            $table->integer('seat_number'); // seatNumber from schema
            $table->boolean('is_reserved')->default(false); // isReserved from schema
            $table->string('booked_by')->nullable()->default(null); // bookedBy from schema
            $table->timestamps();  // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
