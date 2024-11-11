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
        // sqlite doesnt handle updating views automatically so it is necessary to do it manually
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['acl_id']);
            $table->dropColumn('acl_id');
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
                tickets.workstation_id
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('acl_id')->nullable()->after('id');

            $table->foreign('acl_id')
                  ->references('id')
                  ->on('acl')
                  ->onDelete('set null');
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
                tickets.workstation_id
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");
    }
};
