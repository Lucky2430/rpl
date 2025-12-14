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
                            <th>Lokasi Gudang</th>
                            <th>Jumlah Item</th>
                            <th>Keterangan</th>
                            <th>Dibuat Oleh</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
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
        ajax: '{{ route("backend.transaksi.datatable") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
            { data: 'kode_transaksi', name: 'kode_transaksi' },
            { 
                data: 'tanggal', 
                render: data => new Date(data).toLocaleDateString('id-ID', { 
                    day: '2-digit', month: '2-digit', year: 'numeric' 
                })
            },
            { 
                data: 'jenis',
                render: data => data === 'masuk' 
                    ? '<span class="badge badge-success fw-bold">MASUK</span>' 
                    : '<span class="badge badge-danger fw-bold">KELUAR</span>'
            },
            { 
                data: 'details', 
                render: function(data) {
                    if (!data || data.length === 0) return '<em class="text-muted">Tidak ada barang</em>';
                    return data.map(d => 
                        d.barang?.nama_barang || '<em class="text-muted">Barang dihapus</em>'
                    ).join('<br>');
                },
                orderable: false
            },
            { 
                data: 'details', 
                render: function(data) {
                    if (!data || data.length === 0) return '-';
                    return data.map(d => 
                        d.barang?.gudang?.nama_gudang || '<em class="text-muted">Gudang dihapus</em>'
                    ).join('<br>');
                },
                orderable: false
            },
            { 
                data: 'details',
                render: data => {
                    if (!data) return '0';
                    let total = data.reduce((sum, d) => sum + parseInt(d.jumlah || 0), 0);
                    return '<strong>' + total.toLocaleString('id-ID') + '</strong>';
                },
                className: 'text-center'
            },
            { 
                data: 'keterangan', 
                render: data => data 
                    ? '<small>' + data.substring(0,50) + (data.length > 50 ? '...' : '') + '</small>' 
                    : '<em class="text-muted">Tidak ada</em>'
            },
            { data: 'user.name', name: 'user.name', defaultContent: '-' },
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
            emptyTable: "Belum ada transaksi",
            zeroRecords: "Tidak ditemukan transaksi yang cocok",
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