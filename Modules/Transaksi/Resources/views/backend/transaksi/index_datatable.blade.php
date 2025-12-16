@extends('backend.layouts.app')

@php
    $module_action = $module_action ?? 'Daftar';
    $module_title  = $module_title  ?? 'Transaksi';
    $module_icon   = $module_icon   ?? 'cil-list';
@endphp

@section('title') {{ $module_action }} {{ $module_title }} @endsection

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>
        {{ $module_title }}
    </x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-gradient-primary text-white">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="mb-0">
                    <i class="{{ $module_icon }}"></i> List Transaksi
                </h4>
                <small class="text-light">Catatan pengambilan barang dari gudang</small>
            </div>
            <div class="col-auto">
                <x-buttons.create route='{{ route("backend.transaksi.create") }}' title="Transaksi Baru"/>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="datatable" class="table table-bordered table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Lokasi Gudang</th>
                        <th class="text-center">Jumlah Item</th>
                        <th>Keterangan</th>
                        <th>Dibuat Oleh</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="card-footer bg-light">
        <small class="text-muted">
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
        ajax: '{{ route("backend.transaksi.datatable") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%', className: 'text-center' },
            { data: 'kode_transaksi', name: 'kode_transaksi' },
            { 
                data: 'tanggal', 
                render: data => new Date(data).toLocaleDateString('id-ID', {
                    day: '2-digit', month: '2-digit', year: 'numeric'
                })
            },
            { 
                data: 'details', 
                render: function(data) {
                    if (!data || data.length === 0) return '<em class="text-muted">Tidak ada barang</em>';
                    return data.map(d => 
                        d.barang?.nama_barang 
                            ? '<strong>' + d.barang.nama_barang + '</strong><br><small class="text-muted">Kode: ' + d.barang.kode_barang + '</small>'
                            : '<em class="text-muted">Barang dihapus</em>'
                    ).join('<hr class="my-1">');
                },
                orderable: false
            },
            { 
                data: 'details', 
                render: function(data) {
                    if (!data || data.length === 0) return '-';
                    return data.map(d => 
                        d.barang?.gudang?.nama_gudang 
                            ? '<strong>' + d.barang.gudang.nama_gudang + '</strong><br><small class="text-muted">' + (d.barang.gudang.lokasi || '-') + '</small>'
                            : '<em class="text-muted">Gudang dihapus</em>'
                    ).join('<hr class="my-1">');
                },
                orderable: false
            },
            { 
                data: 'details',
                render: data => {
                    if (!data) return '0';
                    let total = data.reduce((sum, d) => sum + parseInt(d.jumlah || 0), 0);
                    return '<strong class="text-danger">' + total.toLocaleString('id-ID') + '</strong>';
                },
                className: 'text-center font-weight-bold'
            },
            { 
                data: 'keterangan', 
                render: data => data 
                    ? '<small>' + data.substring(0, 50) + (data.length > 50 ? '...' : '') + '</small>'
                    : '<em class="text-muted">Tidak ada</em>'
            },
            { data: 'user.name', name: 'user.name', defaultContent: '<em class="text-muted">Sistem</em>' },
            { 
                data: 'action', 
                orderable: false, 
                searchable: false, 
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            processing: "Memuat data transaksi...",
            emptyTable: "Belum ada transaksi pengeluaran",
            zeroRecords: "Tidak ditemukan transaksi",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            }
        }
    });
});
</script>
@endpush