<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBankFieldsToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $t) {
            // normalize payment_method to SimplePay tokens
            if (!Schema::hasColumn('employees', 'payment_method')) {
                $t->string('payment_method', 20)->nullable(); // 'cash'|'cheque'|'eft_manual'
            } else {
                $t->string('payment_method', 20)->nullable()->change();
            }

            // bank fields (required when payment_method=eft_manual)
            $t->unsignedInteger('bank_id')->nullable();
            $t->string('bank_account_type', 2)->nullable();       // "1","2","3","4","6"
            $t->string('bank_account_number', 64)->nullable();
            $t->string('bank_branch_code', 16)->nullable();
            $t->string('bank_holder_relationship', 2)->nullable(); // "1","2","3"
            $t->string('bank_holder_name', 255)->nullable();
        });

        // Backfill old values: 'bank' -> 'eft_manual'
        DB::table('employees')
            ->where('payment_method', 'bank')
            ->update(['payment_method' => 'eft_manual']);

        // If no banking info, fall back to cash so sync won’t fail
        DB::table('employees')
            ->where('payment_method', 'eft_manual')
            ->whereNull('bank_account_number')
            ->orWhereNull('bank_branch_code')
            ->update(['payment_method' => 'cash']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $t) {
            $t->dropColumn([
                'bank_id',
                'bank_account_type',
                'bank_account_number',
                'bank_branch_code',
                'bank_holder_relationship',
                'bank_holder_name',
            ]);
            // (optional) can’t easily revert content change of payment_method
        });
    }
}
