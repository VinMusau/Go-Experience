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
        Schema::table('dependants', function (Blueprint $table) {
            $table->string('blood_group')->nullable()->after('grade');
            $table->string('allergies')->nullable()->after('blood_group');
            $table->string('doctor_contact')->nullable()->after('allergies');
            $table->string('insurance_provider')->nullable()->after('doctor_contact');

            $table->foreignId('tag_id')->nullable()->constrained('tags')->onDelete('set null')->after('insurance_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dependants', function (Blueprint $table) {
            $table->dropColumn(['blood_group', 'allergies', 'doctor_contact', 'insurance_provider']);
            $table->dropForeign(['tag_id']);
            $table->dropColumn('tag_id');
        });
    }
};
