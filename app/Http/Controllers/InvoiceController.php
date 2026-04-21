<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewInvoiceCalculationRequest;
use App\Http\Requests\StoreInvoiceAttachmentRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Services\Invoices\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $invoices = $this->invoiceService->index(15);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $organizations = $this->invoiceService->organizationsForCreate();

        return view('invoices.add', compact('organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $invoice = $this->invoiceService->createInvoice(
            validated: $validated,
            attachment: $request->file('attachment'),
            userId: $request->user()?->id,
        );

        return to_route('invoices.show', $invoice)
            ->with('success', __('invoices.messages.created'));
    }

    public function previewCalculation(PreviewInvoiceCalculationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return response()->json($this->invoiceService->previewCalculation($validated));
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): View
    {
        $invoice = $this->invoiceService->show($invoice);

        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(UpdateInvoiceStatusRequest $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validated();

        $this->invoiceService->updateStatus($invoice, $validated, $request->user()?->id);

        return to_route('invoices.show', $invoice)
            ->with('success', __('invoices.messages.status_updated'));
    }

    public function storeAttachment(StoreInvoiceAttachmentRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->storeAttachment($invoice, $request->file('attachment'), $request->user()?->id);

        return to_route('invoices.show', $invoice)
            ->with('success', __('invoices.messages.attachment_uploaded'));
    }

    public function downloadAttachment(Invoice $invoice, InvoiceAttachment $attachment)
    {
        return $this->invoiceService->downloadAttachment($invoice, $attachment);
    }

    public function destroyAttachment(Invoice $invoice, InvoiceAttachment $attachment): RedirectResponse
    {
        $this->invoiceService->destroyAttachment($invoice, $attachment);

        return to_route('invoices.show', $invoice)
            ->with('success', __('invoices.messages.attachment_deleted'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->deleteInvoice($invoice);

        return to_route('invoices.index')
            ->with('success', __('invoices.messages.deleted'));
    }
}
