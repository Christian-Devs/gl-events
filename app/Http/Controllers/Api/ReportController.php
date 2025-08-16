<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericReportMail;

class ReportController extends Controller
{

    //quote report
    public function quotesReport(Request $request)
    {
        $quotes = \App\Model\Quote::selectRaw('
            COUNT(*) as total_quotes,
            SUM(total) as total_value,
            COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
            COUNT(CASE WHEN status = "approved" THEN 1 END) as approved,
            COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected
        ')
            ->first();

        $monthly = \App\Model\Quote::selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as count,
            SUM(total) as total
        ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $quotes,
            'monthly' => $monthly
        ]);
    }


    //invoice report
    public function invoicesReport(Request $request)
    {
        $summary = \App\Model\Invoice::selectRaw('
        COUNT(*) as total_invoices,
        SUM(total) as total_amount,
        COUNT(CASE WHEN status = "draft" THEN 1 END) as draft,
        COUNT(CASE WHEN status = "sent" THEN 1 END) as sent,
        COUNT(CASE WHEN status = "paid" THEN 1 END) as paid,
        COUNT(CASE WHEN status = "overdue" THEN 1 END) as overdue
    ')->first();

        $monthly = \App\Model\Invoice::selectRaw('
        DATE_FORMAT(invoice_date, "%Y-%m") as month,
        COUNT(*) as count,
        SUM(total) as total
    ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $summary,
            'monthly' => $monthly
        ]);
    }


    //Payment Report
    public function paymentsReport(Request $request)
    {
        $summary = \App\Model\Payment::selectRaw('
        COUNT(*) as total_payments,
        SUM(amount) as total_amount,
        COUNT(CASE WHEN status = "paid" THEN 1 END) as paid,
        COUNT(CASE WHEN status = "pending" THEN 1 END) as pending
    ')->first();

        $monthly = \App\Model\Payment::selectRaw('
        DATE_FORMAT(payment_date, "%Y-%m") as month,
        COUNT(*) as count,
        SUM(amount) as total
    ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $summary,
            'monthly' => $monthly
        ]);
    }


    //Jobcard report
    public function jobcardsReport(Request $request)
    {
        $summary = \App\Model\Jobcard::selectRaw('
        COUNT(*) as total_jobcards,
        COUNT(CASE WHEN status = "open" THEN 1 END) as open,
        COUNT(CASE WHEN status = "in_progress" THEN 1 END) as in_progress,
        COUNT(CASE WHEN status = "completed" THEN 1 END) as completed
    ')->first();

        $monthly = \App\Model\Jobcard::selectRaw('
        DATE_FORMAT(created_at, "%Y-%m") as month,
        COUNT(*) as count
    ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $summary,
            'monthly' => $monthly
        ]);
    }


    //salary report
    public function salariesReport(Request $request)
    {
        $summary = \App\Model\Salary::selectRaw('
        COUNT(*) as total_records,
        SUM(CASE WHEN status = "paid" THEN net_salary ELSE 0 END) as total_paid,
        SUM(CASE WHEN status = "pending" THEN net_salary ELSE 0 END) as total_pending
    ')->first();

        $monthly = \App\Model\Salary::selectRaw('
        DATE_FORMAT(payment_date, "%Y-%m") as month,
        COUNT(*) as count,
        SUM(net_salary) as total
    ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return response()->json([
            'summary' => $summary,
            'monthly' => $monthly
        ]);
    }


    //generate report for email/download
    private function generateReportData($type)
    {
        switch ($type) {
            case 'quotes':
                $title = 'Quotes';
                $summary = \App\Model\Quote::selectRaw('
                COUNT(*) as total_quotes,
                SUM(total) as total_value,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
                COUNT(CASE WHEN status = "approved" THEN 1 END) as approved,
                COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected
            ')->first();

                $monthly = \App\Model\Quote::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(total) as total
            ')->groupBy('month')->orderBy('month', 'desc')->limit(12)->get();

                break;

            case 'invoices':
                $title = 'Invoices';
                $summary = \App\Model\Invoice::selectRaw('
                COUNT(*) as total_invoices,
                SUM(total) as total_value,
                COUNT(CASE WHEN status = "paid" THEN 1 END) as paid,
                COUNT(CASE WHEN status = "overdue" THEN 1 END) as overdue,
                COUNT(CASE WHEN status = "draft" THEN 1 END) as draft
            ')->first();

                $monthly = \App\Model\Invoice::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(total) as total
            ')->groupBy('month')->orderBy('month', 'desc')->limit(12)->get();

                break;

            case 'payments':
                $title = 'Payments';
                $summary = \App\Model\Payment::selectRaw('
                COUNT(*) as total_payments,
                SUM(amount) as total_paid,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
                COUNT(CASE WHEN status = "paid" THEN 1 END) as paid
            ')->first();

                $monthly = \App\Model\Payment::selectRaw('
                DATE_FORMAT(payment_date, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(amount) as total
            ')->groupBy('month')->orderBy('month', 'desc')->limit(12)->get();

                break;

            case 'jobcards':
                $title = 'Jobcards';
                $summary = \App\Model\Jobcard::selectRaw('
                COUNT(*) as total_jobcards,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
                COUNT(CASE WHEN status = "in_progress" THEN 1 END) as in_progress,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed
            ')->first();

                $monthly = \App\Model\Jobcard::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as count
            ')->groupBy('month')->orderBy('month', 'desc')->limit(12)->get();

                break;

            case 'salaries':
                $title = 'Salaries';
                $summary = \App\Model\Salary::selectRaw('
                COUNT(*) as total_salaries,
                SUM(net_salary) as total_paid,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
                COUNT(CASE WHEN status = "paid" THEN 1 END) as paid
            ')->first();

                $monthly = \App\Model\Salary::selectRaw('
                DATE_FORMAT(payment_date, "%Y-%m") as month,
                COUNT(*) as count,
                SUM(net_salary) as total
            ')->groupBy('month')->orderBy('month', 'desc')->limit(12)->get();

                break;

            default:
                abort(404, 'Invalid report type.');
        }

        return compact('summary', 'monthly', 'title');
    }


    //download pdf report
    public function downloadReportPdf($type)
    {
        $data = $this->generateReportData($type);
        if (!$data)
            return response()->json(['message' => 'Invalid report type'], 404);

        $pdf = Pdf::loadView('pdfs.report', $data)->setPaper('a4');
        return $pdf->download($data['title'] . '_Report.pdf');
    }

    //email report
    public function emailReport(Request $request, $type)
    {
        $data = $this->generateReportData($type);

        if (!$data)
            return response()->json(['message' => 'Invalid report type'], 404);

        $pdf = Pdf::loadView('pdfs.report', $data)->setPaper('a4');
        $pdfContent = $pdf->output();

        Mail::to($request->email)->send(new GenericReportMail($data['title'], $pdfContent));

        return response()->json(['message' => 'Report emailed successfully']);
    }

}
