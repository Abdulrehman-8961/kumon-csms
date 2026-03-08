<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdToClientStudentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('client_students', 'student_id')) {
            Schema::table('client_students', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('client_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('client_students', 'student_id')) {
            Schema::table('client_students', function (Blueprint $table) {
                $table->dropColumn('student_id');
            });
        }
    }
}
