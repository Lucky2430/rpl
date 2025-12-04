{{-- HEADER TRANSAKSI --}}
<div class="row">
    <div class="col-12 col-md-4">
        <div class="form-group">
            <?php
            $field_name = 'jenis';
            $field_lable = 'Jenis Transaksi';
            $required = "required";
            $select_options = [
                'masuk'  => 'Transaksi Masuk',
                'keluar' => 'Transaksi Keluar'
            ];
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weight-bold') }} {!! fielf_required($required) !!}
            {{ html()->select($field_name, $select_options)
                ->class('form-control')
                ->attributes(["$required"])
                ->value(old($field_name)) }}
            @error($field_name)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="form-group">
            <?php
            $field_name = 'tanggal';
            $field_lable = 'Tanggal Transaksi';
            $required = "required";
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weight-bold') }} {!! fielf_required($required) !!}
            {{ html()->date($field_name)
                ->class('form-control')
                ->attributes(["$required"])
                ->value(old($field_name, today()->format('Y-m-d'))) }}
            @error($field_name)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="form-group">
            <?php
            $field_name = 'keterangan';
            $field_lable = 'Keterangan';
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weight-bold') }}
            {{ html()->textarea($field_name)
                ->class('form-control')
                ->rows(2)
                ->placeholder('Opsional...')
                ->value(old($field_name)) }}
            @error($field_name)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<hr>
<h5 class="mt-4 mb-3"><i class="fas fa-boxes"></i> Detail Barang</h5>

<div id="detail-container">
    <!-- Baris Pertama -->
    <div class="row detail-row mb-3 align-items-end">
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label class="font-weight-bold">Barang <span class="text-danger">*</span></label>
                <select name="details[0][barang_id]" class="form-control select2-barang" required>
                    <option value="">— Pilih Barang —</option>
                    @foreach(\Modules\Barang\Entities\Barang::active()->orderBy('kode_barang')->get() as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                    @endforeach
                </select>
                @error('details.0.barang_id') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="form-group">
                <label class="font-weight-bold">Gudang <span class="text-danger">*</span></label>
                <select name="details[0][gudang_id]" class="form-control select2-gudang" required>
                    <option value="">— Pilih Gudang —</option>
                    @foreach(\Modules\Gudang\Entities\Gudang::active()->orderBy('kode_gudang')->get() as $gudang)
                        <option value="{{ $gudang->id }}">{{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}</option>
                    @endforeach
                </select>
                @error('details.0.gudang_id') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-12 col-md-2">
            <div class="form-group">
                <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="details[0][jumlah]" class="form-control" min="1" required>
                @error('details.0.jumlah') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-12 col-md-2">
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm btn-block remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 mb-4">
    <div class="col">
        <button type="button" id="tambah-barang" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Barang
        </button>
    </div>
</div>

@push('after-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('after-scripts')
<script>
let index = 1;

// Fungsi untuk init Select2 hanya pada baris yang baru ditambah
function initSelect2($row) {
    $row.find('.select2-barang').select2({
        width: '100%',
        placeholder: "Pilih Barang",
        allowClear: true
    });

    $row.find('.select2-gudang').select2({
        width: '100%',
        placeholder: "Pilih Gudang",
        allowClear: true
    });
}

$('#tambah-barang').click(function() {
    let rowHtml = `
        <div class="row detail-row mb-3 align-items-end">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Barang <span class="text-danger">*</span></label>
                    <select name="details[${index}][barang_id]" class="form-control select2-barang" required>
                        <option value="">— Pilih Barang —</option>
                        @foreach(\Modules\Barang\Entities\Barang::active()->orderBy('kode_barang')->get() as $barang)
                            <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label class="font-weight-bold">Gudang <span class="text-danger">*</span></label>
                    <select name="details[${index}][gudang_id]" class="form-control select2-gudang" required>
                        <option value="">— Pilih Gudang —</option>
                        @foreach(\Modules\Gudang\Entities\Gudang::active()->orderBy('kode_gudang')->get() as $gudang)
                            <option value="{{ $gudang->id }}">{{ $gudang->kode_gudang }} - {{ $gudang->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="details[${index}][jumlah]" class="form-control" min="1" required value="">
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm btn-block remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;

    let $newRow = $(rowHtml);
    $('#detail-container').append($newRow);
    
    // INIT SELECT2 HANYA UNTUK BARIS BARU
    initSelect2($newRow);

    index++;
});

// Hapus baris
$(document).on('click', '.remove-row', function() {
    if ($('.detail-row').length > 1) {
        $(this).closest('.detail-row').remove();
    } else {
        alert('Minimal harus ada 1 barang!');
    }
});

// Init Select2 saat halaman pertama kali load
$(document).ready(function() {
    initSelect2($('#detail-container'));
});
</script>
@endpush