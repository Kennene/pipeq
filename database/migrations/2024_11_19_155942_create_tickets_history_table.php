<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old table
        Schema::dropIfExists('tickets_ended');

        // Create the new table.
        Schema::create('tickets_history', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->nullable();
            $table->integer('ticket_nr')->nullable();
            $table->string('token')->nullable();
            $table->string('username')->nullable();
            $table->integer('status')->nullable();
            $table->integer('destination')->nullable();
            $table->integer('workstation')->nullable();
            $table->datetime('original_created_at')->nullable();
            $table->datetime('original_updated_at')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamps();
        });

        // Triggers for insert and update
        DB::unprepared('
            CREATE TRIGGER after_tickets_insert
            AFTER INSERT ON tickets
            FOR EACH ROW
            BEGIN
                INSERT INTO tickets_history (ticket_id, ticket_nr, token, username, status, destination, workstation, original_created_at, original_updated_at, modified_by, created_at, updated_at)
                SELECT 
                    NEW.id, 
                    NEW.ticket_nr, 
                    NEW.token, 
                    (SELECT name FROM users WHERE id = NEW.user_id), 
                    NEW.status_id, 
                    NEW.destination_id, 
                    NEW.workstation_id, 
                    NEW.created_at, 
                    NEW.updated_at, 
                    (SELECT name FROM users WHERE id = NEW.modified_by), 
                    CURRENT_TIMESTAMP, 
                    CURRENT_TIMESTAMP;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER after_tickets_update
            AFTER UPDATE ON tickets
            FOR EACH ROW
            BEGIN
                INSERT INTO tickets_history (ticket_id, ticket_nr, token, username, status, destination, workstation, original_created_at, original_updated_at, modified_by, created_at, updated_at)
                SELECT 
                    NEW.id, 
                    NEW.ticket_nr, 
                    NEW.token, 
                    (SELECT name FROM users WHERE id = NEW.user_id), 
                    NEW.status_id, 
                    NEW.destination_id, 
                    NEW.workstation_id, 
                    NEW.created_at, 
                    NEW.updated_at, 
                    (SELECT name FROM users WHERE id = NEW.modified_by), 
                    CURRENT_TIMESTAMP, 
                    CURRENT_TIMESTAMP;
            END;
        ');
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS after_tickets_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_tickets_insert');

        // Drop table
        Schema::dropIfExists('tickets_history');

        // Recreate the old table
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
};
