<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Invoice as Invoice;
use App\Http\Resources\api\v1\InvoiceResource;

class InvoiceController extends Controller
{
    /**
     * Get all invoices list.
     *
     * @response {
     *  "data": [invoices],
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', 'App\Invoice');

        return InvoiceResource::collection(Invoice::where('archived', 0)->get());
    }

    /**
     * Get invoice's informations.
     *
     * @urlParam id int required Invoice's id
     *
     * @response {
     *  "data": invoice,
     * }
     */
    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('view', $invoice);

        if ($invoice->archived) {
            return response([
                "message" => "That invoice is archived"
            ], 403);
        }

        return new InvoiceResource($invoice);
    }

    /**
     * Create a new invoice.
     *
     * @bodyParam files[] file required Invoice(s) PDF(s)
     *
     * @response 201 {
     *  "message": "Invoice created",
     *  "data": [invoices],
     * }
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Invoice');

        // Params validation
        $params = $request->validate([
            'files' => 'required',
            'files.*' => 'mimes:pdf',
        ]);

        if (!$request->hasFile('files')) {
            return response([
                'errors' => 'Files not uploaded'
            ], 400);
        }

        $invoices = [];
        $files = $request->file('files');
        foreach ($files as $file) {
            array_push($invoices, Invoice::createFromFile($file));
        }
        return response([
            "data" => $invoices,
        ], 201);

        return response([
            "message" => "Invoice(s) created",
            "data" => InvoiceResource::collection($invoices),
        ], 201);
    }

    /**
     * Delete the specified invoice.
     *
     * @urlParam id int required Invoice's id
     *
     * @response {
     *  "message": "Invoice has been archived",
     * }
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        $this->authorize('delete', $invoice);

        // Archive the invoice
        $invoice->archived = 1;
        $invoice->save();

        return response([
            'message' => 'Invoice has been archived'
        ]);
    }

    /**
     * Download the specified invoice.
     *
     * @urlParam id int required Invoice's id
     *
     * @response {
     *  "message": "Invoice has been archived",
     * }
     */
    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('view', $invoice);

        // Check if file exist
		if(!Storage::disk('dropbox')->exists($invoice->pathname)) {
            return response([
                'error' => 'File not found in storage!',
            ], 404);
        }

        $headers = [
            'Content-Type' => 'application/pdf'
        ];
        return Storage::disk('dropbox')->download($invoice->pathname, $invoice->original_file_name, $headers);
    }
}
