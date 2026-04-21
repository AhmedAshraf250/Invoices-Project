@extends('layouts.master')

@section('title')
    {{ __('invoices.page.title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        .invoice-summary-card .card-body {
            padding: 0.9rem 1rem;
        }

        .invoice-summary-value {
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.2;
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
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('invoices.page.title') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('invoices.page.breadcrumb') }}</span>
            </div>
        </div>
        <div>
            {{-- Button to navigate to invoice creation page --}}
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="las la-plus"></i> {{ __('invoices.page.create_title') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Quick counters used for top summary cards without extra query --}}
    @php
        $paidCount = $invoices->where('status', 'paid')->count();
        $partialCount = $invoices->where('status', 'partial')->count();
        $unpaidCount = $invoices->where('status', 'unpaid')->count();
        $overallTotal = $invoices->sum(fn($invoice) => (float) $invoice->total);
    @endphp

    {{-- Success alert for actions such as create/update --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Error alert for general server-side action failures --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Summary KPI cards showing invoice counts by status --}}
    <div class="row row-sm mb-2">
        <div class="col-xl-3 col-md-6">
            <div class="card invoice-summary-card bg-primary-transparent">
                <div class="card-body">
                    <div class="tx-12 text-muted mb-1">{{ __('invoices.summary.total_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $invoices->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card invoice-summary-card bg-success-transparent">
                <div class="card-body">
                    <div class="tx-12 text-muted mb-1">{{ __('invoices.summary.paid_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $paidCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card invoice-summary-card bg-warning-transparent">
                <div class="card-body">
                    <div class="tx-12 text-muted mb-1">{{ __('invoices.summary.partial_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $partialCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card invoice-summary-card bg-danger-transparent">
                <div class="card-body">
                    <div class="tx-12 text-muted mb-1">{{ __('invoices.summary.unpaid_invoices') }}</div>
                    <div class="invoice-summary-value">{{ $unpaidCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        {{-- Main title for the invoice listing table --}}
                        <h4 class="card-title mg-b-0">{{ __('invoices.page.card_title') }}</h4>
                        {{-- Badge showing total amount for currently listed invoices --}}
                        <span class="badge badge-light">
                            {{ __('invoices.summary.overall_total') }}:
                            {{ number_format((float) $overallTotal, 2) }}
                        </span>
                    </div>
                    <p class="tx-12 tx-gray-500 mb-2">{{ __('invoices.page.card_description') }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- Main invoice table with DataTables features (search/sort/export) --}}
                        <table id="example" class="table key-buttons text-md-nowrap invoice-table">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">{{ __('invoices.table.id') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.invoice_number') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.invoice_date') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.due_date') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.product') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.organization') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.discount') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.rate_vat') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.value_vat') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.total') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.status') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.note') }}</th>
                                    <th class="border-bottom-0">{{ __('invoices.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    @php
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
                                            {{-- Invoice number link opens the invoice details page --}}
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-primary invoice-number-link">
                                                <i class="las la-file-invoice tx-18"></i>
                                                {{ $invoice->invoice_number }}
                                            </a>
                                            {{-- External reference number shown as secondary line if available --}}
                                            @if ($invoice->external_invoice_number)
                                                <div class="tx-11 text-muted mt-1">
                                                    {{ $invoice->external_invoice_number }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $invoice->productModel?->name ?? $invoice->product }}</td>
                                        <td>{{ $invoice->organization?->name ?? '-' }}</td>
                                        <td>{{ number_format((float) $invoice->discount_amount, 2) }}</td>
                                        <td>{{ number_format((float) $invoice->rate_vat, 2) }}%</td>
                                        <td>{{ number_format((float) $invoice->value_vat, 2) }}</td>
                                        <td class="font-weight-bold">{{ number_format((float) $invoice->total, 2) }}</td>
                                        {{-- Status badge for quick visual state recognition --}}
                                        <td><span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td class="text-muted">{{ $invoice->note ?: '-' }}</td>
                                        <td>
                                            {{-- Button to open details for the selected invoice --}}
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="las la-eye"></i>
                                                {{ __('invoices.page.details_title') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Fallback message when no invoices are available --}}
                                        <td colspan="13" class="text-center text-muted">{{ __('invoices.messages.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
