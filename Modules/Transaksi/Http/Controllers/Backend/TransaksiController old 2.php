<?php

namespace Modules\Transaksi\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Modules\Transaksi\Entities\Transaksi;
use Modules\Transaksi\Entities\TransaksiDetail;
use Modules\Barang\Entities\Barang;
use DB;

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
        return view("{$this->module_path}.transaksi.index_datatable");
    }

    public function datatable()
    {
        $query = Transaksi::with(['user', 'details.barang.gudang']);

        return DataTables::of($query)
            ->addIndexColumn() // ini buat kolom #
            ->addColumn('kode_transaksi', fn($row) => $row->kode_transaksi)
            ->addColumn('total_item', fn($row) => $row->details->sum('jumlah'))
            ->addColumn('action', function ($row) {
                $btn = '<a href="'.route('backend.transaksi.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                return $btn;
            })
            ->editColumn('jenis', fn($row) => $row->jenis == 'masuk' ? '<span class="badge badge-success">MASUK</span>' : '<span class="badge badge-danger">KELUAR</span>')
            ->rawColumns(['action', 'jenis'])
            ->make(true);
    }

    public function create()
    {
        $module_title   = $this->module_title;
        $module_name    = $this->module_name;
        $module_path    = $this->module_path;
        $module_icon    = $this->module_icon;
        $module_action = 'Create'; // INI YANG KURANG!

        return view("{$module_path}.transaksi.create", compact(
            'module_title',
            'module_name',
            'module_path',
            'module_icon',
            'module_action'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:masuk,keluar',
            'tanggal' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|exists:barangs,id',
            'details.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'jenis' => $request->jenis,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->details as $item) {
                $barang = Barang::find($item['barang_id']);
                
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $item['jumlah'],
                ]);

                if ($request->jenis == 'keluar') {
                    if ($barang->stok < $item['jumlah']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak cukup!");
                    }
                    $barang->decrement('stok', $item['jumlah']);
                } else {
                    $barang->increment('stok', $item['jumlah']);
                }
            }

            DB::commit();
            flash("Transaksi {$transaksi->kode_transaksi} berhasil!")->success();
        } catch (\Exception $e) {
            DB::rollback();
            flash("Gagal: " . $e->getMessage())->error();
            return back()->withInput();
        }

        return redirect()->route('backend.transaksi.index');
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['user', 'details.barang.gudang'])->findOrFail($id);

        $module_title = $this->module_title;
        $module_name  = $this->module_name;
        $module_icon  = $this->module_icon;
        $module_action = 'Detail';

        return view("{$this->module_path}.transaksi.show", compact(
            'transaksi', 'module_title', 'module_name', 'module_icon', 'module_action'
        ));
    }
}