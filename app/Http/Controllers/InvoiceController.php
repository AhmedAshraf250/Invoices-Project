<?php

namespace App\Http\Controllers;

use App\Exports\InvoiceExport;
use App\Http\Requests\PreviewInvoiceCalculationRequest;
use App\Http\Requests\StoreInvoiceAttachmentRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceStatusRequest;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Services\Invoices\InvoiceService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->renderIndexPage();
    }

    public function status(string $status): View
    {
        abort_unless(in_array($status, [Invoice::STATUS_PAID, Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL], true), 404);

        return $this->renderIndexPage(status: $status);
    }

    public function archived(): View
    {
        return $this->renderIndexPage(onlyTrashed: true);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $status = $request->string('status')->toString();
        $status = in_array($status, [Invoice::STATUS_PAID, Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL], true)
            ? $status
            : null;

        $onlyTrashed = $request->boolean('archived');

        $fileName = 'invoices_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new InvoiceExport($status, $onlyTrashed), $fileName);
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

    public function print(int $invoiceId): View
    {
        $invoice = $this->invoiceService->showWithTrashed($invoiceId);

        return view('invoices.print', compact('invoice'));
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

    public function downloadAttachment(Invoice $invoice, InvoiceAttachment $attachment): StreamedResponse|RedirectResponse
    {
        $fileData = $this->invoiceService->getAttachmentFileData($invoice, $attachment);

        if ($fileData === null) {
            return to_route('invoices.show', $invoice)
                ->with('error', __('invoices.messages.attachment_not_found'));
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($fileData['disk']);

        return $disk->download($fileData['file_path'], $fileData['original_name']);
    }

    public function viewAttachment(Invoice $invoice, InvoiceAttachment $attachment): StreamedResponse|RedirectResponse
    {
        $fileData = $this->invoiceService->getAttachmentFileData($invoice, $attachment);

        if ($fileData === null) {
            return to_route('invoices.show', $invoice)
                ->with('error', __('invoices.messages.attachment_not_found'));
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($fileData['disk']);

        return $disk->response($fileData['file_path'], $fileData['original_name']);
    }

    public function destroyAttachment(Invoice $invoice, InvoiceAttachment $attachment): RedirectResponse
    {
        $this->invoiceService->destroyAttachment($invoice, $attachment);

        return to_route('invoices.show', $invoice)
            ->with('success', __('invoices.messages.attachment_deleted'));
    }

    public function archive(Invoice $invoice): RedirectResponse
    {
        $this->invoiceService->archiveInvoice($invoice);

        return to_route('invoices.index')
            ->with('success', __('invoices.messages.archived'));
    }

    public function restore(int $invoiceId): RedirectResponse
    {
        $this->invoiceService->restoreInvoice($invoiceId);

        return to_route('invoices.archived')
            ->with('success', __('invoices.messages.restored'));
    }

    public function forceDelete(int $invoiceId): RedirectResponse
    {
        $this->invoiceService->forceDeleteInvoice($invoiceId);

        return to_route('invoices.archived')
            ->with('success', __('invoices.messages.force_deleted'));
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

    private function renderIndexPage(?string $status = null, bool $onlyTrashed = false): View
    {
        $invoices = $this->invoiceService->index(15, $status, $onlyTrashed);
        $summary = $this->invoiceService->summary($onlyTrashed);
        $view = $onlyTrashed ? 'invoices.archived' : 'invoices.index';

        $statusLabel = null;
        if ($status !== null) {
            $statusKey = 'invoices.status.' . $status;
            $statusLabel = Lang::has($statusKey) ? __($statusKey) : __('invoices.status.unknown');
        }

        return view($view, [
            'invoices' => $invoices,
            'summary' => $summary,
            'statusFilter' => $status,
            'statusFilterLabel' => $statusLabel,
        ]);
    }
}
