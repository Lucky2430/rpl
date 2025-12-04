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
                    Master Data Barang
                </div>
            </div>
            <div class="col-4">
                <div class="float-right">
                    <x-buttons.create route='{{ route("backend.barang.create") }}' title="Tambah Barang Baru"/>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                            <th width="10%">Status</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <small class="text-muted float-right">
            Data Barang • Diperbarui: {{ now()->format('d M Y H:i') }}
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
        ajax: '{{ route("backend.barang.datatable") }}',
        columns: [
            { data: 'id', name: 'id', width: '5%', className: 'text-center' },
            { data: 'kode_barang', name: 'kode_barang' },
            { data: 'nama_barang', name: 'nama_barang' },
            { data: 'satuan', name: 'satuan', defaultContent: 'pcs' },
            { 
                data: 'keterangan', 
                name: 'keterangan', 
                defaultContent: '<em class="text-muted">Tidak ada</em>',
                render: function(data) {
                    return data ? '<small>' + data.substring(0, 60) + (data.length > 60 ? '...' : '') + '</small>' : '<em class="text-muted">Tidak ada</em>';
                }
            },
            { 
                data: 'is_active', 
                name: 'is_active',
                render: function(data) {
                    return data 
                        ? '<span class="badge badge-success">Aktif</span>' 
                        : '<span class="badge badge-danger">Non Aktif</span>';
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
            processing: "Memuat data...",
            emptyTable: "Belum ada data barang",
            zeroRecords: "Tidak ditemukan data yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ barang"
        }
    });
});
</script>
@endpush