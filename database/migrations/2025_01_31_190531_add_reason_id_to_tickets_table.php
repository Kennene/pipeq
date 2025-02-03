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
        // Drop view, as always...
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('reason_id')->nullable()->default(null)->constrained('reasons')->nullOnDelete();
        });

        // Revert view
        DB::statement("
            CREATE VIEW tickets_view AS
            SELECT 
                tickets.id,
                users.name AS user,
                tickets.ticket_nr,
                destinations.name AS destination,
                tickets.destination_id,
                statuses.name AS status,
                colors.hex_code AS status_color,
                tickets.status_id,
                workstations.name AS workstation,
                tickets.workstation_id,
                tickets.created_at,
                reasons.description AS reason
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN colors ON statuses.color_id = colors.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
            LEFT JOIN reasons ON tickets.reason_id = reasons.id
        ");

        //* Messing with this table probably dropped both triggers, so here are the triggers again
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
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['reason_id']);
            $table->dropColumn('reason_id');
        });

        DB::statement("
            CREATE VIEW tickets_view AS
            SELECT 
                tickets.id,
                users.name AS user,
                tickets.ticket_nr,
                destinations.name AS destination,
                tickets.destination_id,
                statuses.name AS status,
                colors.hex_code AS status_color,
                tickets.status_id,
                workstations.name AS workstation,
                tickets.workstation_id,
                tickets.created_at
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN colors ON statuses.color_id = colors.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");

        // Drop triggers for backwards compatibility. however they should remain at all times
        DB::unprepared('DROP TRIGGER IF EXISTS after_tickets_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_tickets_insert');
    }
};
