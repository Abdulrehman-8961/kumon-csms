<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxReceiptNextNoToCenterSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('center_settings') && !Schema::hasColumn('center_settings', 'tax_receipt_next_no')) {
            Schema::table('center_settings', function (Blueprint $table) {
                $table->unsignedInteger('tax_receipt_next_no')->default(2000)->after('telephone');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('center_settings') && Schema::hasColumn('center_settings', 'tax_receipt_next_no')) {
            Schema::table('center_settings', function (Blueprint $table) {
                $table->dropColumn('tax_receipt_next_no');
            });
        }
    }
}

