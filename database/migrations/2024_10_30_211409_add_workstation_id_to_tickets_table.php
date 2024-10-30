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
        Schema::table('tickets', function (Blueprint $table) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->foreignId('workstation_id')
                      ->nullable()
                      ->default(null)
                      ->constrained('workstations')
                      ->nullOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['workstation_id']);
                $table->dropColumn('workstation_id');
            });
        });
    }
};
