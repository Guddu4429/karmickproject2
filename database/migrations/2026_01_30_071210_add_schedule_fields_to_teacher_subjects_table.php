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
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->date('date')->nullable()->after('class_id');
            $table->time('time')->nullable()->after('date');
            $table->enum('status', ['scheduled', 'attended', 'absent', 'cancelled'])->default('scheduled')->after('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_subjects', function (Blueprint $table) {
            $table->dropColumn(['date', 'time', 'status', 'created_at', 'updated_at']);
        });
    }
};
