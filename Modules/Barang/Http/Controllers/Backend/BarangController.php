<?php

namespace Modules\Barang\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Barang\Entities\Barang;
use Modules\Gudang\Entities\Gudang;
use Illuminate\Support\Str; // Added to support Str::limit

class BarangController extends BackendBaseController
{
    public function __construct()
    {
        $this->module_title = 'Barang';
        $this->module_name  = 'barang';
        $this->module_path  = 'barang::backend';
        $this->module_icon  = 'fas fa-boxes';
        $this->module_model = "Modules\Barang\Entities\Barang";
    }

    public function index()
    {
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_path  = $this->module_path;
        $module_icon  = $this->module_icon;
        $module_action = 'List';

        return view("{$module_path}.barang.index_datatable", compact(
            'module_title', 'module_name', 'module_path', 'module_icon', 'module_action'
        ));
    }

    public function datatable()
    {
        $query = Barang::with('gudang'); // TAMBAH RELASI GUDANG

        return DataTables::of($query)
            // Fixed: avoid PHP 8 null-safe operator (?->)
            ->addColumn('gudang', fn($row) => optional($row->gudang)->nama_gudang ?? '-')
            ->addColumn('harga_rp', fn($row) => 'Rp ' . number_format($row->harga, 0, ',', '.'))
            ->addColumn('action', function ($row) {
                $btn  = '<div class="btn-group">';
                $btn .= '<a href="'.route('backend.barang.edit', $row->id).'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
                $btn .= '<form action="'.route('backend.barang.destroy', $row->id).'" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Yakin hapus barang ini?\')">';
                $btn .= csrf_field().method_field('DELETE');
                $btn .= '<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>';
                $btn .= '</form></div>';
                return $btn;
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-danger">Non Aktif</span>';
            })
            ->editColumn('keterangan', fn($row) => $row->keterangan ? Str::limit($row->keterangan, 50) : '-')
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    public function create()
    {
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_icon  = $this->module_icon;
        $module_action = 'Create';

        // KIRIM DATA GUDANG KE VIEW!
        $gudangs = Gudang::active()->orderBy('nama_gudang')->get();

        return view("{$this->module_path}.barang.create", compact(
            'module_title', 'module_name', 'module_icon', 'module_action', 'gudangs'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'gudang_id'   => 'required|exists:gudangs,id',
            'stok'        => 'required|integer|min:0',
            'harga'       => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Barang::create([
            'nama_barang' => $request->nama_barang,
            'gudang_id'   => $request->gudang_id,
            'stok'        => $request->stok,
            'harga'       => $request->harga ?? 0,
            'keterangan'  => $request->keterangan,
            'is_active'   => $request->has('is_active') ? 1 : 0,
            // KODE OTOMATIS LANGSUNG DARI MODEL!
        ]);

        flash("Barang berhasil ditambahkan!")->success()->important();
        return redirect()->route('backend.barang.index');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $gudangs = Gudang::active()->orderBy('nama_gudang')->get();

        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_icon  = $this->module_icon;
        $module_action = 'Edit';

        return view("{$this->module_path}.barang.edit", compact(
            'module_title', 'module_name', 'module_icon', 'module_action', 'barang', 'gudangs'
        ));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'gudang_id'   => 'required|exists:gudangs,id',
            'stok'        => 'required|integer|min:0',
            'harga'       => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'gudang_id'   => $request->gudang_id,
            'stok'        => $request->stok,
            'harga'       => $request->harga ?? 0,
            'keterangan'  => $request->keterangan,
            'is_active'   => $request->has('is_active') ? 1 : 0,
        ]);

        flash("Barang berhasil diperbarui!")->success()->important();
        return redirect()->route('backend.barang.index');
    }

    public function destroy($id)
    {
        Barang::findOrFail($id)->delete();
        flash("Barang berhasil dihapus!")->success()->important();
        return redirect()->route('backend.barang.index');
    }
}