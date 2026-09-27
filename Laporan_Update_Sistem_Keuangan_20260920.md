# Laporan Pembaruan Sistem Keuangan BKM
**Tanggal Update:** 20 September 2026

Berikut adalah ringkasan dari seluruh pekerjaan yang berhasil dieksekusi dan diimplementasikan pada hari ini:

## 1. Poin 1: Modul SKM (Sewa Kendaraan Mandiri)
- **Database & Model:** Telah dibuat tabel `pengajuandana_skm` dan `form_ajukan_dana_skm` lengkap beserta _Model_ dan _Controller_-nya.
- **Logika Potong Kas:** Penarikan / pemotongan saldo kas hanya akan dieksekusi **saat Bukti Transfer diunggah** (status _Approved All_), bukan saat pengajuan dibuat.
- **Input Fleksibel:** Pada pop-up upload bukti, sistem kini meminta input **Tanggal Pembayaran** dan pilihan manual untuk memotong **Kas BKM** atau **Kas RAAB** (melalui _Combobox_). Transaksi akan tercatat di buku besar berdasarkan tanggal tersebut.

## 2. Poin 2: Logistik (Pembelian & Kebutuhan Langsung)
- Pada pop-up _upload_ bukti transfer di modul **Pembelian Barang** dan **Kebutuhan Langsung**, telah ditambahkan _combobox_ untuk memilih tujuan pemotongan kas (**Kas BKM** atau **Kas RAAB**).
- Ditambahkan _field_ **Kode Rekening** dan **Subrekening** agar bisa masuk ke laporan buku kas dengan rapi dan spesifik secara manual.

## 3. Poin 3: Sistem Approval Berjenjang
- Aturan _approval_ diseragamkan di seluruh modul pengajuan dana:
  - Nominal **di bawah atau sama dengan Rp 2.000.000**: Hanya memerlukan _approval_ dari Project Manager (PM).
  - Nominal **di atas Rp 2.000.000**: Memerlukan _approval_ berjenjang. PM harus melakukan _approve_ terlebih dahulu, barulah tiket pengajuan diteruskan ke Direktur (Bpk. Andreas) untuk _approve_ final.

## 4. Poin 4: Modul Gaji
- **Struktur Halaman:** Halaman dan fitur Gaji berhasil ditambahkan. Cara kerjanya sama persis dengan Modul SKM.
- **Automasi Potong Kas BKM:** Berbeda dengan SKM yang bisa memilih kas, Pengajuan Gaji akan **otomatis memotong dari Kas BKM**. Input tanggal pembayaran juga tetap diminta di pop-up unggah bukti transfer.

## 5. Poin 5: Automasi Perhitungan Saldo Kas
- Perhitungan saldo (BKM & RAAB) sekarang berjalan independen baris-demi-baris:
  - `Saldo = Saldo Sebelumnya + Debet - Kredit`.
  - Logika perhitungan dari Excel sudah diterapkan dengan benar pada `AutomasiController` tanpa terpengaruh oleh _running total_ yang memecahkan baris di bawahnya jika ada baris yang terselip/terhapus.

---

### ⚠️ Tindakan Lanjutan (Action Required)
Karena modul **SKM** dan **Gaji** membutuhkan tabel baru di database MySQL Anda, Anda wajib menjalankan perintah migrasi tabel sebelum fitur tersebut bisa digunakan atau diuji. 
Silakan jalankan perintah ini di terminal VSCode Anda besok saat pengecekan:
```bash
php artisan migrate
```
