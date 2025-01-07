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
        Schema::table('error', function (Blueprint $table) {
            // Optionally, you can add a temporary column to handle conversion
            $table->string('user_agent_temp')->charset('utf8mb4')->nullable()->after('user_agent');
        });

        // Copy data to the temporary column
        DB::table('error')->update(['user_agent_temp' => DB::raw('user_agent')]);

        Schema::table('error', function (Blueprint $table) {
            // Drop the old column
            $table->dropColumn('user_agent');
        });

        // Step 4: Rename the temporary column using raw SQL
        DB::statement('ALTER TABLE error CHANGE user_agent_temp user_agent VARCHAR(255) CHARACTER SET utf8mb4');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the operations if needed
        Schema::table('error', function (Blueprint $table) {
            $table->string('user_agent')->nullable()->after('ip'); // Adjust position as necessary
            $table->renameColumn('user_agent', 'user_agent_temp');
        });
    }
};
