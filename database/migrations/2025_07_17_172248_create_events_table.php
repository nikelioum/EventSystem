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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index('events_title_index');
            $table->text('description')->nullable();
            $table->dateTime('event_date')->index('events_event_date_index');
            $table->string('location');
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('current_registrations_count')->default(0);
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('draft')->index('events_status_index');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
