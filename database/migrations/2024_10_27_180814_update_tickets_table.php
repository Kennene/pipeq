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
            $table->dropColumn(['destination', 'status']);
            $table->foreignId('destination_id')->default(1)->constrained('destinations');
            $table->foreignId('status_id')->default(1)->constrained('statuses');
            $table->foreign('modified_by')->references('id')->on('users')->onDelete('set null')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('destination')->nullable();
            $table->string('status')->nullable();
            $table->dropForeign(['destination_id']);
            $table->dropForeign(['status_id']);
            $table->dropColumn(['destination_id', 'status_id']);
        });
    }
};
