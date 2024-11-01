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
        DB::statement('DROP VIEW IF EXISTS ticket_view');

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('acl_id')->nullable()->after('id');

            $table->foreign('acl_id')
                  ->references('id')
                  ->on('acl')
                  ->onDelete('set null');
        });

        DB::statement("
            CREATE VIEW ticket_view AS
            SELECT 
                tickets.id,
                users.name AS user,
                destinations.name AS destination,
                statuses.name AS status,
                tickets.status_id,
                tickets.ticket_nr,
                workstations.name AS workstation
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
        DB::statement('DROP VIEW IF EXISTS ticket_view');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['acl_id']);
            $table->dropColumn('acl_id');
        });

        DB::statement("
            CREATE VIEW ticket_view AS
            SELECT 
                tickets.id,
                users.name AS user,
                destinations.name AS destination,
                statuses.name AS status,
                tickets.status_id,
                tickets.ticket_nr,
                workstations.name AS workstation
            FROM tickets
            LEFT JOIN users ON tickets.user_id = users.id
            LEFT JOIN destinations ON tickets.destination_id = destinations.id
            LEFT JOIN statuses ON tickets.status_id = statuses.id
            LEFT JOIN workstations ON tickets.workstation_id = workstations.id
        ");
    }
};
