<?php

namespace App\Http\Controllers;

use App\Models\RekeningBkm;
use App\Models\RekeningRaab;
use App\Models\SubrekeningBkm;
use App\Models\SubrekeningRaab;
use App\Models\ItemBkm;
use App\Models\PenyaluranPembayaranBkm;
use App\Models\PenyaluranPembayaranRaab;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    private function catatLog($action, $description)
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    // --- 1. REKENING BKM ---
    public function rekeningBkmIndex(Request $request)
    {
        $query = RekeningBkm::query();
        if ($request->filled('search')) {
            $query->where('nama_rekening', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_rekening', 'like', '%' . $request->search . '%');
        }
        $rekenings = $query->paginate(15)->withQueryString();
        return view('master.rekening_bkm', compact('rekenings'));
    }

    public function storeRekeningBkm(Request $request)
    {
        $request->validate([
            'nomer_rekening' => 'required|string|unique:rekening_bkm,nomer_rekening',
            'nama_rekening' => 'required|string',
        ]);

        $rek = RekeningBkm::create($request->only('nomer_rekening', 'nama_rekening'));
        $this->catatLog('Tambah Master Rekening BKM', 'Menambahkan Rekening BKM: ' . $rek->nomer_rekening . ' - ' . $rek->nama_rekening);

        return redirect()->back()->with('success', 'Rekening BKM berhasil ditambahkan!');
    }

    public function updateRekeningBkm(Request $request, RekeningBkm $rekeningBkm)
    {
        $request->validate([
            'nomer_rekening' => 'required|string|unique:rekening_bkm,nomer_rekening,' . $rekeningBkm->id,
            'nama_rekening' => 'required|string',
        ]);

        $rekeningBkm->update($request->only('nomer_rekening', 'nama_rekening'));
        $this->catatLog('Ubah Master Rekening BKM', 'Mengubah Rekening BKM: ' . $rekeningBkm->nomer_rekening . ' - ' . $rekeningBkm->nama_rekening);

        return redirect()->back()->with('success', 'Rekening BKM berhasil diperbarui!');
    }

    public function destroyRekeningBkm(RekeningBkm $rekeningBkm)
    {
        $info = $rekeningBkm->nomer_rekening . ' - ' . $rekeningBkm->nama_rekening;
        $rekeningBkm->delete();
        $this->catatLog('Hapus Master Rekening BKM', 'Menghapus Rekening BKM: ' . $info);

        return redirect()->back()->with('success', 'Rekening BKM berhasil dihapus!');
    }

    // --- 2. REKENING RAAB ---
    public function rekeningRaabIndex(Request $request)
    {
        $query = RekeningRaab::query();
        if ($request->filled('search')) {
            $query->where('nama_rekening', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_rekening', 'like', '%' . $request->search . '%');
        }
        $rekenings = $query->paginate(15)->withQueryString();
        return view('master.rekening_raab', compact('rekenings'));
    }

    public function storeRekeningRaab(Request $request)
    {
        $request->validate([
            'nomer_rekening' => 'required|string|unique:rekening_raab,nomer_rekening',
            'nama_rekening' => 'required|string',
        ]);

        $rek = RekeningRaab::create($request->only('nomer_rekening', 'nama_rekening'));
        $this->catatLog('Tambah Master Rekening RAAB', 'Menambahkan Rekening RAAB: ' . $rek->nomer_rekening . ' - ' . $rek->nama_rekening);

        return redirect()->back()->with('success', 'Rekening RAAB berhasil ditambahkan!');
    }

    public function updateRekeningRaab(Request $request, RekeningRaab $rekeningRaab)
    {
        $request->validate([
            'nomer_rekening' => 'required|string|unique:rekening_raab,nomer_rekening,' . $rekeningRaab->id,
            'nama_rekening' => 'required|string',
        ]);

        $rekeningRaab->update($request->only('nomer_rekening', 'nama_rekening'));
        $this->catatLog('Ubah Master Rekening RAAB', 'Mengubah Rekening RAAB: ' . $rekeningRaab->nomer_rekening . ' - ' . $rekeningRaab->nama_rekening);

        return redirect()->back()->with('success', 'Rekening RAAB berhasil diperbarui!');
    }

    public function destroyRekeningRaab(RekeningRaab $rekeningRaab)
    {
        $info = $rekeningRaab->nomer_rekening . ' - ' . $rekeningRaab->nama_rekening;
        $rekeningRaab->delete();
        $this->catatLog('Hapus Master Rekening RAAB', 'Menghapus Rekening RAAB: ' . $info);

        return redirect()->back()->with('success', 'Rekening RAAB berhasil dihapus!');
    }

    // --- 3. SUBREKENING BKM ---
    public function subrekeningBkmIndex(Request $request)
    {
        $query = SubrekeningBkm::query();
        if ($request->filled('search')) {
            $query->where('nama_subrekening', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_subrekening', 'like', '%' . $request->search . '%');
        }
        $subrekenings = $query->paginate(15)->withQueryString();
        return view('master.subrekening_bkm', compact('subrekenings'));
    }

    public function storeSubrekeningBkm(Request $request)
    {
        $request->validate([
            'nomer_subrekening' => 'required|string|unique:subrekening_bkm,nomer_subrekening',
            'nama_subrekening' => 'required|string',
        ]);

        $sub = SubrekeningBkm::create($request->only('nomer_subrekening', 'nama_subrekening'));
        $this->catatLog('Tambah Master Subrekening BKM', 'Menambahkan Subrekening BKM: ' . $sub->nomer_subrekening . ' - ' . $sub->nama_subrekening);

        return redirect()->back()->with('success', 'Subrekening BKM berhasil ditambahkan!');
    }

    public function updateSubrekeningBkm(Request $request, SubrekeningBkm $subrekeningBkm)
    {
        $request->validate([
            'nomer_subrekening' => 'required|string|unique:subrekening_bkm,nomer_subrekening,' . $subrekeningBkm->id,
            'nama_subrekening' => 'required|string',
        ]);

        $subrekeningBkm->update($request->only('nomer_subrekening', 'nama_subrekening'));
        $this->catatLog('Ubah Master Subrekening BKM', 'Mengubah Subrekening BKM: ' . $subrekeningBkm->nomer_subrekening . ' - ' . $subrekeningBkm->nama_subrekening);

        return redirect()->back()->with('success', 'Subrekening BKM berhasil diperbarui!');
    }

    public function destroySubrekeningBkm(SubrekeningBkm $subrekeningBkm)
    {
        $info = $subrekeningBkm->nomer_subrekening . ' - ' . $subrekeningBkm->nama_subrekening;
        $subrekeningBkm->delete();
        $this->catatLog('Hapus Master Subrekening BKM', 'Menghapus Subrekening BKM: ' . $info);

        return redirect()->back()->with('success', 'Subrekening BKM berhasil dihapus!');
    }

    // --- 4. SUBREKENING RAAB ---
    public function subrekeningRaabIndex(Request $request)
    {
        $query = SubrekeningRaab::query();
        if ($request->filled('search')) {
            $query->where('nama_subrekening', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_subrekening', 'like', '%' . $request->search . '%');
        }
        $subrekenings = $query->paginate(15)->withQueryString();
        return view('master.subrekening_raab', compact('subrekenings'));
    }

    public function storeSubrekeningRaab(Request $request)
    {
        $request->validate([
            'nomer_subrekening' => 'required|string|unique:subrekening_raab,nomer_subrekening',
            'nama_subrekening' => 'required|string',
        ]);

        $sub = SubrekeningRaab::create($request->only('nomer_subrekening', 'nama_subrekening'));
        $this->catatLog('Tambah Master Subrekening RAAB', 'Menambahkan Subrekening RAAB: ' . $sub->nomer_subrekening . ' - ' . $sub->nama_subrekening);

        return redirect()->back()->with('success', 'Subrekening RAAB berhasil ditambahkan!');
    }

    public function updateSubrekeningRaab(Request $request, SubrekeningRaab $subrekeningRaab)
    {
        $request->validate([
            'nomer_subrekening' => 'required|string|unique:subrekening_raab,nomer_subrekening,' . $subrekeningRaab->id,
            'nama_subrekening' => 'required|string',
        ]);

        $subrekeningRaab->update($request->only('nomer_subrekening', 'nama_subrekening'));
        $this->catatLog('Ubah Master Subrekening RAAB', 'Mengubah Subrekening RAAB: ' . $subrekeningRaab->nomer_subrekening . ' - ' . $subrekeningRaab->nama_subrekening);

        return redirect()->back()->with('success', 'Subrekening RAAB berhasil diperbarui!');
    }

    public function destroySubrekeningRaab(SubrekeningRaab $subrekeningRaab)
    {
        $info = $subrekeningRaab->nomer_subrekening . ' - ' . $subrekeningRaab->nama_subrekening;
        $subrekeningRaab->delete();
        $this->catatLog('Hapus Master Subrekening RAAB', 'Menghapus Subrekening RAAB: ' . $info);

        return redirect()->back()->with('success', 'Subrekening RAAB berhasil dihapus!');
    }

    // --- 5. ITEM BKM ---
    public function itemBkmIndex(Request $request)
    {
        $query = ItemBkm::query();
        if ($request->filled('search')) {
            $query->where('nama_item', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_item', 'like', '%' . $request->search . '%');
        }
        $items = $query->paginate(15)->withQueryString();
        return view('master.item_bkm', compact('items'));
    }

    public function storeItemBkm(Request $request)
    {
        $request->validate([
            'nomer_item' => 'required|string|unique:item_bkm,nomer_item',
            'nama_item' => 'required|string',
        ]);

        $itm = ItemBkm::create($request->only('nomer_item', 'nama_item'));
        $this->catatLog('Tambah Master Item BKM', 'Menambahkan Item BKM: ' . $itm->nomer_item . ' - ' . $itm->nama_item);

        return redirect()->back()->with('success', 'Item BKM berhasil ditambahkan!');
    }

    public function updateItemBkm(Request $request, ItemBkm $itemBkm)
    {
        $request->validate([
            'nomer_item' => 'required|string|unique:item_bkm,nomer_item,' . $itemBkm->id,
            'nama_item' => 'required|string',
        ]);

        $itemBkm->update($request->only('nomer_item', 'nama_item'));
        $this->catatLog('Ubah Master Item BKM', 'Mengubah Item BKM: ' . $itemBkm->nomer_item . ' - ' . $itemBkm->nama_item);

        return redirect()->back()->with('success', 'Item BKM berhasil diperbarui!');
    }

    public function destroyItemBkm(ItemBkm $itemBkm)
    {
        $info = $itemBkm->nomer_item . ' - ' . $itemBkm->nama_item;
        $itemBkm->delete();
        $this->catatLog('Hapus Master Item BKM', 'Menghapus Item BKM: ' . $info);

        return redirect()->back()->with('success', 'Item BKM berhasil dihapus!');
    }

    // --- 6. PENYALURAN PEMBAYARAN BKM ---
    public function penyaluranPembayaranBkmIndex(Request $request)
    {
        $query = PenyaluranPembayaranBkm::query();
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('bank', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_rekening', 'like', '%' . $request->search . '%');
        }
        $penyalurans = $query->paginate(15)->withQueryString();
        return view('master.penyaluran_pembayaran_bkm', compact('penyalurans'));
    }

    public function storePenyaluranPembayaranBkm(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'nomer_rekening' => 'required|string',
            'bank' => 'required|string',
        ]);

        $pen = PenyaluranPembayaranBkm::create($request->only('nama', 'deskripsi', 'nomer_rekening', 'bank'));
        $this->catatLog('Tambah Master Penyaluran Pembayaran BKM', 'Menambahkan Penyaluran Pembayaran BKM: ' . $pen->nama . ' (' . $pen->bank . ' ' . $pen->nomer_rekening . ')');

        return redirect()->back()->with('success', 'Penyaluran Pembayaran BKM berhasil ditambahkan!');
    }

    public function updatePenyaluranPembayaranBkm(Request $request, PenyaluranPembayaranBkm $penyaluranBkm)
    {
        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'nomer_rekening' => 'required|string',
            'bank' => 'required|string',
        ]);

        $penyaluranBkm->update($request->only('nama', 'deskripsi', 'nomer_rekening', 'bank'));
        $this->catatLog('Ubah Master Penyaluran Pembayaran BKM', 'Mengubah Penyaluran Pembayaran BKM: ' . $penyaluranBkm->nama);

        return redirect()->back()->with('success', 'Penyaluran Pembayaran BKM berhasil diperbarui!');
    }

    public function destroyPenyaluranPembayaranBkm(PenyaluranPembayaranBkm $penyaluranBkm)
    {
        $info = $penyaluranBkm->nama . ' (' . $penyaluranBkm->bank . ' ' . $penyaluranBkm->nomer_rekening . ')';
        $penyaluranBkm->delete();
        $this->catatLog('Hapus Master Penyaluran Pembayaran BKM', 'Menghapus Penyaluran Pembayaran BKM: ' . $info);

        return redirect()->back()->with('success', 'Penyaluran Pembayaran BKM berhasil dihapus!');
    }

    // --- 7. PENYALURAN PEMBAYARAN RAAB ---
    public function penyaluranPembayaranRaabIndex(Request $request)
    {
        $query = PenyaluranPembayaranRaab::query();
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('bank', 'like', '%' . $request->search . '%')
                  ->orWhere('nomer_rekening', 'like', '%' . $request->search . '%');
        }
        $penyalurans = $query->paginate(15)->withQueryString();
        return view('master.penyaluran_pembayaran_raab', compact('penyalurans'));
    }

    public function storePenyaluranPembayaranRaab(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'nomer_rekening' => 'required|string',
            'bank' => 'required|string',
        ]);

        $pen = PenyaluranPembayaranRaab::create($request->only('nama', 'deskripsi', 'nomer_rekening', 'bank'));
        $this->catatLog('Tambah Master Penyaluran Pembayaran RAAB', 'Menambahkan Penyaluran Pembayaran RAAB: ' . $pen->nama . ' (' . $pen->bank . ' ' . $pen->nomer_rekening . ')');

        return redirect()->back()->with('success', 'Penyaluran Pembayaran RAAB berhasil ditambahkan!');
    }

    public function updatePenyaluranPembayaranRaab(Request $request, PenyaluranPembayaranRaab $penyaluranRaab)
    {
        $request->validate([
            'nama' => 'required|string',
            'deskripsi' => 'nullable|string',
            'nomer_rekening' => 'required|string',
            'bank' => 'required|string',
        ]);

        $penyaluranRaab->update($request->only('nama', 'deskripsi', 'nomer_rekening', 'bank'));
        $this->catatLog('Ubah Master Penyaluran Pembayaran RAAB', 'Mengubah Penyaluran Pembayaran RAAB: ' . $penyaluranRaab->nama);

        return redirect()->back()->with('success', 'Penyaluran Pembayaran RAAB berhasil diperbarui!');
    }

    public function destroyPenyaluranPembayaranRaab(PenyaluranPembayaranRaab $penyaluranRaab)
    {
        $info = $penyaluranRaab->nama . ' (' . $penyaluranRaab->bank . ' ' . $penyaluranRaab->nomer_rekening . ')';
        $penyaluranRaab->delete();
        $this->catatLog('Hapus Master Penyaluran Pembayaran RAAB', 'Menghapus Penyaluran Pembayaran RAAB: ' . $info);

        return redirect()->back()->with('success', 'Penyaluran Pembayaran RAAB berhasil dihapus!');
    }
}
