<?php

namespace Modules\Transaksi\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Backend\BackendBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Transaksi\Entities\Transaksi;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends BackendBaseController
{
    use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'Transaksi';

        // module name
        $this->module_name = 'transaksi';

        // directory path of the module
        $this->module_path = 'transaksi::backend';

        // module icon
        $this->module_icon = 'fas fa-exchange-alt';

        // module model name, path
        $this->module_model = "Modules\Transaksi\Entities\Transaksi";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_action = 'Store';

        // VALIDASI YANG BENAR UNTUK MULTIPLE DETAIL!
        $request->validate([
            'jenis'                 => 'required|in:masuk,keluar',
            'tanggal'               => 'required|date',
            'keterangan'            => 'nullable|string|max:1000',
            
            // VALIDASI UNTUK SEMUA DETAIL BARANG
            'details'               => 'required|array|min:1',
            'details.*.barang_id'   => 'required|exists:barangs,id',
            'details.*.gudang_id'   => 'required|exists:gudangs,id',
            'details.*.jumlah'      => 'required|integer|min:1',
        ]);

        // Mulai transaksi database
        \DB::beginTransaction();
        try {
            // Generate kode transaksi otomatis
            $last = Transaksi::latest()->first();
            $nomor = $last ? ((int)substr($last->kode_transaksi, -4)) + 1 : 1;
            $kode = 'TRX-' . date('Y') . '-' .str_pad($nomor, 4, '0', STR_PAD_LEFT);

            // Buat header transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kode,
                'jenis'          => $request->jenis,
                'tanggal'        => $request->tanggal,
                'keterangan'     => $request->keterangan,
                'user_id'        => auth()->id(),
            ]);

            // Simpan semua detail
            foreach ($request->details as $detail) {
                $transaksi->details()->create([
                    'barang_id'   => $detail['barang_id'],
                    'gudang_id'   => $detail['gudang_id'],
                    'jumlah'      => $detail['jumlah'],
                    'harga_satuan'=> 0, // bisa ditambah nanti
                ]);

                // Update stok (jika perlu)
                $barang = \Modules\Barang\Entities\Barang::find($detail['barang_id']);
                $gudang = \Modules\Gudang\Entities\Gudang::find($detail['gudang_id']);

                if ($request->jenis == 'masuk') {
                    $barang->increment('stok', $detail['jumlah']);
                } else {
                    if ($barang->stok < $detail['jumlah']) {
                        throw new \Exception("Stok {$barang->nama_barang} tidak mencukupi!");
                    }
                    $barang->decrement('stok', $detail['jumlah']);
                }
            }

            \DB::commit();

            flash("Transaksi {$kode} berhasil disimpan!")->success()->important();

            return redirect()->route('backend.transaksi.index');

        } catch (\Exception $e) {
            \DB::rollback();
            flash('Gagal: ' . $e->getMessage())->error()->important();
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Show';

        $$module_name_singular = $module_model::findOrFail($id);

        $posts = $$module_name_singular->posts()->latest()->paginate();

        logUserAccess($module_title.' '.$module_action. " | Id: ". $$module_name_singular->id);

        return view(
            "$module_path.$module_name.show",
            compact('module_title', 'module_name', 'module_icon', 'module_name_singular', 'module_action', "$module_name_singular", 'posts')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'Update';

        $validatedData = $request->validate([
            'jenis'      => 'required|in:masuk,keluar',
            'barang_id'  => 'required|exists:barangs,id',
            'gudang_id'  => 'required|exists:gudangs,id',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $$module_name_singular = $module_model::findOrFail($id);

        $$module_name_singular->update($request->except('image', 'image_remove'));

        // Image
        if ($request->hasFile('image')) {
            if ($$module_name_singular->getMedia($module_name)->first()) {
                $$module_name_singular->getMedia($module_name)->first()->delete();
            }
            $media = $$module_name_singular->addMedia($request->file('image'))->toMediaCollection($module_name);

            $$module_name_singular->image = $media->getUrl();

            $$module_name_singular->save();
        }
        if ($request->image_remove == 'image_remove') {
            if ($$module_name_singular->getMedia($module_name)->first()) {
                $$module_name_singular->getMedia($module_name)->first()->delete();

                $$module_name_singular->image = '';

                $$module_name_singular->save();
            }
        }

        flash(icon()." ".Str::singular($module_title)."' Updated Successfully")->success()->important();

        logUserAccess($module_title.' '.$module_action. " | Id: ". $$module_name_singular->id);

        return redirect()->back();
    }
    public function datatable()
    {
        $query = Transaksi::with(['user', 'details' => function($q) {
            $q->select('transaksi_id', 'barang_id', 'gudang_id', 'jumlah');
        }, 'details.barang', 'details.gudang'])
        ->select('transaksis.*');

        return DataTables::of($query)
            ->addColumn('total_item', function ($row) {
                return $row->details->sum('jumlah');
            })
            ->addColumn('barang_list', function ($row) {
                return $row->details->pluck('barang.nama_barang')->implode(', ');
            })
            ->addColumn('user', function ($row) {
                return $row->user->name ?? '-';
            })
            ->editColumn('jenis', function ($row) {
                $badge = $row->jenis == 'masuk' 
                    ? '<span class="badge badge-success">MASUK</span>' 
                    : '<span class="badge badge-danger">KELUAR</span>';
                return $badge;
            })
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal->format('d/m/Y');
            })
            ->addColumn('action', function ($row) {
                return '<a href="'.route('backend.transaksi.show', $row->id).'" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ' .
                    '<a href="'.route('backend.transaksi.edit', $row->id).'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ' .
                    '<form action="'.route('backend.transaksi.destroy', $row->id).'" method="POST" style="display:inline">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus?\')"><i class="fas fa-trash"></i></button>
                        </form>';
            })
            ->rawColumns(['jenis', 'action'])
            ->make(true);
    }
}
