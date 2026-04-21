@extends('layouts.master')

@section('title')
    {{ __('invoices.page.create_title') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('invoices.page.title') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('invoices.page.create_title') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    {{-- Validation error block shown after form submission --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    {{-- New invoice creation form: collects core and financial data and submits to invoices.store --}}
                    <form action="{{ route('invoices.store') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf

                        <div class="row">
                            <div class="col-md-4">
                                {{-- Internal invoice number is generated automatically, so this field is display-only --}}
                                <label>{{ __('invoices.form.invoice_number') }}</label>
                                <input type="text" class="form-control" value="Auto-generated" readonly>
                            </div>

                            <div class="col-md-4">
                                {{-- Optional external reference invoice number from bank/entity --}}
                                <label>{{ __('invoices.form.external_invoice_number') }}</label>
                                <input class="form-control" name="external_invoice_number" type="text"
                                    value="{{ old('external_invoice_number') }}">
                            </div>

                            <div class="col-md-4">
                                {{-- Invoice date defaults to today and remains editable --}}
                                <label>{{ __('invoices.form.invoice_date') }}</label>
                                <input class="form-control" name="invoice_date" type="date"
                                    value="{{ old('invoice_date', now()->toDateString()) }}" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                {{-- Optional due date used for payment timeline tracking --}}
                                <label>{{ __('invoices.form.due_date') }}</label>
                                <input class="form-control" name="due_date" type="date" value="{{ old('due_date') }}">
                            </div>

                            <div class="col-md-4">
                                {{-- Select organization/entity: first step to load related products --}}
                                <label>{{ __('invoices.form.organization') }}</label>
                                <select name="organization_id" id="organization_id" class="form-control" required>
                                    <option value="" selected disabled>--
                                        {{ __('invoices.form.select_organization') }} --</option>
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}"
                                            {{ (string) old('organization_id') === (string) $organization->id ? 'selected' : '' }}>
                                            {{ $organization->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                {{-- Product list is loaded dynamically based on selected organization --}}
                                <label>{{ __('invoices.form.product') }}</label>
                                <select id="product_id" name="product_id" class="form-control" required>
                                    <option value="" selected disabled>-- {{ __('invoices.form.select_product') }} --
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                {{-- Actual collected amount entered by user and used as base for all calculations --}}
                                <label>{{ __('invoices.form.amount_collection') }}</label>
                                <input type="number" class="form-control" id="amount_collection" name="amount_collection"
                                    min="0" step="0.01" value="{{ old('amount_collection') }}" required>
                            </div>

                            <div class="col-md-4">
                                {{-- Commission rate is auto-calculated from organization/product settings --}}
                                <label>{{ __('invoices.form.commission_rate') }}</label>
                                <input type="text" class="form-control" id="commission_rate" readonly>
                            </div>

                            <div class="col-md-4">
                                {{-- Commission amount resulting from applying the commission rate --}}
                                <label>{{ __('invoices.form.amount_commission') }}</label>
                                <input type="text" class="form-control" id="amount_commission" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                {{-- Discount type: fixed amount or percentage for precise discount calculation --}}
                                <label>{{ __('invoices.form.discount_type') }}</label>
                                <select name="discount_type" id="discount_type" class="form-control" required>
                                    <option value="fixed"
                                        {{ old('discount_type', 'fixed') === 'fixed' ? 'selected' : '' }}>
                                        {{ __('invoices.form.discount_type_fixed') }}
                                    </option>
                                    <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>
                                        {{ __('invoices.form.discount_type_percent') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                {{-- Raw discount value entered by user based on selected discount type --}}
                                <label>{{ __('invoices.form.discount_value') }}</label>
                                <input type="number" class="form-control" id="discount_value" name="discount_value"
                                    min="0" step="0.01" value="{{ old('discount_value', 0) }}" required>
                            </div>

                            <div class="col-md-4">
                                {{-- Final calculated discount after processing type and value --}}
                                <label>{{ __('invoices.form.discount_amount') }}</label>
                                <input type="text" class="form-control" id="discount_amount" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                {{-- VAT rate applied to the net amount --}}
                                <label>{{ __('invoices.form.rate_vat') }}</label>
                                <input type="number" class="form-control" id="rate_vat" name="rate_vat" min="0"
                                    max="100" step="0.01" value="{{ old('rate_vat', 5) }}" required>
                            </div>

                            <div class="col-md-4">
                                {{-- Tax value calculated from the selected VAT rate --}}
                                <label>{{ __('invoices.form.value_vat') }}</label>
                                <input type="text" class="form-control" id="value_vat" readonly>
                            </div>

                            <div class="col-md-4">
                                {{-- Final invoice total after commission, discount, and VAT --}}
                                <label>{{ __('invoices.form.total') }}</label>
                                <input type="text" class="form-control" id="total" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                {{-- Optional internal notes to document invoice context --}}
                                <label>{{ __('invoices.form.note') }}</label>
                                <textarea class="form-control" name="note" rows="3">{{ old('note', __('invoices.messages.default_note')) }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                {{-- Optional supporting attachment such as bank statement or collection notice --}}
                                <label>{{ __('invoices.form.attachment') }}</label>
                                <input type="file" name="attachment" class="dropify"
                                    accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf" data-height="80" />
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{-- Submit button to create and store the invoice --}}
                            <button type="submit" class="btn btn-primary px-4">{{ __('invoices.form.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

    <script>
        // Core form elements used by product loading and live calculations.
        const organizationSelect = $('#organization_id');
        const productSelect = $('#product_id');
        const amountCollectionInput = $('#amount_collection');
        const discountTypeInput = $('#discount_type');
        const discountValueInput = $('#discount_value');
        const rateVatInput = $('#rate_vat');

        const commissionRateInput = $('#commission_rate');
        const amountCommissionInput = $('#amount_commission');
        const discountAmountInput = $('#discount_amount');
        const valueVatInput = $('#value_vat');
        const totalInput = $('#total');

        const oldProductId = @json(old('product_id'));

        // Reset calculated fields when input is insufficient or preview request fails.
        function resetCalculationFields() {
            commissionRateInput.val('0.00%');
            amountCommissionInput.val('0.00');
            discountAmountInput.val('0.00');
            valueVatInput.val('0.00');
            totalInput.val('0.00');
        }

        // Normalize decimal formatting before showing values in readonly fields.
        function formatAmount(value) {
            return Number(value || 0).toFixed(2);
        }

        // Keep only one pending preview request schedule while user is typing.
        let timer = null;

        // Load products for selected organization to keep product selection scoped correctly.
        function loadProducts(organizationId, selectedProductId = null) {
            if (!organizationId) {
                productSelect.html(
                    '<option value="" selected disabled>-- {{ __('invoices.form.select_product') }} --</option>');
                return;
            }

            const productsUrlTemplate = @json(route('organizations.products.index', ['organization' => '__ORG__']));
            const productsUrl = productsUrlTemplate.replace('__ORG__', organizationId);

            $.get(productsUrl, function(products) {
                const options = [
                    '<option value="" selected disabled>-- {{ __('invoices.form.select_product') }} --</option>'
                ];

                products.forEach((product) => {
                    const selected = selectedProductId && String(selectedProductId) === String(product.id) ?
                        'selected' : '';
                    options.push(`<option value="${product.id}" ${selected}>${product.name}</option>`);
                });

                productSelect.html(options.join(''));
                calculatePreview();
            });
        }

        // Real calculation request to backend (commission/discount/tax/total).
        function requestCalculationPreview() {
            const organizationId = organizationSelect.val();
            const productId = productSelect.val();
            const amountCollection = amountCollectionInput.val();

            if (!organizationId || !productId || !amountCollection) {
                resetCalculationFields();
                return;
            }

            $.ajax({
                url: '{{ route('invoices.preview-calculation') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    organization_id: organizationId,
                    product_id: productId,
                    amount_collection: amountCollection,
                    discount_type: discountTypeInput.val(),
                    discount_value: discountValueInput.val(),
                    rate_vat: rateVatInput.val(),
                },
                success: function(response) {
                    commissionRateInput.val(`${formatAmount(response.commission_rate)}%`);
                    amountCommissionInput.val(formatAmount(response.amount_commission));
                    discountAmountInput.val(formatAmount(response.discount_amount));
                    valueVatInput.val(formatAmount(response.value_vat));
                    totalInput.val(formatAmount(response.total));
                },
                error: function() {
                    resetCalculationFields();
                }
            });
        }

        // Delay calculation slightly so we do not send a request on every keystroke.
        function calculatePreview() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                requestCalculationPreview();
            }, 900);
        }

        // Refresh product list whenever organization changes.
        organizationSelect.on('change', function() {
            loadProducts($(this).val());
        });

        // Any calculation-related field change triggers preview recalculation.
        productSelect.on('change', calculatePreview);
        amountCollectionInput.on('input', calculatePreview);
        discountTypeInput.on('change', calculatePreview);
        discountValueInput.on('input', calculatePreview);
        rateVatInput.on('input', calculatePreview);

        // Initial page setup: restore old product and trigger first calculation preview.
        $(document).ready(function() {
            const oldOrganizationId = organizationSelect.val();

            if (oldOrganizationId) {
                loadProducts(oldOrganizationId, oldProductId);
            }

            resetCalculationFields();
            calculatePreview();
        });
    </script>
@endsection
