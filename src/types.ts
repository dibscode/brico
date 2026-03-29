export enum StatusAnggota {
  AKTIF = 'AKTIF',
  TIDAK_AKTIF = 'TIDAK_AKTIF',
  KELUAR = 'KELUAR',
}

export enum JenisSimpanan {
  POKOK = 'POKOK',
  WAJIB = 'WAJIB',
  SUKARELA = 'SUKARELA',
}

export enum StatusPinjaman {
  DIAJUKAN = 'DIAJUKAN',
  DISETUJUI = 'DISETUJUI',
  DITOLAK = 'DITOLAK',
  BERJALAN = 'BERJALAN',
  LUNAS = 'LUNAS',
}

export enum JenisTransaksi {
  SETORAN_SIMPANAN = 'SETORAN_SIMPANAN',
  PENARIKAN_SIMPANAN = 'PENARIKAN_SIMPANAN',
  PENCAIRAN_PINJAMAN = 'PENCAIRAN_PINJAMAN',
  ANGSURAN_PINJAMAN = 'ANGSURAN_PINJAMAN',
}

export interface Anggota {
  id: string;
  nomorAnggota: string;
  nama: string;
  nik: string;
  alamat: string;
  telepon: string;
  tanggalBergabung: Date;
  status: StatusAnggota;
}

export interface Simpanan {
  id: string;
  anggotaId: string;
  jenis: JenisSimpanan;
  saldo: number;
}

export interface Pinjaman {
  id: string;
  anggotaId: string;
  jumlahPokok: number;
  bungaPersenPerTahun: number;
  tenorBulan: number;
  angsuranBulanan: number;
  sisaPokok: number;
  status: StatusPinjaman;
  tanggalPengajuan: Date;
  tanggalDisetujui?: Date;
}

export interface Transaksi {
  id: string;
  anggotaId: string;
  jenis: JenisTransaksi;
  jumlah: number;
  keterangan: string;
  tanggal: Date;
  referensiId?: string;
}
