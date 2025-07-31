<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentsTableForInvoiceAndJobcard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop employee_id foreign key and column
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');

            // Add jobcard_id and invoice_id
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('jobcard_id')->nullable()->constrained()->onDelete('set null');

            // Add optional metadata if not already present
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (!Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable();
            }
            $table->string('status')->default('pending')->change();
            // You can keep or rename 'status' if useful (e.g. 'pending', 'paid', 'failed')
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['jobcard_id']);
            $table->dropColumn(['invoice_id', 'jobcard_id']);

            // Re-add employee_id if needed (not required in your current plan)
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
}
