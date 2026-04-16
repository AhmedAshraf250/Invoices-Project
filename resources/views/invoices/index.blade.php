@extends('layouts.master')

@section('title')
    {{ __('invoices.page.title') }}
@endsection

@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('invoices.page.title') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('invoices.page.breadcrumb') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">{{ __('invoices.page.card_title') }}</h4>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                    <p class="tx-12 tx-gray-500 mb-2">{{ __('invoices.page.card_description') }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example" class="table key-buttons text-md-nowrap">
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
                                            2 => 'warning',
                                            default => 'danger',
                                        };
                                        $statusKey = "invoices.status.{$invoice->status}";
                                        $statusLabel = \Illuminate\Support\Facades\Lang::has($statusKey)
                                            ? __($statusKey)
                                            : __('invoices.status.unknown');
                                    @endphp
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $invoice->product }}</td>
                                        <td>{{ $invoice->organization?->name ?? '-' }}</td>
                                        <td>{{ number_format((float) $invoice->discount, 2) }}</td>
                                        <td>{{ number_format((float) $invoice->rate_vat, 2) }}%</td>
                                        <td>{{ number_format((float) $invoice->value_vat, 2) }}</td>
                                        <td>{{ number_format((float) $invoice->total, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>{{ $invoice->note ?: '-' }}</td>
                                        <td>-</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center text-muted">
                                            {{ __('invoices.messages.empty') }}
                                        </td>
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
    <!-- Internal Data tables -->
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
    <!-- Internal Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
@endsection
