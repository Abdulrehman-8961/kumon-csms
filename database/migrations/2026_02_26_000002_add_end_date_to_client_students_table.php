<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateToClientStudentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('client_students', 'end_date')) {
            Schema::table('client_students', function (Blueprint $table) {
                $table->date('end_date')->nullable()->after('start_date');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('client_students', 'end_date')) {
            Schema::table('client_students', function (Blueprint $table) {
                $table->dropColumn('end_date');
            });
        }
    }
}
