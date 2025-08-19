<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RedesignEmployeesForPayroll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            Schema::table('employees', function (Blueprint $table) {
                // New normalized fields
                $table->string('first_name')->nullable()->after('name');
                $table->string('last_name')->nullable()->after('first_name');
                $table->string('id_number')->nullable()->after('phone');
                $table->date('birthdate')->nullable()->after('id_number');
                $table->date('start_date')->nullable()->after('birthdate'); // SimplePay appointment_date
                $table->string('pay_frequency')->default('monthly')->after('start_date'); // weekly|fortnightly|monthly
                $table->string('payment_method')->default('bank')->after('pay_frequency'); // bank|cash
                $table->string('status')->default('active')->after('payment_method'); // active|terminated

                // Helpful indexes
                $table->index('id_number');
                $table->index('status');
            });

            // Backfill best-effort: split name into first/last, copy nid->id_number, joining_date->start_date
            DB::statement("
            UPDATE employees
            SET first_name = COALESCE(NULLIF(SUBSTRING_INDEX(TRIM(name), ' ', 1), ''), first_name),
                last_name  = COALESCE(NULLIF(TRIM(SUBSTRING(TRIM(name), LENGTH(SUBSTRING_INDEX(TRIM(name), ' ', 1)) + 1)), ''), last_name),
                id_number  = COALESCE(NULLIF(nid, ''), id_number),
                start_date = COALESCE(start_date, joining_date)
        ");
            // If last_name ended up empty, mirror first_name to avoid nulls later
            DB::statement("UPDATE employees SET last_name = COALESCE(NULLIF(last_name,''), first_name) WHERE last_name IS NULL OR last_name = ''");
            // Backfill composite name from new fields
            DB::statement("UPDATE employees SET name = TRIM(CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,''))) WHERE name IS NULL OR name = ''");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropIndex(['id_number']);
                $table->dropIndex(['status']);
                $table->dropColumn([
                    'first_name',
                    'last_name',
                    'id_number',
                    'birthdate',
                    'start_date',
                    'pay_frequency',
                    'payment_method',
                    'status',
                    'simplepay_employee_id'
                ]);
            });
        });
    }
}
