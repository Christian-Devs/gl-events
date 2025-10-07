<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\QuoteMail;
use App\Model\Quote;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotes = Quote::with(['items', 'jobcard', 'invoice'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($quotes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $latestQuote = Quote::orderBy('id', 'desc')->first();
        $nextNumber = $latestQuote ? $latestQuote->id + 1 : 1;
        $quoteNumber = 'QT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'subtotal' => 'required|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $quote = Quote::create([
            'quote_number' => $quoteNumber,
            'quote_date' => now(),
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'subtotal' => $validated['subtotal'],
            'vat' => $validated['vat'] ?? 0,
            'total' => $validated['total'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $quote->items()->create($item);
        }

        return response()->json([
            'message' => 'Quote created successfully',
            'quote' => $quote->load('items'),
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
        $quote = Quote::with('items')->findOrFail($id);
        return response()->json($quote);
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
        $quote = Quote::findOrFail($id);

        // If only updating status
        if ($request->has('status') && count($request->all()) === 1) {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected'
            ]);

            $quote->status = $request->status;
            $quote->save();

            return response()->json(['message' => 'Status updated']);
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'subtotal' => 'required|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $quote->update($validated);

        // Delete old items and re-create
        $quote->items()->delete();

        foreach ($validated['items'] as $item) {
            $quote->items()->create($item);
        }

        return response()->json([
            'message' => 'Quote updated',
            'quote' => $quote->load('items'),
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
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json(['message' => 'Quote deleted']);
    }

    public function sendQuoteEmail(Request $request, $id)
    {
        $quote = Quote::with('items')->find($id);

        $pdf = Pdf::loadView('pdfs.quote', compact('quote'))->setPaper('a4');
        $pdfContent = $pdf->output();

        Mail::to($quote->client_email)->send(new QuoteMail($quote, $pdfContent));

        return response()->json(['message' => 'Invoice emailed successfully']);
    }
}
