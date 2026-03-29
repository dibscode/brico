# brico

Sistem manajemen **Koperasi Bank Rakyat Indonesia** yang diimplementasikan dengan TypeScript.

## Fitur

- **Manajemen Anggota**: Pendaftaran anggota baru dengan nomor anggota otomatis (`BRI-KOP-YYYY-NNNNN`), nonaktifkan anggota.
- **Manajemen Simpanan**: Setoran dan penarikan simpanan Pokok, Wajib, dan Sukarela.
- **Manajemen Pinjaman**: Pengajuan, persetujuan/penolakan, pencairan, dan pembayaran angsuran pinjaman.
- **Riwayat Transaksi**: Pencatatan seluruh transaksi per anggota.
- **Laporan Keuangan**: Total aset dan total pinjaman berjalan.

## Instalasi

```bash
npm install
```

## Penggunaan

```typescript
import { KoperasiBankRakyatIndonesia, JenisSimpanan, StatusPinjaman } from './src';

const koperasi = new KoperasiBankRakyatIndonesia();

// Daftarkan anggota baru
const anggota = koperasi.daftarAnggota({
  nama: 'Budi Santoso',
  nik: '3301010101010001',
  alamat: 'Jl. Merdeka No. 1, Jakarta',
  telepon: '08123456789',
});

// Setor simpanan pokok
koperasi.setor(anggota.id, JenisSimpanan.POKOK, 500000);

// Ajukan pinjaman
const pinjaman = koperasi.ajukanPinjaman(anggota.id, 10000000, 12, 24);

// Setujui dan cairkan pinjaman
koperasi.setujuiPinjaman(pinjaman.id);
koperasi.cairkanPinjaman(pinjaman.id);

// Bayar angsuran
koperasi.bayarAngsuran(pinjaman.id, pinjaman.angsuranBulanan);
```

## Pengujian

```bash
npm test
```
