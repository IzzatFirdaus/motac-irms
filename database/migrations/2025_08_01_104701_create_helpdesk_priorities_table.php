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
        Schema::create('helpdesk_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('level')->default(0); // For sorting purposes (e.g., 1 for Low, 5 for Critical)
            $table->string('color_code')->nullable(); // e.g., #FF0000 for Critical
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helpdesk_priorities');
    }
};
