@extends('layouts.master')

@section('title')
    {{ __('invoices.page.archived_title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        .invoice-summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0.75rem;
        }

        .invoice-summary-item {
            flex: 1 1 calc(20% - 10px);
            min-width: 210px;
        }

        .invoice-summary-card .card-body {
            padding: 1rem 1.1rem;
        }

        .invoice-summary-value {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .invoice-summary-label {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .invoice-table tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .invoice-number-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
@endsection

@section('page-header')
    @php
        $canViewInvoices = auth()->user()?->can('invoices.view') ?? false;
        $canExportInvoices = auth()->user()?->can('invoices.export') ?? false;
        $canListInvoices = auth()->user()?->can('invoices.list') ?? false;
    @endphp
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('common.sidebar.invoices') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('invoices.page.archived_title') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center" style="gap: 12px;">
            @if ($canExportInvoices)
                <a href="{{ route('invoices.export.excel', ['status' => $statusFilter, 'archived' => 1]) }}"
                    class="btn btn-outline-success px-4 py-2 font-weight-bold">
                    <i class="las la-file-excel mr-1"></i> {{ __('invoices.actions.export_excel') }}
                </a>
            @endif

            @if ($canListInvoices)
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-primary px-4 py-2 font-weight-bold">
                    <i class="las la-list mr-1"></i> {{ __('invoices.page.breadcrumb') }}
                </a>
            @endif
        </div>
    </div>
@endsection

@section('content')
    {{-- Success alert for restore/delete operations --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="invoice-summary-grid">
        <div class="invoice-summary-item">
            <div class="card invoice-summary-card bg-primary-transparent border border-primary">
                <div class="card-body">
                    <div class="invoice-summary-label text-muted mb-1">{{ __('invoices.summary.archived_total_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $summary['total'] }}</div>
                </div>
            </div>
        </div>

        <div class="invoice-summary-item">
            <div class="card invoice-summary-card bg-success-transparent">
                <div class="card-body">
                    <div class="invoice-summary-label text-muted mb-1">{{ __('invoices.summary.archived_paid_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $summary['paid'] }}</div>
                </div>
            </div>
        </div>

        <div class="invoice-summary-item">
            <div class="card invoice-summary-card bg-warning-transparent">
                <div class="card-body">
                    <div class="invoice-summary-label text-muted mb-1">{{ __('invoices.summary.archived_partial_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $summary['partial'] }}</div>
                </div>
            </div>
        </div>

        <div class="invoice-summary-item">
            <div class="card invoice-summary-card bg-danger-transparent">
                <div class="card-body">
                    <div class="invoice-summary-label text-muted mb-1">{{ __('invoices.summary.archived_unpaid_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $summary['unpaid'] }}</div>
                </div>
            </div>
        </div>

        <div class="invoice-summary-item">
            <div class="card invoice-summary-card bg-secondary-transparent border border-secondary">
                <div class="card-body">
                    <div class="invoice-summary-label text-muted mb-1">{{ __('invoices.summary.overall_total') }}</div>
                    <div class="invoice-summary-value">{{ number_format((float) ($summary['overall_total'] ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">{{ __('invoices.page.archived_title') }}</h4>
                        <span class="badge badge-light">
                            {{ __('invoices.summary.overall_total') }}:
                            {{ number_format((float) ($summary['overall_total'] ?? 0), 2) }}
                        </span>
                    </div>
                    <p class="tx-12 tx-gray-500 mb-2">{{ __('invoices.page.card_description') }}</p>
                </div>
                <div class="card-body">
                    <table id="example" class="table key-buttons text-md-nowrap invoice-table disable-responsive">
                        <thead>
                            <tr>
                                <th class="border-bottom-0">{{ __('invoices.table.id') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.invoice_number') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.invoice_date') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.due_date') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.product') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.organization') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.total') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.status') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.note') }}</th>
                                <th class="border-bottom-0">{{ __('invoices.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                @php
                                    $canRestoreInvoice = auth()->user()?->can('invoices.restore') ?? false;
                                    $canDeleteInvoice = auth()->user()?->can('invoices.delete') ?? false;
                                    $hasRowActions = $canViewInvoices || $canRestoreInvoice || $canDeleteInvoice;
                                    $statusClass = match ($invoice->status_value) {
                                        1 => 'success',
                                        2 => 'danger',
                                        default => 'warning',
                                    };
                                    $statusKey = "invoices.status.{$invoice->status}";
                                    $statusLabel = \Illuminate\Support\Facades\Lang::has($statusKey)
                                        ? __($statusKey)
                                        : __('invoices.status.unknown');
                                @endphp
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>
                                        @if ($canViewInvoices)
                                            <a href="{{ route('invoices.print', $invoice->id) }}" class="text-primary invoice-number-link" target="_blank">
                                                <i class="las la-file-invoice tx-18"></i>
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        @else
                                            <span class="invoice-number-link text-muted">
                                                <i class="las la-file-invoice tx-18"></i>
                                                {{ $invoice->invoice_number }}
                                            </span>
                                        @endif
                                        @if ($invoice->external_invoice_number)
                                            <div class="tx-11 text-muted mt-1">{{ $invoice->external_invoice_number }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $invoice->productModel?->name ?? $invoice->product }}</td>
                                    <td>{{ $invoice->organization?->name ?? '-' }}</td>
                                    <td class="font-weight-bold">{{ number_format((float) $invoice->total, 2) }}</td>
                                    <td><span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="text-muted">{{ $invoice->note ?: '-' }}</td>
                                    <td class="text-nowrap">
                                        @if ($hasRowActions)
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline-primary" type="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{ __('invoices.table.actions') }}
                                                    <i class="fas fa-caret-down ml-1"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if ($canViewInvoices)
                                                        <a class="dropdown-item" href="{{ route('invoices.print', $invoice->id) }}" target="_blank">
                                                            <i class="las la-eye mr-1"></i>{{ __('invoices.actions.view_details') }}
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('invoices.print', $invoice->id) }}" target="_blank">
                                                            <i class="las la-print mr-1"></i>{{ __('invoices.actions.print') }}
                                                        </a>
                                                    @endif
                                                    @if ($canRestoreInvoice)
                                                        <button class="dropdown-item text-success" data-toggle="modal"
                                                            data-target="#restoreInvoiceModal{{ $invoice->id }}">
                                                            <i class="las la-undo mr-1"></i>{{ __('invoices.actions.restore') }}
                                                        </button>
                                                    @endif
                                                    @if ($canDeleteInvoice)
                                                        <button class="dropdown-item text-danger" data-toggle="modal"
                                                            data-target="#forceDeleteInvoiceModal{{ $invoice->id }}">
                                                            <i class="las la-trash-alt mr-1"></i>{{ __('invoices.actions.force_delete') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">{{ __('invoices.table.no_actions') }}</span>
                                        @endif
                                    </td>
                                </tr>

                                @if ($canRestoreInvoice)
                                    <div class="modal fade" id="restoreInvoiceModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('invoices.actions.restore') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">{{ __('invoices.confirmations.restore') }}</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{ __('invoices.actions.cancel') }}</button>
                                                    <form action="{{ route('invoices.restore', $invoice->id) }}" method="post">
                                                        @csrf
                                                        @method('patch')
                                                        <button type="submit" class="btn btn-success">{{ __('invoices.actions.confirm') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($canDeleteInvoice)
                                    <div class="modal fade" id="forceDeleteInvoiceModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('invoices.actions.force_delete') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">{{ __('invoices.confirmations.force_delete') }}</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{ __('invoices.actions.cancel') }}</button>
                                                    <form action="{{ route('invoices.force-delete', $invoice->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger">{{ __('invoices.actions.confirm') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">{{ __('invoices.messages.empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
@endsection
