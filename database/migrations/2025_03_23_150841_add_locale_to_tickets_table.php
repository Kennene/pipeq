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
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('locale', 10)->default('pl')->after('reason_id');

            //remove unused column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });


        DB::statement("
                CREATE VIEW tickets_view AS
                SELECT 
                    tickets.id,
                    tickets.ticket_nr,
                    destinations.name AS destination,
                    tickets.destination_id,
                    statuses.name AS status,
                    colors.hex_code AS status_color,
                    tickets.status_id,
                    workstations.name AS workstation,
                    tickets.workstation_id,
                    tickets.created_at,
                    reasons.description AS reason,
                    tickets.locale AS locale
                FROM tickets
                LEFT JOIN destinations ON tickets.destination_id = destinations.id
                LEFT JOIN statuses ON tickets.status_id = statuses.id
                LEFT JOIN colors ON statuses.color_id = colors.id
                LEFT JOIN workstations ON tickets.workstation_id = workstations.id
                LEFT JOIN reasons ON tickets.reason_id = reasons.id
            ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS tickets_view');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('locale');
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete()->noActionOnUpdate();
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
    }
};
