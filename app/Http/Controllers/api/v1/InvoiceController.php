<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Invoice as Invoice;
use App\Http\Resources\api\v1\InvoiceResource;

class InvoiceController extends Controller
{
    /**
     * Get all invoices list.
     *
     * @urlParam filter_date_from string From date (Y-m-d format)
     * @urlParam filter_date_to string To date (Y-m-d format)
     * @urlParam filter_min_amount float Minimum amount
     * @urlParam filter_max_amount float Maximum amount
     * @urlParam filter_status enum('paid', 'failed') Invoice status
     * @urlParam filter_type enum('normal', 'auto') Invoice type
     * @urlParam filter_uploaded boolean Invoice uploaded flag
     * @urlParam filter_transaction boolean Invoice transaction flag
     *
     * @response {
     *  "data": [invoices],
     * }
     */
    public function index(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_min_amount' => 'numeric|min:0',
            'filter_max_amount' => 'numeric|min:0',
            'filter_status' => 'in:paid,failed',
            'filter_type' => 'in:normal,auto',
            'filter_uploaded' => 'boolean',
            'filter_transaction' => 'boolean',
        ]);

        // Query invoices (applying filters)
        $invoices = Invoice::query();
        $invoices = $invoices->where('archived', 0);
        if (!Auth::user()->can('viewAny', 'App\Invoice')) {
            $invoices = $invoices->where('user_id', Auth::user()->id);
        }
        if (isset($params['filter_date_from'])) {
            $invoices = $invoices->where('invoice_date', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $invoices = $invoices->where('invoice_date', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_min_amount'])) {
            $invoices = $invoices->where('amount', '>=', $params['filter_min_amount']);
        }
        if (isset($params['filter_max_amount'])) {
            $invoices = $invoices->where('amount', '<=', $params['filter_max_amount']);
        }
        if (isset($params['filter_status'])) {
            $invoices = $invoices->where('status', $params['filter_status']);
        }
        if (isset($params['filter_type'])) {
            $invoices = $invoices->where('type', $param['filter_type']);
        }
        if (isset($params['filter_uploaded'])) {
            $invoices = $invoices->where('uploaded', $params['filter_uploaded']);
        }
        if (isset($params['filter_transaction'])) {
            $invoices = $invoices->where('transaction', $params['filter_transaction']);
        }

        return InvoiceResource::collection($invoices->get());
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
            "data" => InvoiceResource::collection($invoices),
        ], 201);
    }

    /**
     * Update invoice.
     *
     * @urlParam id int required Invoice's id
     *
     * @bodyParam type enum('normal', 'auto') Invoice type
     *
     * @response {
     *  "data": invoice,
     * }
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->authorize('update', $invoice);

        // Params validation
        $params = $request->validate([
            'type' => 'in:normal,auto',
        ]);

        $invoice->update($params);

        return new InvoiceResource($invoice);
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
     *  "data": file_content,
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

    /**
     * Download the specified invoices in a zip archive.
     *
     * @bodyParam ids array required Invoices' ids
     *
     * @response {
     *  "data": file_content,
     * }
     */
    public function downloadZip(Request $request)
    {
        // Params validation
        $params = $request->validate([
            'ids' => 'required',
            'ids.*' => 'integer|exists:invoices,id',
        ]);

        // Initializing zip archive
		$zipFile = storage_path('app/invoices.zip');
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return response([
                "message" => "Unexpected error while creating zip archive"
            ], 500);
        }

        $canDownload = true;
        foreach ($params['ids'] as $id) {
            $invoice = Invoice::findOrFail($id);

            if (!Auth::user()->can('view', $invoice)) {
                $canDownload = false;
                break;
            }

            if (!Storage::disk('dropbox')->exists($invoice->pathname)) {
                continue;
            }

            $contents = Storage::disk('dropbox')->get($invoice->pathname);
            if ($zip->addFromString($invoice->original_file_name, $contents) !== TRUE) {
                $zip->close();
                return response([
                    "message" => "Unexpected error while adding file to zip archive"
                ], 500);
            }
        }

        $zip->close();
        return response()->download($zipFile);
    }

    /**
     * Download invoices summary
     *
     * @bodyParam filter_date_from string From date (Y-m-d format)
     * @bodyParam filter_date_to string To date (Y-m-d format)
     * @bodyParam filter_min_amount float Minimum amount
     * @bodyParam filter_max_amount float Maximum amount
     * @bodyParam filter_status enum('paid', 'failed') Invoice status
     * @bodyParam filter_type enum('normal', 'auto') Invoice type
     * @bodyParam filter_uploaded boolean Invoice uploaded flag
     * @bodyParam filter_transaction boolean Invoice transaction flag
     *
     * @response {
     *  "data": file_content,
     * }
     */
    public function downloadSummary(Request $request)
    {
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' => 'nullable|date|date_format:Y-m-d',
            'filter_min_amount' => 'numeric|min:0',
            'filter_max_amount' => 'numeric|min:0',
            'filter_status' => 'in:paid,failed',
            'filter_type' => 'in:normal,auto',
            'filter_uploaded' => 'boolean',
            'filter_transaction' => 'boolean',
        ]);

        // Query invoices (applying filters)
        $invoices = Invoice::query();
        $invoices = $invoices->where('archived', 0);
        if (isset($params['filter_date_from'])) {
            $invoices = $invoices->where('invoice_date', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $invoices = $invoices->where('invoice_date', '<=', $params['filter_date_to']);
        }
        if (isset($params['filter_min_amount'])) {
            $invoices = $invoices->where('amount', '>=', $params['filter_min_amount']);
        }
        if (isset($params['filter_max_amount'])) {
            $invoices = $invoices->where('amount', '<=', $params['filter_max_amount']);
        }
        if (isset($params['filter_status'])) {
            $invoices = $invoices->where('status', $params['filter_status']);
        }
        if (isset($params['filter_type'])) {
            $invoices = $invoices->where('type', $params['filter_type']);
        }
        if (isset($params['filter_uploaded'])) {
            $invoices = $invoices->where('uploaded', $params['filter_uploaded']);
        }
        if (isset($params['filter_transaction'])) {
            $invoices = $invoices->where('transaction', $params['filter_transaction']);
        }

        $fileName = storage_path('app/summary.csv');
        $handle = fopen($fileName, "w");
        $header = array('data', 'numero fattaura', 'importo', 'metodo pagamento');
        fputcsv($handle, $header);
        foreach ($invoices->get() as $invoice) {
            $method = ($invoice->status == 'paid') ? 'SOLDO' : '';
            $data = array(
                $invoice->invoice_date,
                $invoice->number,
                $invoice->amount,
                $method,
            );
            fputcsv($handle, $data);
        }
        fclose($handle);

        return Storage::download('summary.csv');
    }
}
