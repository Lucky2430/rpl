<?php

namespace Modules\Transaksi\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Barang\Entities\Barang;
use Modules\Gudang\Entities\Gudang;

class TransaksiController extends BackendBaseController
{
    public function __construct()
    {
        $this->module_title = 'Transaksi';
        $this->module_name  = 'transaksi';
        $this->module_path  = 'transaksi::backend';
        $this->module_icon  = 'fas fa-exchange-alt';
        $this->module_model = "Modules\Transaksi\Entities\Transaksi";
    }

    public function index()
    {
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_path  = $this->module_path;
        $module_icon  = $this->module_icon;
        $module_action = 'List';

        return view("{$module_path}.transaksi.index_datatable", compact(
            'module_title', 'module_name', 'module_path', 'module_icon', 'module_action'
        ));
    }

    public function create()
    {
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_path  = $this->module_path;
        $module_icon  = $this->module_icon;
        $module_action = 'Create';

        $barangs = Barang::active()->orderBy('nama_barang')->pluck('nama_barang', 'id');
        $gudangs = Gudang::active()->orderBy('nama_gudang')->pluck('nama_gudang', 'id');

        return view("{$module_path}.transaksi.create", compact(
            'module_title', 'module_name', 'module_icon', 'module_action', 'barangs', 'gudangs'
        ));
    }

    public function datatable()
    {
        $query = Transaksi::with(['barang', 'gudang', 'user'])->latest();

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group">';
                $btn .= '<button type="button" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i></button> ';
                $btn .= '<form action="'.route('backend.transaksi.destroy', $row->id).'" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Yakin hapus transaksi ini?\')">';
                $btn .= csrf_field().method_field('DELETE');
                $btn .= '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>';
                $btn .= '</form></div>';
                return $btn;
            })
            ->editColumn('jenis', function ($row) {
                return $row->jenis === 'masuk'
                    ? '<span class="badge badge-success">MASUK</span>'
                    : '<span class="badge badge-danger">KELUAR</span>';
            })
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->editColumn('jumlah', function ($row) {
                return number_format($row->jumlah, 0, ',', '.');
            })
            ->editColumn('barang.nama_barang', function ($row) {
                return $row->barang ? $row->barang->nama_barang : '-';
            })
            ->editColumn('gudang.nama_gudang', function ($row) {
                return $row->gudang ? $row->gudang->nama_gudang : '-';
            })
            ->editColumn('user.name', function ($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->rawColumns(['action', 'jenis'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_model = $this->module_model;
        $module_action = 'Store';

        $request->validate([
            'jenis'      => 'required|in:masuk,keluar',
            'barang_id'  => 'required|exists:barangs,id',
            'gudang_id'  => 'required|exists:gudangs,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $transaksi = $module_model::create($request->all());

        flash(icon()." Transaksi {$request->jenis} berhasil dicatat!")->success()->important();

        logUserAccess("{$module_title} {$module_action} | Kode: {$transaksi->kode_transaksi}");

        return redirect()->route("backend.{$module_name}.index");
    }

    public function destroy($id)
    {
        $module_title = $this->module_title;
        $module_model = $this->module_model;

        $transaksi = $module_model::findOrFail($id);
        $kode = $transaksi->kode_transaksi;
        $transaksi->delete();

        flash(icon()." Transaksi {$kode} berhasil dihapus")->success()->important();

        return redirect()->route('backend.transaksi.index');
    }
}