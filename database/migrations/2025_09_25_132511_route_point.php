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
        Schema::create('route_point', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->foreignId('point_id')->constrained()->onDelete('cascade');
            $table->primary(['route_id', 'point_id']);
            $table->integer('day_of_the_route');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_point');
    }
};
