# Project Brief: Sistem Koperasi Lentera Hijau

## Deskripsi Proyek
Membangun sistem manajemen koperasi berbasis web menggunakan Laravel untuk Koperasi Lentera Hijau dengan fitur pengelolaan simpanan, tunggakan, pinjaman, kas, dan laporan laba rugi.

## Fitur Utama

### 1. Modul Simpanan
- Input data simpanan anggota
- Riwayat simpanan per anggota
- Total simpanan per anggota
- Laporan simpanan

### 2. Modul Tunggakan
- Pencatatan tunggakan pinjaman
- Tracking tunggakan per anggota
- Notifikasi tunggakan
- Laporan tunggakan

### 3. Modul Pinjaman
- Form pengajuan pinjaman dengan pilihan bunga:
  - Harian: 20%, 25%, 30%, 35%, 40%, 45%, 50%
  - Mingguan: 20%, 25%, 30%, 35%, 40%, 45%, 50%
- Perhitungan otomatis bunga berdasarkan jenis dan persentase yang dipilih
- Jadwal angsuran otomatis
- Tracking pembayaran angsuran
- Status pinjaman (aktif, lunas, menunggak)
- Riwayat pinjaman per anggota

### 4. Modul Kas
- Input pemasukan kas
- Input pengeluaran kas
- Saldo kas real-time
- Mutasi kas harian/bulanan
- Kategori pemasukan dan pengeluaran

### 5. Modul Laba Rugi
- Perhitungan otomatis laba/rugi dari:
  - Pendapatan bunga pinjaman
  - Pendapatan administrasi
  - Biaya operasional
  - Pengeluaran lainnya
- Laporan laba rugi per periode (harian, mingguan, bulanan, tahunan)
- Grafik visualisasi laba rugi

### 6. Modul Anggota
- CRUD data anggota
- Profil lengkap anggota
- Riwayat transaksi per anggota

## Role & Permissions

### Admin
- Full access ke semua modul
- CRUD (Create, Read, Update, Delete) semua data
- Setting bunga pinjaman (harian dan mingguan)
- Edit dan delete data kas
- Manajemen user (kasir dan admin)
- Setting sistem (bunga, denda, biaya admin, dll)
- Approve/reject pengajuan pinjaman
- Generate laporan semua modul

### Kasir
- Input data transaksi:
  - Simpanan
  - Pembayaran angsuran
  - Pinjaman (setelah diapprove admin)
  - Kas (pemasukan/pengeluaran)
- View/Read only untuk semua data
- **TIDAK BISA** edit atau delete data
- Print slip/bukti transaksi
- View laporan
