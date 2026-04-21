@extends('layouts.master')

@section('title')
    {{ __('organizations.page.title') }}
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
                <h4 class="content-title mb-0 my-auto">{{ __('organizations.page.settings') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('organizations.page.title') }}</span>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session('error') }}</strong>
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
                            data-toggle="modal" data-target="#createOrganizationModal">
                            <i class="las la-plus mr-1"></i>
                            {{ __('organizations.actions.add') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table key-buttons text-md-nowrap" style="text-align: center">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">{{ __('organizations.table.id') }}</th>
                                    <th class="border-bottom-0">{{ __('organizations.table.name') }}</th>
                                    <th class="border-bottom-0">{{ __('organizations.table.commission_rate') }}</th>
                                    <th class="border-bottom-0">{{ __('organizations.table.description') }}</th>
                                    <th class="border-bottom-0">{{ __('organizations.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($organizations as $organization)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $organization->name }}</td>
                                        <td>{{ number_format($organization->commission_rate, 2) }}%</td>
                                        <td>{{ $organization->description ?: '-' }}</td>
                                        <td class="text-nowrap">
                                            <div class="btn-group" role="group"
                                                aria-label="{{ __('organizations.table.actions') }}">
                                                <button class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                                    data-id="{{ $organization->id }}"
                                                    data-name="{{ $organization->name }}"
                                                    data-description="{{ $organization->description }}"
                                                    data-commission-rate="{{ $organization->commission_rate }}"
                                                    data-update-url="{{ route('organizations.update', $organization) }}"
                                                    data-toggle="modal" data-target="#editOrganizationModal"
                                                    title="{{ __('organizations.actions.edit') }}">
                                                    {{ __('organizations.actions.edit') }}
                                                </button>

                                                <button class="modal-effect btn btn-sm btn-danger"
                                                    data-effect="effect-scale" data-id="{{ $organization->id }}"
                                                    data-name="{{ $organization->name }}"
                                                    data-delete-url="{{ route('organizations.destroy', $organization) }}"
                                                    data-toggle="modal" data-target="#deleteOrganizationModal"
                                                    title="{{ __('organizations.actions.delete') }}">
                                                    {{ __('organizations.actions.delete') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="createOrganizationModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('organizations.actions.add') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('organizations.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="create_name">{{ __('organizations.form.name') }}</label>
                            <input type="text" class="form-control" id="create_name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="create_description">{{ __('organizations.form.description') }}</label>
                            <textarea class="form-control" id="create_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create_commission_rate">{{ __('organizations.form.commission_rate') }}</label>
                            <input type="number" class="form-control" id="create_commission_rate"
                                name="commission_rate" min="0" max="100" step="0.01" value="0">
                        </div>

                        <div class="modal-footer">
                            <button type="submit"
                                class="btn btn-success">{{ __('organizations.actions.confirm') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('organizations.actions.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editOrganizationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('organizations.actions.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_organization_form" method="post" autocomplete="off">
                        @method('patch')
                        @csrf
                        <div class="form-group">
                            <label for="edit_name">{{ __('organizations.form.name') }}</label>
                            <input class="form-control" name="name" id="edit_name" type="text" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">{{ __('organizations.form.description') }}</label>
                            <textarea class="form-control" id="edit_description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_commission_rate">{{ __('organizations.form.commission_rate') }}</label>
                            <input type="number" class="form-control" id="edit_commission_rate" name="commission_rate"
                                min="0" max="100" step="0.01">
                        </div>
                        <div class="modal-footer">
                            <button type="submit"
                                class="btn btn-primary">{{ __('organizations.actions.confirm') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('organizations.actions.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="deleteOrganizationModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ __('organizations.actions.delete') }}</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="delete_organization_form" method="post">
                    @method('delete')
                    @csrf
                    <div class="modal-body">
                        <p>{{ __('organizations.messages.delete_confirmation') }}</p>
                        <input class="form-control" name="name" id="delete_organization_name" type="text"
                            readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('organizations.actions.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('organizations.actions.confirm') }}</button>
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
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <script>
        $('#editOrganizationModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const name = button.data('name');
            const description = button.data('description');
            const commissionRate = button.data('commission-rate');
            const updateUrl = button.data('update-url');

            $('#edit_name').val(name);
            $('#edit_description').val(description);
            $('#edit_commission_rate').val(commissionRate);
            $('#edit_organization_form').attr('action', updateUrl);
        });

        $('#deleteOrganizationModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const name = button.data('name');
            const deleteUrl = button.data('delete-url');

            $('#delete_organization_name').val(name);
            $('#delete_organization_form').attr('action', deleteUrl);
        });
    </script>
@endsection
