@extends('layouts.master')

@section('title')
    {{ __('products.page.title') }}
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
                <h4 class="content-title mb-0 my-auto">{{ __('products.page.settings') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('products.page.title') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session('success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card mg-b-20">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-center">
                        <button class="modal-effect btn btn-primary px-4 py-2" data-effect="effect-scale"
                            data-toggle="modal" data-target="#createProductModal">
                            <i class="las la-plus mr-1"></i>
                            {{ __('products.actions.add') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" style="text-align: center">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">{{ __('products.table.id') }}</th>
                                    <th class="border-bottom-0">{{ __('products.table.name') }}</th>
                                    <th class="border-bottom-0">{{ __('products.table.organization') }}</th>
                                    <th class="border-bottom-0">{{ __('products.table.commission_rate') }}</th>
                                    <th class="border-bottom-0">{{ __('products.table.description') }}</th>
                                    <th class="border-bottom-0">{{ __('products.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->organization?->name ?? '-' }}</td>
                                        <td>
                                            {{ $product->commission_rate !== null ? number_format((float) $product->commission_rate, 2) . '%' : __('products.form.inherit_from_organization') }}
                                        </td>
                                        <td>{{ $product->description ?: '-' }}</td>
                                        <td class="text-nowrap">
                                            <div class="btn-group" role="group"
                                                aria-label="{{ __('products.table.actions') }}">
                                                <button class="btn btn-sm btn-info" data-name="{{ $product->name }}"
                                                    data-organization-id="{{ $product->organization_id }}"
                                                    data-description="{{ $product->description }}"
                                                    data-commission-rate="{{ $product->commission_rate }}"
                                                    data-update-url="{{ route('products.update', $product) }}"
                                                    data-toggle="modal" data-target="#editProductModal"
                                                    title="{{ __('products.actions.edit') }}">
                                                    {{ __('products.actions.edit') }}
                                                </button>
                                                <button class="btn btn-sm btn-danger" data-name="{{ $product->name }}"
                                                    data-delete-url="{{ route('products.destroy', $product) }}"
                                                    data-toggle="modal" data-target="#deleteProductModal"
                                                    title="{{ __('products.actions.delete') }}">
                                                    {{ __('products.actions.delete') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            {{ __('products.messages.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('products.actions.add') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_product_name">{{ __('products.form.name') }}</label>
                            <input type="text" class="form-control" id="create_product_name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="create_organization_id">{{ __('products.form.organization') }}</label>
                            <select name="organization_id" id="create_organization_id" class="form-control" required>
                                <option value="" selected disabled>-- {{ __('products.form.select_organization') }} --
                                </option>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="create_description">{{ __('products.form.description') }}</label>
                            <textarea class="form-control" id="create_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create_commission_rate">{{ __('products.form.commission_rate') }}</label>
                            <input type="number" class="form-control" id="create_commission_rate" name="commission_rate"
                                min="0" max="100" step="0.01" placeholder="{{ __('products.form.inherit_from_organization') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('products.actions.confirm') }}</button>
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('products.actions.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('products.actions.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit_product_form" method="post">
                    @method('patch')
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_product_name">{{ __('products.form.name') }}</label>
                            <input type="text" class="form-control" id="edit_product_name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_organization_id">{{ __('products.form.organization') }}</label>
                            <select name="organization_id" id="edit_organization_id" class="form-control" required>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_description">{{ __('products.form.description') }}</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_commission_rate">{{ __('products.form.commission_rate') }}</label>
                            <input type="number" class="form-control" id="edit_commission_rate" name="commission_rate"
                                min="0" max="100" step="0.01" placeholder="{{ __('products.form.inherit_from_organization') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ __('products.actions.confirm') }}</button>
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('products.actions.close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('products.actions.delete') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="delete_product_form" method="post">
                    @method('delete')
                    @csrf
                    <div class="modal-body">
                        <p>{{ __('products.messages.delete_confirmation') }}</p>
                        <input class="form-control" id="delete_product_name" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('products.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('products.actions.confirm') }}</button>
                    </div>
                </form>
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

    <script>
        $('#editProductModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);

            $('#edit_product_name').val(button.data('name'));
            $('#edit_organization_id').val(button.data('organization-id')).trigger('change');
            $('#edit_description').val(button.data('description'));
            $('#edit_commission_rate').val(button.data('commission-rate'));
            $('#edit_product_form').attr('action', button.data('update-url'));
        });

        $('#deleteProductModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);

            $('#delete_product_name').val(button.data('name'));
            $('#delete_product_form').attr('action', button.data('delete-url'));
        });
    </script>
@endsection
