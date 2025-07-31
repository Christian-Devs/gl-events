<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Model\Payment;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(
            Invoice::with('items', 'quote')->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $latestInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextNumber = $latestInvoice ? $latestInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $validated = $request->validate([
            'quote_id' => 'required|unique:invoices,quote_id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        // Set invoice_number manually
        $invoice = Invoice::create([
            'quote_id' => $validated['quote_id'],
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $validated['subtotal'],
            'vat' => $validated['vat'],
            'total' => $validated['total'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice->load('items')
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(
            Invoice::with(['items', 'quote', 'payments'])->findOrFail($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $invoice->update($validated);

        // Optional: clear + reinsert items
        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            $invoice->items()->create($item);
        }

        return response()->json([
            'message' => 'Invoice updated',
            'invoice' => $invoice->load('items', 'quote'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with('items', 'quote')->findOrFail($id);
        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'))->setPaper('a4');

        return $pdf->download("Invoice_{$invoice->invoice_number}.pdf");
    }

    public function sendInvoiceEmail($id)
    {
        $invoice = Invoice::with('items', 'quote')->findOrFail($id);

        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'))->setPaper('a4');
        $pdfContent = $pdf->output();

        Mail::to($invoice->quote->client_email)->send(new InvoiceMail($invoice, $pdfContent));

        return response()->json(['message' => 'Invoice emailed successfully']);
    }

    public function generatePayment($id)
    {
        $invoice = Invoice::with('quote')->findOrFail($id);

        // Check if a payment exists directly by invoice_id
        if ($invoice->payments()->exists()) {
            return response()->json(['message' => 'Payment already exists for this invoice'], 409);
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,  // ✅ use the correct foreign key
            'amount' => $invoice->total,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'EFT',
            'status' => 'paid',
            'notes' => 'Auto-generated payment for Invoice #' . $invoice->invoice_number,
        ]);

        return response()->json([
            'message' => 'Payment generated successfully',
            'payment' => $payment
        ], 201);
    }


}
