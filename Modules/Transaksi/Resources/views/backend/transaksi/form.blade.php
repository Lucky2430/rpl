{{-- HEADER TRANSAKSI --}}
<div class="row">
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
<h5 class="mt-4 mb-3">Detail Barang</h5>

<div id="detail-container">
    <div class="row detail-row mb-3 align-items-end">
        <div class="col-12 col-md-5">
            <div class="form-group">
                <label class="font-weight-bold">Barang <span class="text-danger">*</span></label>
                <select name="details[0][barang_id]" class="form-control select2-barang" required>
                    <option value="">— Pilih Barang —</option>
                    @foreach(\Modules\Barang\Entities\Barang::active()->with('gudang')->get() as $barang)
                        <option value="{{ $barang->id }}"
                                {{-- replaced PHP 8 null-safe operator to support PHP < 8 --}}
                                data-gudang="{{ optional($barang->gudang)->nama_gudang ?? 'Tidak ada gudang' }}"
                                data-stok="{{ $barang->stok }}">
                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                        </option>
                    @endforeach
                </select>
                @error('details.0.barang_id') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="form-group">
                <label class="font-weight-bold">Gudang (Otomatis)</label>
                <input type="text" class="form-control gudang-display" readonly placeholder="Gudang akan muncul otomatis">
            </div>
        </div>

        <div class="col-12 col-md-2">
            <div class="form-group">
                <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                <input type="number" name="details[0][jumlah]" class="form-control" min="1" required>
                @error('details.0.jumlah') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="col-12 col-md-1">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// DATA BARANG (dengan gudang) — SUDAH AMAN PAKAI function()
const barangData = @json(
    \Modules\Barang\Entities\Barang::active()
        ->with('gudang')
        ->get()
        ->map(function($b) {
            return [
                'id' => $b->id,
                'text' => $b->kode_barang . ' - ' . $b->nama_barang . ' (Stok: ' . $b->stok . ')',
                // replaced PHP 8 null-safe operator to support PHP < 8
                'gudang' => optional($b->gudang)->nama_gudang ?? 'Tidak ada gudang'
            ];
        })
);

let index = 1;

function initSelect2($row) {
    let $select = $row.find('.select2-barang');
    $select.select2({
        width: '100%',
        data: barangData,
        placeholder: "Pilih Barang",
        allowClear: true
    });

    // Saat barang dipilih → gudang otomatis muncul
    $select.on('change', function() {
        let selected = barangData.find(b => b.id == $(this).val());
        let gudangText = selected ? selected.gudang : '';
        $row.find('.gudang-display').val(gudangText);
    });
}

$('#tambah-barang').click(function() {
    let row = `
        <div class="row detail-row mb-3 align-items-end">
            <div class="col-12 col-md-5">
                <div class="form-group">
                    <label class="font-weight-bold">Barang <span class="text-danger">*</span></label>
                    <select name="details[${index}][barang_id]" class="form-control select2-barang" required>
                        <option value="">— Pilih Barang —</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold">Gudang (Otomatis)</label>
                    <input type="text" class="form-control gudang-display" readonly>
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label class="font-weight-bold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="details[${index}][jumlah]" class="form-control" min="1" required>
                </div>
            </div>
            <div class="col-12 col-md-1">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm btn-block remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;

    let $newRow = $(row);
    $('#detail-container').append($newRow);
    initSelect2($newRow);
    index++;
});

$(document).on('click', '.remove-row', function() {
    if ($('.detail-row').length > 1) {
        $(this).closest('.detail-row').remove();
    } 
    // else {
    //     alert('Minimal 1 barang!');
    // }
});

$(document).ready(function() {
    initSelect2($('#detail-container'));
});
</script>
@endpush