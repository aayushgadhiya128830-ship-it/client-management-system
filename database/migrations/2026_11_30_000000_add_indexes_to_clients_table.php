<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data-safe migration: only adds indexes, no column or record is changed.
 * - created_at : used by the list ordering (latest first)
 * - name       : used when sorting / prefix lookups by name
 * Note: LIKE '%text%' searches cannot use a normal index; with the expected
 * data size (hundreds or a few thousand rows) this is acceptable.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->index('created_at', 'clients_created_at_index');
            $table->index('name', 'clients_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_created_at_index');
            $table->dropIndex('clients_name_index');
        });
    }
};
