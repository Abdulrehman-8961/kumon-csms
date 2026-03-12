<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFatherFieldsToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'father_firstname')) {
                $table->string('father_firstname')->nullable()->after('portal_invited_at');
            }
            if (!Schema::hasColumn('clients', 'father_lastname')) {
                $table->string('father_lastname')->nullable()->after('father_firstname');
            }
            if (!Schema::hasColumn('clients', 'father_client_address')) {
                $table->text('father_client_address')->nullable()->after('father_lastname');
            }
            if (!Schema::hasColumn('clients', 'father_city')) {
                $table->string('father_city')->nullable()->after('father_client_address');
            }
            if (!Schema::hasColumn('clients', 'father_state')) {
                $table->string('father_state')->nullable()->after('father_city');
            }
            if (!Schema::hasColumn('clients', 'father_zip')) {
                $table->string('father_zip')->nullable()->after('father_state');
            }
            if (!Schema::hasColumn('clients', 'father_work_phone')) {
                $table->string('father_work_phone')->nullable()->after('father_zip');
            }
            if (!Schema::hasColumn('clients', 'father_email_address')) {
                $table->string('father_email_address')->nullable()->after('father_work_phone');
            }
            if (!Schema::hasColumn('clients', 'father_portal_access')) {
                $table->tinyInteger('father_portal_access')->default(0)->after('father_email_address');
            }
            if (!Schema::hasColumn('clients', 'father_portal_invited_at')) {
                $table->timestamp('father_portal_invited_at')->nullable()->after('father_portal_access');
            }
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $columns = [
                'father_firstname',
                'father_lastname',
                'father_client_address',
                'father_city',
                'father_state',
                'father_zip',
                'father_work_phone',
                'father_email_address',
                'father_portal_access',
                'father_portal_invited_at',
            ];

            $existing = array_filter($columns, function ($column) {
                return Schema::hasColumn('clients', $column);
            });

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
}
