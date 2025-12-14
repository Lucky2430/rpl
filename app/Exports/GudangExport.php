<?php

namespace App\Exports;

use Modules\Gudang\Entities\Gudang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GudangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Gudang::select('kode_gudang', 'nama_gudang', 'lokasi', 'is_active')->get();
    }

    public function headings(): array
    {
        return ['Kode Gudang', 'Nama Gudang', 'Lokasi', 'Status Aktif'];
    }
}