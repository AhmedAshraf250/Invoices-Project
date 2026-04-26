@extends('layouts.master')

@section('title')
    {{ __('invoices.page.print_title') }}
@endsection

@section('css')
    <style>
        .invoice-print-shell {
            max-width: 1040px;
            margin: 0 auto;
        }

        .card-invoice-modern {
            border: 1px solid #e5e9f2;
            border-radius: 14px;
            box-shadow: 0 20px 45px -35px rgba(0, 0, 0, 0.45);
            overflow: hidden;
        }

        .invoice-header-band {
            background: linear-gradient(135deg, #0f3fa9 0%, #1f66e5 100%);
            color: #fff;
            padding: 1.25rem 1.5rem;
        }

        .invoice-title-main {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0;
        }

        .invoice-sub-main {
            opacity: 0.88;
            margin-top: 0.25rem;
            margin-bottom: 0;
        }

        .invoice-body {
            padding: 1.4rem 1.4rem 1.6rem;
            background: #fff;
        }

        .invoice-label {
            color: #7a8194;
            font-size: 0.82rem;
            margin-bottom: 0.15rem;
        }

        .invoice-value {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.9rem;
        }

        .invoice-table th,
        .invoice-table td {
            vertical-align: middle;
        }

        .invoice-total {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f3fa9;
        }

        @media print {
            @page {
                margin: 10mm;
            }

            body,
            html {
                background: #fff !important;
            }

            .main-header,
            .main-sidebar,
            .main-footer,
            .breadcrumb-header,
            .no-print,
            #back-to-top {
                display: none !important;
            }

            .main-content,
            .app-content,
            .container-fluid,
            .invoice-print-shell,
            .row,
            .col-xl-12 {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-invoice-modern {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between no-print">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('invoices.page.print_title') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $invoice->invoice_number }}</span>
            </div>
        </div>
        <div>
            {{-- Print controls for invoice sheet --}}
            <button type="button" onclick="window.print()" class="btn btn-primary mr-2">
                <i class="las la-print mr-1"></i>{{ __('invoices.actions.print') }}
            </button>
            <a href="{{ $invoice->trashed() ? route('invoices.archived') : route('invoices.show', $invoice->id) }}"
                class="btn btn-outline-primary">
                {{ __('invoices.actions.back_to_details') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    @php
        $statusKey = 'invoices.status.' . $invoice->status;
        $statusLabel = \Illuminate\Support\Facades\Lang::has($statusKey)
            ? __($statusKey)
            : __('invoices.status.unknown');
        $remainingAmount = max((float) $invoice->total - (float) $invoice->paid_amount, 0);
    @endphp

    <div class="invoice-print-shell">
        <div class="card card-invoice-modern">
            <div class="invoice-header-band d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="invoice-title-main">{{ __('invoices.page.collection_invoice_title') }}</h1>
                    <p class="invoice-sub-main">{{ config('app.name') }}</p>
                </div>
                <div class="text-md-right mt-2 mt-md-0">
                    <div class="font-weight-bold">{{ __('invoices.form.invoice_number') }}: {{ $invoice->invoice_number }}</div>
                    <div>{{ __('invoices.form.invoice_date') }}: {{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
            </div>

            <div class="invoice-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="invoice-label">{{ __('invoices.form.organization') }}</div>
                        <div class="invoice-value">{{ $invoice->organization?->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="invoice-label">{{ __('invoices.form.product') }}</div>
                        <div class="invoice-value">{{ $invoice->productModel?->name ?? $invoice->product }}</div>
                    </div>
                </div>

                <table class="table table-bordered invoice-table mt-2 mb-4">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('invoices.form.amount_collection') }}</th>
                            <th>{{ __('invoices.form.rate_vat') }}</th>
                            <th>{{ __('invoices.form.value_vat') }}</th>
                            <th>{{ __('invoices.form.discount_amount') }}</th>
                            <th>{{ __('invoices.form.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ number_format((float) $invoice->amount_collection, 2) }}</td>
                            <td>{{ number_format((float) $invoice->rate_vat, 2) }}%</td>
                            <td>{{ number_format((float) $invoice->value_vat, 2) }}</td>
                            <td>{{ number_format((float) $invoice->discount_amount, 2) }}</td>
                            <td class="invoice-total">{{ number_format((float) $invoice->total, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-4">
                        <div class="invoice-label">{{ __('invoices.table.status') }}</div>
                        <div class="invoice-value">{{ $statusLabel }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="invoice-label">{{ __('invoices.details.paid_amount') }}</div>
                        <div class="invoice-value">{{ number_format((float) $invoice->paid_amount, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="invoice-label">{{ __('invoices.details.remaining_amount') }}</div>
                        <div class="invoice-value">{{ number_format($remainingAmount, 2) }}</div>
                    </div>
                </div>

                <div class="invoice-label">{{ __('invoices.form.note') }}</div>
                <div class="invoice-value mb-0">{{ $invoice->note ?: '-' }}</div>
            </div>
        </div>
    </div>
@endsection
