@extends('backend.layouts.app')

@section('title') {{ $module_action }} {{ $module_title }} @endsection

@section('breadcrumbs')
    <x-backend-breadcrumbs>
        <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>
            {{ $module_title }}
        </x-backend-breadcrumb-item>
    </x-backend-breadcrumbs>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-8">
                <h4 class="card-title mb-0">
                    <i class="{{ $module_icon }}"></i> {{ $module_title }}
                    <small class="text-muted">Data Table {{ $module_action }}</small>
                </h4>
                <div class="small text-muted">
                    Master Data Gudang
                </div>
            </div>
            <div class="col-4">
                <div class="float-right">
                    <x-buttons.create route='{{ route("backend.$module_name.create") }}' title="{{__('Create')}} {{ ucwords(Str::singular($module_name)) }}"/>
                    {{-- <x-buttons.create route='{{ module_route("gudang.create") }}' title="Tambah Gudang" /> --}}

                    @if(config('modules.gudang.trashed_enabled', false))
                    <div class="btn-group" role="group">
                        <button id="btnGroupToolbar" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ module_route('gudang.trashed') }}">
                                <i class="fas fa-trash-alt"></i> Lihat Sampah
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Kode Gudang</th>
                            <th>Nama Gudang</th>
                            <th>Lokasi</th>
                            <th width="10%">Status</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <small class="text-muted float-right">
            Data Gudang • Diperbarui: {{ now()->format('d M Y H:i') }}
        </small>
    </div>
</div>
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

<script>
$(function () {
    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        // ajax: '{{ route("backend.$module_name.index_data") }}',
        ajax: '{{ route("backend.gudang.datatable") }}',
        columns: [
            { data: 'id', name: 'id', width: '5%', className: 'text-center' },
            { data: 'kode_gudang', name: 'kode_gudang' },
            { data: 'nama_gudang', name: 'nama_gudang' },
            { data: 'lokasi', name: 'lokasi', defaultContent: '-' },
            { 
                data: 'is_active', 
                name: 'is_active',
                render: function(data) {
                    return data ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Non Aktif</span>';
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: "Memuat...",
            emptyTable: "Belum ada data gudang",
            zeroRecords: "Tidak ditemukan data"
        }
    });
});
</script>
@endpush