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
        // need to drop view, because it depends on statuses table
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        // update statuses table
        Schema::table('statuses', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
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
                tickets.created_at
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN colors ON statuses.color_id = colors.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('statuses', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropColumn('color_id');
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
                tickets.status_id,
                workstations.name AS workstation,
                tickets.workstation_id,
                tickets.created_at
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");
    }
};
