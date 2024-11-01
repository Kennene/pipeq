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
        Schema::create('tickets_ended', function (Blueprint $table) {
            $table->id();
            $table->integer('original_id');
            $table->string('user')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('ticket_nr')->nullable();
            $table->string('status')->nullable();
            $table->integer('status_id')->nullable();
            $table->string('destination')->nullable();
            $table->integer('destination_id')->nullable();
            $table->string('workstation')->nullable();
            $table->integer('workstation_id')->nullable();
            $table->datetime('original_created_at')->nullable();
            $table->datetime('original_updated_at')->nullable();
            $table->string('modified_by')->nullable();
            $table->datetime('created_at')->nullable()->useCurrent();
            $table->datetime('updated_by')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_ended');
    }
};
