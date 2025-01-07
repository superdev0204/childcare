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
        DB::table('users')->where('login', '0000-00-00 00:00:00')->update(['login' => NULL]);
        DB::table('users')->where('created', '0000-00-00 00:00:00')->update(['created' => '2000-03-08 12:00:00']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('password', 255)->after('pwd');
        });

        // Get all users
        $users = DB::table('users')->get();

        // Update each user's password with bcrypt
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update(['password' => $user->pwd]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pwd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->string('pwd')->after('email');
        });
    }
};
