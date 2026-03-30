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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('dependant_id')->constrained()->onDelete('cascade');
            $table->string('device_id')->unique();
            $table->integer('battery_level')->default(100);
            $table->enum('status', ['active', 'inactive', 'low_battery'])->default('active');
            $table->timestamp('last_ping_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
