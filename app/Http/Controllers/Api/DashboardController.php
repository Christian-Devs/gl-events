<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Model\Quote;
use App\Model\Invoice;
use App\Services\SimplePayClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function summary(SimplePayClient $sp)
    {
        $now = Carbon::now();

        /* ---------------- Employees ---------------- */
        $total       = Employee::count();
        $active      = Employee::where('status', 'active')->count();
        $linked      = Employee::whereNotNull('simplepay_employee_id')->count();
        $notLinked   = max(0, $total - $linked);

        $recent = Employee::select('id', 'first_name', 'last_name', 'email', 'start_date', 'status', 'simplepay_employee_id')
            ->orderByDesc('id')->limit(5)->get();

        $perMonth = Employee::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-01') as month_start"),
            DB::raw('COUNT(*) as cnt')
        )
            ->where('created_at', '>=', $now->copy()->subMonths(5)->startOfMonth())
            ->groupBy('month_start')
            ->orderBy('month_start')
            ->get();

        /* ---------------- SimplePay (best-effort) ---------------- */
        $payroll = ['ok' => false];
        try {
            $clientId = (int) $sp->getPrimaryClientId();
            $runId    = $sp->getLatestRunId($clientId);
            if ($runId) {
                $slips = $sp->listPayslipsInRun($runId);
                $payroll = [
                    'ok'            => true,
                    'client_id'     => $clientId,
                    'latest_run_id' => $runId,
                    'payslip_count' => is_array($slips) ? count($slips) : 0,
                ];
            }
        } catch (\Throwable $e) {
            $payroll = ['ok' => false, 'error' => $e->getMessage()];
        }

        /* ---------------- Quotes ---------------- */
        // 🔧 If your schema differs:
        //  - status column might be 'status' | 'state'
        //  - pending approval might be 'pending_approval' | 'awaiting_approval' | approved=false
        $quotesTotal   = Quote::count();

        // Option A: status value
        $quotesPending = Quote::where('status', 'pending_approval')->count();

        // Option B (fallback): approved flag
        if ($quotesPending === 0 && Schema::hasColumn((new Quote)->getTable(), 'approved')) {
            $quotesPending = Quote::where('approved', false)->count();
        }

        $quotesPendingList = Quote::select('id', 'quote_number', 'client_name', 'total', 'created_at', 'status')
            ->where('status', 'pending_approval')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /* ---------------- Invoices ---------------- */
        // 🔧 If your schema differs:
        //  - unpaid statuses might be: 'sent','unpaid','overdue','partially_paid'
        //  - amount due field might be 'amount_due' or compute (total - paid_amount)
        $unpaidStatuses = ['sent', 'unpaid', 'overdue', 'partially_paid'];

        $invoicesCreatedThisMonth = Invoice::whereBetween('created_at', [
            $now->copy()->startOfMonth(),
            $now
        ])->count();

        $invoicesUnpaidQuery = Invoice::whereIn('status', $unpaidStatuses);

        // Try to sum amount_due; fallback to total - paid_amount if amount_due missing
        $outstandingField = 'amount_due';
        $hasAmountDue = Schema::hasColumn((new Invoice)->getTable(), $outstandingField);

        $invoicesUnpaidCount = (clone $invoicesUnpaidQuery)->count();
        $invoicesOutstanding = $hasAmountDue
            ? (clone $invoicesUnpaidQuery)->sum($outstandingField)
            : (clone $invoicesUnpaidQuery)->sum('total');

        $invoicesUnpaidList = Invoice::select('id', 'invoice_number', 'quote_id', 'due_date', 'status')
            ->when($hasAmountDue, fn($q) => $q->addSelect('amount_due'))
            ->when(!$hasAmountDue, fn($q) => $q->addSelect('total'))
            ->whereIn('status', $unpaidStatuses)
            ->orderBy('due_date')      // earliest due first
            ->limit(5)
            ->get();

        return response()->json([
            'totals'    => [
                'employees'  => $total,
                'active'     => $active,
                'active_pct' => $total ? round($active / $total * 100, 1) : 0,
                'linked'     => $linked,
                'not_linked' => $notLinked,
            ],
            'recent'    => $recent,
            'per_month' => $perMonth,
            'payroll'   => $payroll,

            'sales'     => [
                'quotes' => [
                    'total'   => $quotesTotal,
                    'pending' => $quotesPending,
                    'pending_list' => $quotesPendingList,
                ],
                'invoices' => [
                    'created_this_month' => $invoicesCreatedThisMonth,
                    'unpaid_count'       => $invoicesUnpaidCount,
                    'outstanding_total'  => round((float) $invoicesOutstanding, 2),
                    'unpaid_list'        => $invoicesUnpaidList,
                ],
            ],
        ]);
    }
}
