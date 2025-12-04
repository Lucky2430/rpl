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
                    <small class="text-muted">Riwayat Transaksi</small>
                </h4>
                <div class="small text-muted">
                    Transaksi Barang Masuk & Keluar
                </div>
            </div>
            <div class="col-4">
                <div class="float-right">
                    <x-buttons.create route='{{ route("backend.transaksi.create") }}' title="Transaksi Baru"/>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <table id="datatable" class="table table-bordered table-hover table-responsive-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Barang</th>
                            <th>Gudang</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Dibuat Oleh</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <small class="text-muted float-right">
            Transaksi • Diperbarui: {{ now()->format('d M Y H:i') }}
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
        ajax: '{{ route("backend.transaksi.datatable") }}',
        columns: [
            { data: 'id', name: 'id', width: '5%', className: 'text-center' },
            { data: 'kode_transaksi', name: 'kode_transaksi' },
            { 
                data: 'tanggal', 
                name: 'tanggal',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            { 
                data: 'jenis', 
                name: 'jenis',
                render: function(data) {
                    return data === 'masuk' 
                        ? '<span class="badge badge-success">MASUK</span>' 
                        : '<span class="badge badge-danger">KELUAR</span>';
                }
            },
            { data: 'barang.nama_barang', name: 'barang.nama_barang', defaultContent: '-' },
            { data: 'gudang.nama_gudang', name: 'gudang.nama_gudang', defaultContent: '-' },
            { 
                data: 'jumlah', 
                name: 'jumlah',
                className: 'text-right',
                render: function(data) {
                    return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            },
            { 
                data: 'keterangan', 
                name: 'keterangan', 
                defaultContent: '<em class="text-muted">Tidak ada</em>',
                render: function(data) {
                    return data ? '<small>' + data.substring(0, 50) + (data.length > 50 ? '...' : '') + '</small>' : '<em class="text-muted">Tidak ada</em>';
                }
            },
            { data: 'user.name', name: 'user.name', defaultContent: '-' },
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
            processing: "Memuat data transaksi...",
            emptyTable: "Belum ada transaksi",
            zeroRecords: "Tidak ditemukan transaksi yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi"
        }
    });
});
</script>
@endpush