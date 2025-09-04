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
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'name']);
        });
        Schema::create('task_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Low, Medium, High, Critical
            $table->string('slug')->unique();
            $table->integer('level')->unique(); // 1=Low, 2=Medium, 3=High, 4=Critical
            $table->string('color', 7)->default('#6B7280');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'level']);
        });
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Pending, In Progress, Review, Completed, Cancelled
            $table->string('slug')->unique();
            $table->integer('order')->default(0); // For ordering statuses
            $table->string('color', 7)->default('#6B7280');
            $table->boolean('is_completed')->default(false); // For filtering completed tasks
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'order']);
            $table->index('is_completed');
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('task_categories')->onDelete('set null');
            $table->foreignId('priority_id')->nullable()->constrained('task_priorities')->onDelete('set null');
            $table->foreignId('status_id')->constrained('task_statuses')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            // Time tracking
            $table->datetime('start_date')->nullable();
            $table->datetime('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();

            // Progress tracking
            $table->integer('progress')->default(0); // 0-100 percentage

            // Recurring task fields
            $table->boolean('is_recurring')->default(false);
            $table->enum('repeat_mode', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->integer('repeat_interval')->nullable(); // Every X days/weeks/months/years
            $table->json('repeat_days')->nullable(); // For weekly: [1,2,3] (Mon,Tue,Wed)
            $table->date('repeat_until')->nullable(); // End date for recurrence
            $table->integer('repeat_count')->nullable(); // Or end after X occurrences

            // Additional metadata
            $table->json('tags')->nullable(); // Flexible tagging system
            $table->json('metadata')->nullable(); // For custom fields
            $table->boolean('is_archived')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status_id', 'created_at']);
            $table->index(['category_id', 'priority_id']);
            $table->index(['due_date', 'is_archived']);
            $table->index(['created_by', 'is_archived']);
            $table->index('is_archived');
        });

        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');

            // Assignment metadata
            $table->enum('role', ['assignee', 'reviewer', 'observer'])->default('assignee');
            $table->text('assignment_notes')->nullable();
            $table->datetime('assigned_at');
            $table->datetime('accepted_at')->nullable();
            $table->datetime('completed_at')->nullable();

            // Time tracking per assignee
            $table->integer('estimated_hours')->nullable();
            $table->integer('logged_hours')->default(0);

            // Status tracking
            $table->boolean('is_active')->default(true);
            $table->boolean('notifications_enabled')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate assignments
            $table->unique(['task_id', 'user_id', 'role']);

            // Indexes
            $table->index(['task_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index(['assigned_by', 'assigned_at']);
        });

        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('task_comments')->onDelete('cascade');

            $table->text('content');
            $table->json('mentions')->nullable(); // Store mentioned user IDs
            $table->boolean('is_internal')->default(false); // For internal team comments
            $table->boolean('is_edited')->default(false);
            $table->datetime('edited_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('parent_id');
        });

        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'created_at']);
            $table->index('uploaded_by');
        });

        Schema::create('task_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('action'); // created, updated, assigned, completed, etc.
            $table->string('field')->nullable(); // Field that was changed
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description'); // Human readable description
            $table->json('metadata')->nullable(); // Additional context

            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });

        Schema::create('task_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->datetime('started_at');
            $table->datetime('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable(); // Auto-calculated
            $table->text('description')->nullable();
            $table->boolean('is_billable')->default(false);
            $table->decimal('hourly_rate', 8, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['task_id', 'started_at']);
            $table->index(['user_id', 'started_at']);
            $table->index('is_billable');
        });
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('depends_on_task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->enum('type', ['blocks', 'requires', 'related'])->default('blocks');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Prevent self-dependency and duplicate dependencies
            $table->unique(['task_id', 'depends_on_task_id']);

            $table->index(['task_id', 'type']);
            $table->index('depends_on_task_id');
        });

        Schema::create('recurring_task_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('instance_task_id')->constrained('tasks')->onDelete('cascade');
            $table->date('scheduled_date');
            $table->integer('instance_number');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_task_id', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_categories');
        Schema::dropIfExists('task_priorities');
        Schema::dropIfExists('task_statuses');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_activity_logs');
        Schema::dropIfExists('task_time_logs');
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('recurring_task_instances');
    }
};
