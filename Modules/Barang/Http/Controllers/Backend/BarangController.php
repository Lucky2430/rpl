<?php

namespace Modules\Barang\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Barang\Entities\Barang;

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
        $query = Barang::query();

        return DataTables::of($query)
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

        return view("{$this->module_path}.barang.create", compact(
            'module_title', 'module_name', 'module_icon', 'module_action'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan'      => 'required|string|max:50',
            'keterangan'  => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        Barang::create($request->only(['nama_barang', 'satuan', 'keterangan', 'is_active']));

        flash(icon()." Barang berhasil ditambahkan")->success()->important();
        return redirect()->route('backend.barang.index');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_icon  = $this->module_icon;
        $module_action = 'Edit';

        return view("{$this->module_path}.barang.edit", compact(
            'module_title', 'module_name', 'module_icon', 'module_action', 'barang'
        ));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'satuan'      => 'required|string|max:50',
            'keterangan'  => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $barang->update($request->only(['nama_barang', 'satuan', 'keterangan', 'is_active']));

        flash(icon()." Barang berhasil diperbarui")->success()->important();
        return redirect()->route('backend.barang.index');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        flash(icon()." Barang berhasil dihapus")->success()->important();
        return redirect()->route('backend.barang.index');
    }
}