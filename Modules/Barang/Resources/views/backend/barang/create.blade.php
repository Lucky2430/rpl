@extends('backend.layouts.app')
@section('title', 'Tambah Barang Baru')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4><i class="fas fa-plus"></i> Tambah Barang Baru</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('backend.barang.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" required autofocus>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="satuan" class="form-control" value="pcs" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="3" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked> Aktif
                </label>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Barang
            </button>
            <a href="{{ route('backend.barang.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection