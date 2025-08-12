<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('university_course_values', function (Blueprint $table) {
            $table->foreignId('key_id')
                ->constrained('university_course_keys')
                ->onDelete('cascade');
            $table->foreignId('university_course_id')
                ->constrained('university_courses')
                ->onDelete('cascade');
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_course_values');
    }
};
