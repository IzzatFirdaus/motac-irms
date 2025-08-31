<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'title',
            'identification_number',
            'passport_number',
            'motac_email',
            'personal_email',
            'phone_number',
            'status',
        ];

        foreach ($columns as $col) {
            if (! Schema::hasColumn('users', $col)) {
                Schema::table('users', function (Blueprint $table) use ($col): void {
                    // make them nullable to avoid breaking existing records
                    $table->string($col)->nullable()->after('email');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'title',
            'identification_number',
            'passport_number',
            'motac_email',
            'personal_email',
            'phone_number',
            'status',
        ];

        foreach ($columns as $col) {
            if (Schema::hasColumn('users', $col)) {
                Schema::table('users', function (Blueprint $table) use ($col): void {
                    $table->dropColumn($col);
                });
            }
        }
    }
}
