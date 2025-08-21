<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 0) Drop child FKs that reference employees.id
         *    Repeat this block for every child table that has employee_id -> employees.id
         */
        if (Schema::hasTable('salaries') && Schema::hasColumn('salaries', 'employee_id')) {
            Schema::table('salaries', function (Blueprint $table) {
                // Drops FK even if the name isn't the default (array form handles it)
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Throwable $e) {
                }
            });
        }

        /**
         * 1) Drop employees
         */
        Schema::dropIfExists('employees');

        /**
         * 2) Recreate employees with SimplePay‑aligned structure
         */
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identity
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Payroll identifiers
            $table->string('id_number');   // SA ID or passport
            $table->date('birthdate');     // SimplePay: birthdate
            $table->date('start_date');    // SimplePay: appointment_date

            // Payroll settings
            $table->string('pay_frequency')->default('monthly');  // monthly|fortnightly|weekly (SimplePay: wave)
            $table->string('payment_method')->default('bank');    // bank|cash
            $table->string('status')->default('active');          // active|terminated

            // Integrations
            $table->unsignedBigInteger('simplepay_employee_id')->nullable()->unique();
            $table->string('external_reference')->nullable();

            // Keep required links
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_number');
            $table->index('status');
            $table->index('pay_frequency');

            // FKs to roles/users (adjust table names if yours differ)
            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('set null');
        });

        /**
         * 3) Recreate child FKs back to employees.id
         */
        if (Schema::hasTable('salaries') && Schema::hasColumn('salaries', 'employee_id')) {
            Schema::table('salaries', function (Blueprint $table) {
                $table->foreign('employee_id')
                    ->references('id')->on('employees')
                    ->onUpdate('cascade')->onDelete('cascade'); // choose behavior you prefer
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rollback strategy: drop child FKs, drop employees, (optionally) recreate a minimal previous version.

        if (Schema::hasTable('salaries') && Schema::hasColumn('salaries', 'employee_id')) {
            Schema::table('salaries', function (Blueprint $table) {
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Throwable $e) {
                }
            });
        }

        Schema::dropIfExists('employees');

        // (Optional) Recreate an old minimal employees table to satisfy down() if you need strict rollbacks.
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('salaries') && Schema::hasColumn('salaries', 'employee_id')) {
            Schema::table('salaries', function (Blueprint $table) {
                $table->foreign('employee_id')->references('id')->on('employees');
            });
        }
    }
}
