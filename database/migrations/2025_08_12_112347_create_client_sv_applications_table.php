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
        Schema::create('client_sv_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('client_details')
                ->onDelete('cascade');
            $table->foreignId('client_lead_reg_id')
                ->constrained('client_lead_registrations')
                ->onDelete('cascade');
            $table->date('date')->nullable();
            $table->foreignId('country')
                ->constrained('foreign_countries')
                ->onDelete('cascade');
            $table->foreignId('university')
                ->constrained('universities')
                ->onDelete('cascade');
            $table->foreignId('campus')
                ->constrained('campuses')
                ->onDelete('cascade');
            $table->foreignId('credentials')
                ->constrained('course_types')
                ->onDelete('cascade');
            $table->foreignId('program')
                ->constrained('university_courses')
                ->onDelete('cascade');
            $table->foreignId('application_type')
                ->constrained('application_types')
                ->onDelete('cascade');
            $table->foreignId('intake')
                ->constrained('intakes')
                ->onDelete('cascade');
            $table->foreignId('application_through')
                ->constrained('associates')
                ->onDelete('cascade');
            $table->text('added_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sv_applications');
    }
};
