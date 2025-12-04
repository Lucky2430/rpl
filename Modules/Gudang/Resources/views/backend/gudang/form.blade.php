<div class="row">
    {{-- <div class="col-12 col-md-6">
        <div class="form-group">
            <?php
            $field_name = 'kode_gudang';
            $field_lable = label_case($field_name);
            $field_placeholder = 'Contoh: GDG-001';
            $required = "required";
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weight-bold') }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)
                ->placeholder($field_placeholder)
                ->class('form-control')
                ->attributes(["$required", "autofocus"])
                ->value(old('kode_gudang', isset($gudang) ? $gudang->kode_gudang : '')) }}
            @error('kode_gudang')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div> --}}

    <div class="col-12 col-md-6">
        <div class="form-group">
            <?php
            $field_name = 'nama_gudang';
            $field_lable = label_case($field_name);
            $field_placeholder = 'Nama gudang lengkap';
            $required = "required";
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weight-bold') }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)
                ->placeholder($field_placeholder)
                ->class('form-control')
                ->attributes(["$required"])
                ->value(old('nama_gudang', isset($gudang) ? $gudang->nama_gudang : '')) }}
            @error('nama_gudang')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?php
            $field_name = 'lokasi';
            $field_lable = label_case($field_name);
            $field_placeholder = 'Contoh: Jl. Raya Bogor No. 123, Jakarta';
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }}
            {{ html()->text($field_name)
                ->placeholder($field_placeholder)
                ->class('form-control')
                ->value(old('lokasi', isset($gudang) ? $gudang->lokasi : '')) }}
            @error('lokasi')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <?php
            $field_name = 'is_active';
            $field_lable = label_case('status');
            $required = "required";
            $select_options = [
                '1' => 'Aktif',
                '0' => 'Non Aktif'
            ];
            ?>
            {{ html()->label($field_lable, $field_name)->class('font-weighttif-bold') }} {!! fielf_required($required) !!}
            {{ html()->select($field_name, $select_options)
                ->class('form-control select2')
                ->attributes(["$required"])
                ->value(old('is_active', isset($gudang) ? $gudang->is_active : '1')) }}
        </div>
    </div>
</div>