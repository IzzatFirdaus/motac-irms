<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
                Schema::table('users', function (Blueprint $table) use ($col) {
                    // make them nullable to avoid breaking existing records
                    $table->string($col)->nullable()->after('email');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
                Schema::table('users', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
}
