@extends('backend.layouts.app')

@section('title', 'Detail Transaksi #{{ $transaksi->kode_transaksi }}')

@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item route='{{ route("backend.transaksi.index") }}' icon='fas fa-exchange-alt'>
        Transaksi
    </x-backend-breadcrumb-item>
    <x-backend-breadcrumb-item type="active">
        {{ $transaksi->kode_transaksi }}
    </x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-gradient-info text-white">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt mr-2"></i> Detail Transaksi
                </h4>
            </div>
            <div class="col-auto">
                <span class="badge badge-light badge-pill font-size-18 px-4 py-2">
                    {{ $transaksi->kode_transaksi }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Info Utama Transaksi -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="35%">Kode Transaksi</th>
                        <td><strong>{{ $transaksi->kode_transaksi }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>
                            {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Transaksi</th>
                        <td>
                            @if($transaksi->jenis == 'masuk')
                                <span class="badge badge-success badge-pill px-4 py-2">MASUK</span>
                            @else
                                <span class="badge badge-danger badge-pill px-4 py-2">KELUAR</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $transaksi->user->name ?? 'Sistem' }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat Pada</th>
                        <td>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($transaksi->updated_at->gt($transaksi->created_at))
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ \Carbon\Carbon::parse($transaksi->updated_at)->diffForHumans() }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="col-lg-6">
                @if($transaksi->keterangan)
                <div class="alert alert-light border">
                    <strong>Keterangan:</strong>
                    <p class="mb-0 mt-2">{{ nl2br(e($transaksi->keterangan)) }}</p>
                </div>
                @else
                <div class="alert alert-secondary">
                    <em>Tidak ada keterangan</em>
                </div>
                @endif
            </div>
        </div>

        <hr class="my-5">

        <!-- Daftar Barang -->
        <h5 class="mb-4">
            <i class="fas fa-boxes mr-2"></i> Daftar Barang Transaksi
        </h5>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th>Barang</th>
                        <th>Lokasi Gudang</th>
                        <th width="15%" class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi->details as $index => $detail)
                        <tr>
                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                @if($detail->barang)
                                    <strong>{{ $detail->barang->nama_barang }}</strong>
                                    <br>
                                    <small class="text-muted">Kode: {{ $detail->barang->kode_barang }}</small>
                                @else
                                    <em class="text-muted">Barang dihapus</em>
                                @endif
                            </td>
                            <td>
                                @if($detail->barang && $detail->barang->gudang)
                                    <strong>{{ $detail->barang->gudang->nama_gudang }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $detail->barang->gudang->lokasi ?? '-' }}</small>
                                @else
                                    <em class="text-muted">Gudang dihapus atau tidak tersedia</em>
                                @endif
                            </td>
                            <td class="text-center font-weight-bold font-size-18">
                                {{ number_format($detail->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Tidak ada barang dalam transaksi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($transaksi->details->count() > 0)
                <tfoot class="table-info">
                    <tr>
                        <th colspan="3" class="text-right font-weight-bold">Total Item</th>
                        <th class="text-center font-weight-bold font-size-18">
                            {{ number_format($transaksi->details->sum('jumlah'), 0, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="mt-5">
            <a href="{{ route('backend.transaksi.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Transaksi
            </a>
        </div>
    </div>

    <div class="card-footer bg-light text-center text-muted">
        Transaksi dibuat pada {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d F Y H:i') }}
        @if($transaksi->updated_at->gt($transaksi->created_at))
            • Terakhir diperbarui {{ \Carbon\Carbon::parse($transaksi->updated_at)->diffForHumans() }}
        @endif
    </div>
</div>
@endsection