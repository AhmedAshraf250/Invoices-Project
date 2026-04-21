@extends('layouts.master')

@section('title')
    {{ __('invoices.page.details_title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .status-update-focus {
            border: 1px solid rgba(0, 123, 255, 0.35);
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.06) 0%, rgba(0, 123, 255, 0.01) 100%);
            box-shadow: 0 10px 25px -18px rgba(0, 123, 255, 0.8);
        }

        .status-update-focus .card-header {
            background-color: rgba(0, 123, 255, 0.09);
            border-bottom: 1px dashed rgba(0, 123, 255, 0.35);
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('invoices.page.title') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $invoice->invoice_number }}</span>
            </div>
        </div>
        <div>
            {{-- Quick back button to the invoices list page --}}
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">{{ __('invoices.page.details_title') }}</a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Success alert for page actions (status updates, attachments upload/delete) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Error alert for general backend action failures --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Validation errors block for failed status/attachment forms --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="example">
                        <div class="panel panel-primary tabs-style-2">
                            {{-- Main tab navigation: invoice info, status history, and attachments --}}
                            <div class="tab-menu-heading">
                                <div class="tabs-menu1">
                                    <ul class="nav panel-tabs main-nav-line">
                                        <li><a href="#invoice-info" class="nav-link active"
                                                data-toggle="tab">{{ __('invoices.details.tab_info') }}</a></li>
                                        <li><a href="#invoice-statuses" class="nav-link"
                                                data-toggle="tab">{{ __('invoices.details.tab_statuses') }}</a></li>
                                        <li><a href="#invoice-attachments" class="nav-link"
                                                data-toggle="tab">{{ __('invoices.details.tab_attachments') }}</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="panel-body tabs-menu-body main-content-body-right border">
                                <div class="tab-content">
                                    {{-- Invoice info tab: financial summary, meta details, and status update form --}}
                                    <div class="tab-pane active" id="invoice-info">
                                        @php
                                            $currentStatusKey = 'invoices.status.' . $invoice->status;
                                            $currentStatusLabel = \Illuminate\Support\Facades\Lang::has(
                                                $currentStatusKey,
                                            )
                                                ? __($currentStatusKey)
                                                : __('invoices.status.unknown');
                                            $remainingAmount = max(
                                                (float) $invoice->total - (float) $invoice->paid_amount,
                                                0,
                                            );
                                        @endphp

                                        {{-- Quick KPI cards for current financial state of the invoice --}}
                                        <div class="row row-sm">
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card bg-primary-transparent mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="tx-12 text-muted mb-1">
                                                            {{ __('invoices.table.status') }}</div>
                                                        <div class="tx-16 font-weight-bold">{{ $currentStatusLabel }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card bg-success-transparent mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="tx-12 text-muted mb-1">{{ __('invoices.form.total') }}
                                                        </div>
                                                        <div class="tx-16 font-weight-bold">
                                                            {{ number_format((float) $invoice->total, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card bg-warning-transparent mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="tx-12 text-muted mb-1">
                                                            {{ __('invoices.details.paid_amount') }}</div>
                                                        <div class="tx-16 font-weight-bold">
                                                            {{ number_format((float) $invoice->paid_amount, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card bg-danger-transparent mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="tx-12 text-muted mb-1">
                                                            {{ __('invoices.details.remaining_amount') }}</div>
                                                        <div class="tx-16 font-weight-bold">
                                                            {{ number_format($remainingAmount, 2) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-6">
                                                <div class="card bg-info-transparent mb-3">
                                                    <div class="card-body py-3">
                                                        <div class="tx-12 text-muted mb-1">
                                                            {{ __('invoices.details.payment_date') }}</div>
                                                        <div class="tx-16 font-weight-bold">
                                                            {{ $invoice->payment_date?->format('Y-m-d') ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Invoice identity details section (numbers, entity, dates, product) --}}
                                        <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <div class="card mb-0">
                                                    <div class="card-header pb-2">
                                                        <h6 class="card-title mb-0">{{ __('invoices.details.tab_info') }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body pt-3 pb-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless table-sm mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="w-50">
                                                                            {{ __('invoices.form.invoice_number') }}</th>
                                                                        <td>{{ $invoice->invoice_number }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.external_invoice_number') }}
                                                                        </th>
                                                                        <td>{{ $invoice->external_invoice_number ?: '-' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.organization') }}</th>
                                                                        <td>{{ $invoice->organization?->name ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.product') }}</th>
                                                                        <td>{{ $invoice->productModel?->name ?? $invoice->product }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.invoice_date') }}</th>
                                                                        <td>{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.due_date') }}</th>
                                                                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Detailed financial values section for the invoice --}}
                                            <div class="col-lg-6 mb-3">
                                                <div class="card mb-0">
                                                    <div class="card-header pb-2">
                                                        <h6 class="card-title mb-0">{{ __('invoices.form.total') }}</h6>
                                                    </div>
                                                    <div class="card-body pt-3 pb-2">
                                                        <div class="table-responsive">
                                                            <table class="table table-borderless table-sm mb-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <th class="w-50">
                                                                            {{ __('invoices.form.amount_collection') }}
                                                                        </th>
                                                                        <td>{{ number_format((float) $invoice->amount_collection, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.commission_rate') }}</th>
                                                                        <td>{{ number_format((float) $invoice->commission_rate, 2) }}%
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.amount_commission') }}
                                                                        </th>
                                                                        <td>{{ number_format((float) $invoice->amount_commission, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.discount_amount') }}</th>
                                                                        <td>{{ number_format((float) $invoice->discount_amount, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.value_vat') }}</th>
                                                                        <td>{{ number_format((float) $invoice->value_vat, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.form.total') }}</th>
                                                                        <td>{{ number_format((float) $invoice->total, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.details.created_by') }}</th>
                                                                        <td>{{ $invoice->creator?->name ?? '-' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ __('invoices.details.created_at') }}</th>
                                                                        <td>{{ $invoice->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Payment status update form with amount/date/note tracking --}}
                                        <div class="card mb-0 mt-2 status-update-focus">
                                            <div class="card-header pb-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="las la-sync-alt mr-1"></i>
                                                    {{ __('invoices.details.change_status') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <p class="text-muted mb-3">
                                                    {{ __('invoices.details.status_financial_hint', [
                                                        'paid' => number_format((float) $invoice->paid_amount, 2),
                                                        'remaining' => number_format($remainingAmount, 2),
                                                    ]) }}
                                                </p>
                                                <form action="{{ route('invoices.status.update', $invoice) }}"
                                                    method="post" class="mb-0">
                                                    @csrf
                                                    @method('patch')
                                                    <div class="row">
                                                        <div class="col-lg-3 col-md-6 mb-2">
                                                            {{-- Select the new status to apply to this invoice --}}
                                                            <select name="status" class="form-control" required>
                                                                <option value="unpaid"
                                                                    {{ $invoice->status === 'unpaid' ? 'selected' : '' }}>
                                                                    {{ __('invoices.status.unpaid') }}</option>
                                                                <option value="partial"
                                                                    {{ $invoice->status === 'partial' ? 'selected' : '' }}>
                                                                    {{ __('invoices.status.partial') }}</option>
                                                                <option value="paid"
                                                                    {{ $invoice->status === 'paid' ? 'selected' : '' }}>
                                                                    {{ __('invoices.status.paid') }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3 col-md-6 mb-2">
                                                            {{-- Payment amount received in this specific update --}}
                                                            <input type="number" name="payment_amount" min="0"
                                                                step="0.01" class="form-control"
                                                                placeholder="{{ __('invoices.details.payment_amount') }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-6 mb-2">
                                                            {{-- Actual payment date for the current recorded transaction --}}
                                                            <input type="date" name="payment_date"
                                                                class="form-control" value="{{ now()->toDateString() }}">
                                                        </div>
                                                        <div class="col-lg-3 col-md-6 mb-2">
                                                            {{-- Submit status change and append a history record --}}
                                                            <button type="submit" class="btn btn-primary btn-block">
                                                                {{ __('invoices.details.update_status') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1">
                                                        <div class="col-md-12">
                                                            {{-- Optional note describing reason/details of status change --}}
                                                            <input type="text" name="note" class="form-control"
                                                                placeholder="{{ __('invoices.details.status_note') }}">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Status history tab: timeline of payment state changes (table view) --}}
                                    <div class="tab-pane" id="invoice-statuses">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-md-nowrap mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('invoices.details.history_date') }}</th>
                                                        <th>{{ __('invoices.details.history_by') }}</th>
                                                        <th>{{ __('invoices.details.history_from') }}</th>
                                                        <th>{{ __('invoices.details.history_to') }}</th>
                                                        <th>{{ __('invoices.details.payment_amount') }}</th>
                                                        <th>{{ __('invoices.details.history_remaining') }}</th>
                                                        <th>{{ __('invoices.details.payment_date') }}</th>
                                                        <th>{{ __('invoices.details.history_note') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($invoice->statusHistories as $history)
                                                        <tr>
                                                            <td>{{ $history->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td>{{ $history->changedBy?->name ?? '-' }}</td>
                                                            @php
                                                                $fromStatusLabel = '-';
                                                                if ($history->from_status) {
                                                                    $fromStatusKey =
                                                                        'invoices.status.' . $history->from_status;
                                                                    $fromStatusLabel = \Illuminate\Support\Facades\Lang::has(
                                                                        $fromStatusKey,
                                                                    )
                                                                        ? __($fromStatusKey)
                                                                        : __('invoices.status.unknown');
                                                                }
                                                                $toStatusKey = 'invoices.status.' . $history->to_status;
                                                                $toStatusLabel = \Illuminate\Support\Facades\Lang::has(
                                                                    $toStatusKey,
                                                                )
                                                                    ? __($toStatusKey)
                                                                    : __('invoices.status.unknown');
                                                                $remainingForHistory = max(
                                                                    (float) $invoice->total -
                                                                        (float) ($history->payment_amount ?? 0),
                                                                    0,
                                                                );
                                                            @endphp
                                                            <td>{{ $fromStatusLabel }}</td>
                                                            <td>{{ $toStatusLabel }}</td>
                                                            <td>{{ $history->payment_amount !== null ? number_format((float) $history->payment_amount, 2) : '-' }}
                                                            </td>
                                                            <td>{{ number_format($remainingForHistory, 2) }}</td>
                                                            <td>{{ $history->payment_date?->format('Y-m-d') ?? '-' }}</td>
                                                            <td>{{ $history->note ?: '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            {{-- Fallback message when no status history exists --}}
                                                            <td colspan="8" class="text-center text-muted">
                                                                {{ __('invoices.details.no_status_history') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Attachments tab: upload new files and manage existing ones --}}
                                    <div class="tab-pane" id="invoice-attachments">
                                        {{-- Form to upload a new attachment for this invoice --}}
                                        <form action="{{ route('invoices.attachments.store', $invoice) }}" method="post"
                                            enctype="multipart/form-data" class="mb-4">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-8 mb-2">
                                                    {{-- File input for selecting attachment to upload --}}
                                                    <input type="file" name="attachment" class="form-control"
                                                        required>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    {{-- Upload button to store attachment in system storage --}}
                                                    <button type="submit"
                                                        class="btn btn-success btn-block">{{ __('invoices.details.upload_button') }}</button>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="table-responsive">
                                            <table class="table table-bordered text-md-nowrap mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('invoices.details.file_name') }}</th>
                                                        <th>{{ __('invoices.details.file_size') }}</th>
                                                        <th>{{ __('invoices.details.history_by') }}</th>
                                                        <th>{{ __('invoices.details.history_date') }}</th>
                                                        <th>{{ __('invoices.table.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($invoice->attachments as $attachment)
                                                        <tr>
                                                            <td>{{ $attachment->original_name }}</td>
                                                            <td>{{ number_format($attachment->file_size / 1024, 2) }} KB
                                                            </td>
                                                            <td>{{ $attachment->uploader?->name ?? '-' }}</td>
                                                            <td>{{ $attachment->created_at?->format('Y-m-d H:i') ?? '-' }}
                                                            </td>
                                                            <td class="text-nowrap">
                                                                {{-- Download button for the selected attachment --}}
                                                                <a href="{{ route('invoices.attachments.download', [$invoice, $attachment]) }}"
                                                                    class="btn btn-sm btn-info">{{ __('invoices.details.download') }}</a>
                                                                <form
                                                                    action="{{ route('invoices.attachments.destroy', [$invoice, $attachment]) }}"
                                                                    method="post" class="d-inline">
                                                                    @csrf
                                                                    @method('delete')
                                                                    {{-- Delete button to remove attachment permanently --}}
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-danger">{{ __('invoices.details.delete') }}</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            {{-- Fallback message when no attachments are available --}}
                                                            <td colspan="5" class="text-center text-muted">
                                                                {{ __('invoices.details.no_attachments') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
@endsection
