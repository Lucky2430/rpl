@extends('backend.layouts.app')
@section('title', 'Tambah Barang Baru')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4>Tambah Barang Baru</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('backend.barang.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" 
                               value="{{ old('nama_barang') }}" required autofocus>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Gudang Penyimpanan <span class="text-danger">*</span></label>
                        <select name="gudang_id" class="form-control select2 @error('gudang_id') is-invalid @enderror" required>
                            <option value="">— Pilih Gudang —</option>
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->id }}" {{ old('gudang_id') == $gudang->id ? 'selected' : '' }}>
                                    {{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}
                                </option>
                            @endforeach
                        </select>
                        @error('gudang_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" 
                               value="{{ old('stok', 0) }}" min="0" required>
                        @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" 
                               value="{{ old('harga', 0) }}" step="0.01">
                        @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Aktif</label>
                </div>
            </div>

            <hr>

            <button type="submit" class="btn btn-success btn-lg">
                Simpan Barang
            </button>
            <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary btn-lg ml-2">
                Kembali
            </a>
        </form>
    </div>
</div>
@endsection

@push('after-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%'
    });
});
</script>
@endpush