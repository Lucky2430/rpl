<?php

namespace Modules\Gudang\Http\Controllers\Backend;

// use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Modules\Gudang\Entities\Gudang;
use App\Exports\GudangExport;
use Maatwebsite\Excel\Facades\Excel;

class GudangController extends BackendBaseController
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Gudang';

        // module name
        $this->module_name = 'gudang';

        // directory path of the module
        $this->module_path = 'gudang::backend';

        // module icon
        $this->module_icon = 'fas fa-warehouse';

        // module model name, path
        $this->module_model = "Modules\Gudang\Entities\Gudang";
    }

    // OVERRIDE: Tampilkan view DataTable khusus Gudang
    public function index()
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_action = 'List';

        return view("{$module_path}.gudang.index_datatable",
            compact('module_title', 'module_name', 'module_path', 'module_icon', 'module_action')
        );
    }

    // BARU: JSON untuk DataTables (ini yang dipanggil ajax)
    public function datatable()
    {
        $query = Gudang::query();

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $btn  = '<div class="btn-group">';
                $btn .= '<a href="'.route('backend.gudang.edit', $row->id).'" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a> ';
                $btn .= '<form action="'.route('backend.gudang.destroy', $row->id).'" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Yakin hapus gudang ini?\')">';
                $btn .= csrf_field().method_field('DELETE');
                $btn .= '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>';
                $btn .= '</form></div>';

                return $btn;
            })
            ->editColumn('is_active', function ($row) {
                return $row->is_active
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-danger">Non Aktif</span>';
            })
            ->editColumn('lokasi', fn($row) => $row->lokasi ?: '-')
            ->rawColumns(['action', 'is_active'])
            ->make(true);
    }

    // STORE – sudah kamu perbaiki, hanya diperhalus sedikit
    public function store(Request $request)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_model = $this->module_model;
        $module_action = 'Store';

        $request->validate([
            // 'kode_gudang' => 'required|unique:gudangs,kode_gudang',
            'nama_gudang' => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $gudang = $module_model::create($request->only([
            'kode_gudang', 'nama_gudang', 'lokasi', 'is_active'
        ]));

        flash(icon()." {$module_title} berhasil ditambahkan")->success()->important();

        logUserAccess("{$module_title} {$module_action} | Id: {$gudang->id}");

        return redirect()->route("backend.{$module_name}.index");
    }

    // UPDATE – sudah kamu perbaiki, hanya diperhalus
    public function update(Request $request, $id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_model = $this->module_model;
        $module_action = 'Update';

        $gudang = $module_model::findOrFail($id);

        $request->validate([
            // 'kode_gudang' => 'required|unique:gudangs,kode_gudang,'.$id,
            'nama_gudang' => 'required|string|max:255',
            'lokasi'      => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $gudang->update($request->only([
            'kode_gudang', 'nama_gudang', 'lokasi', 'is_active'
        ]));

        flash(icon()." {$module_title} berhasil diperbarui")->success()->important();

        logUserAccess("{$module_title} {$module_action} | Id: {$gudang->id}");

        return redirect()->route("backend.{$module_name}.index");
    }

    // Hapus (opsional, bisa pakai yang dari parent kalau mau)
    public function destroy($id)
    {
        $module_title = $this->module_title;
        $module_model = $this->module_model;

        $gudang = $module_model::findOrFail($id);
        $gudang->delete();

        flash(icon()." {$module_title} berhasil dihapus")->success()->important();

        return redirect()->route("backend.gudang.index");
    }

    public function exportCsv()
    {
        return Excel::download(new GudangExport, 'gudang-' . date('Y-m-d') . '.csv');
    }

    // Method lain yang tidak dipakai (show, trashed, dll) biarkan tetap atau hapus kalau mau
}