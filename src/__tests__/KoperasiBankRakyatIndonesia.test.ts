import { KoperasiBankRakyatIndonesia } from '../KoperasiBankRakyatIndonesia';
import {
  StatusAnggota,
  JenisSimpanan,
  StatusPinjaman,
  JenisTransaksi,
} from '../types';

describe('KoperasiBankRakyatIndonesia', () => {
  let koperasi: KoperasiBankRakyatIndonesia;
  const dataAnggota = {
    nama: 'Budi Santoso',
    nik: '3301010101010001',
    alamat: 'Jl. Merdeka No. 1, Jakarta',
    telepon: '08123456789',
  };

  beforeEach(() => {
    koperasi = new KoperasiBankRakyatIndonesia();
  });

  describe('daftarAnggota', () => {
    it('harus mendaftarkan anggota baru dengan berhasil', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);

      expect(anggota.id).toBeDefined();
      expect(anggota.nomorAnggota).toMatch(/^BRI-KOP-\d{4}-\d{5}$/);
      expect(anggota.nama).toBe(dataAnggota.nama);
      expect(anggota.nik).toBe(dataAnggota.nik);
      expect(anggota.status).toBe(StatusAnggota.AKTIF);
    });

    it('harus membuat rekening simpanan pokok, wajib, dan sukarela secara otomatis', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const simpanan = koperasi.getSimpananAnggota(anggota.id);

      expect(simpanan).toHaveLength(3);
      const jenisSet = new Set(simpanan.map((s) => s.jenis));
      expect(jenisSet.has(JenisSimpanan.POKOK)).toBe(true);
      expect(jenisSet.has(JenisSimpanan.WAJIB)).toBe(true);
      expect(jenisSet.has(JenisSimpanan.SUKARELA)).toBe(true);
      simpanan.forEach((s) => expect(s.saldo).toBe(0));
    });

    it('harus menghasilkan nomor anggota unik berurutan', () => {
      const anggota1 = koperasi.daftarAnggota(dataAnggota);
      const anggota2 = koperasi.daftarAnggota({ ...dataAnggota, nik: '3301010101010002' });

      expect(anggota1.nomorAnggota).not.toBe(anggota2.nomorAnggota);
    });
  });

  describe('getAnggota', () => {
    it('harus mengembalikan anggota yang terdaftar', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const found = koperasi.getAnggota(anggota.id);
      expect(found.id).toBe(anggota.id);
    });

    it('harus melempar error jika anggota tidak ditemukan', () => {
      expect(() => koperasi.getAnggota('id-tidak-ada')).toThrow('tidak ditemukan');
    });
  });

  describe('nonaktifkanAnggota', () => {
    it('harus menonaktifkan anggota yang tidak memiliki pinjaman aktif', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const result = koperasi.nonaktifkanAnggota(anggota.id);
      expect(result.status).toBe(StatusAnggota.TIDAK_AKTIF);
    });

    it('harus melempar error jika anggota masih memiliki pinjaman aktif', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      koperasi.setujuiPinjaman(pinjaman.id);
      koperasi.cairkanPinjaman(pinjaman.id);

      expect(() => koperasi.nonaktifkanAnggota(anggota.id)).toThrow('masih memiliki pinjaman aktif');
    });
  });

  describe('setor', () => {
    it('harus menambah saldo simpanan dengan benar', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const simpanan = koperasi.setor(anggota.id, JenisSimpanan.POKOK, 500000);

      expect(simpanan.saldo).toBe(500000);
    });

    it('harus merekam transaksi setoran', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.setor(anggota.id, JenisSimpanan.WAJIB, 100000);

      const riwayat = koperasi.getRiwayatTransaksi(anggota.id);
      expect(riwayat).toHaveLength(1);
      expect(riwayat[0].jenis).toBe(JenisTransaksi.SETORAN_SIMPANAN);
      expect(riwayat[0].jumlah).toBe(100000);
    });

    it('harus melempar error jika jumlah setoran 0 atau negatif', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      expect(() => koperasi.setor(anggota.id, JenisSimpanan.SUKARELA, 0)).toThrow();
      expect(() => koperasi.setor(anggota.id, JenisSimpanan.SUKARELA, -1000)).toThrow();
    });

    it('harus melempar error jika anggota tidak aktif', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.nonaktifkanAnggota(anggota.id);
      expect(() => koperasi.setor(anggota.id, JenisSimpanan.SUKARELA, 100000)).toThrow('anggota aktif');
    });
  });

  describe('tarik', () => {
    it('harus mengurangi saldo simpanan sukarela dengan benar', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.setor(anggota.id, JenisSimpanan.SUKARELA, 1000000);
      const simpanan = koperasi.tarik(anggota.id, JenisSimpanan.SUKARELA, 400000);

      expect(simpanan.saldo).toBe(600000);
    });

    it('harus melarang penarikan simpanan pokok', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.setor(anggota.id, JenisSimpanan.POKOK, 1000000);
      expect(() => koperasi.tarik(anggota.id, JenisSimpanan.POKOK, 100000)).toThrow('tidak dapat ditarik');
    });

    it('harus melarang penarikan simpanan wajib', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.setor(anggota.id, JenisSimpanan.WAJIB, 1000000);
      expect(() => koperasi.tarik(anggota.id, JenisSimpanan.WAJIB, 100000)).toThrow('tidak dapat ditarik');
    });

    it('harus melempar error jika saldo tidak mencukupi', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      koperasi.setor(anggota.id, JenisSimpanan.SUKARELA, 100000);
      expect(() => koperasi.tarik(anggota.id, JenisSimpanan.SUKARELA, 200000)).toThrow('Saldo tidak mencukupi');
    });
  });

  describe('ajukanPinjaman', () => {
    it('harus membuat pengajuan pinjaman baru', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 10000000, 12, 24);

      expect(pinjaman.id).toBeDefined();
      expect(pinjaman.jumlahPokok).toBe(10000000);
      expect(pinjaman.status).toBe(StatusPinjaman.DIAJUKAN);
      expect(pinjaman.angsuranBulanan).toBeGreaterThan(0);
      expect(pinjaman.sisaPokok).toBe(10000000);
    });

    it('harus menghitung angsuran bulanan dengan benar (bunga 0%)', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 12000000, 0, 12);

      expect(pinjaman.angsuranBulanan).toBe(1000000);
    });

    it('harus melempar error jika anggota sudah memiliki pinjaman aktif', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const p1 = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      koperasi.setujuiPinjaman(p1.id);
      koperasi.cairkanPinjaman(p1.id);

      expect(() => koperasi.ajukanPinjaman(anggota.id, 3000000, 10, 6)).toThrow('masih memiliki pinjaman aktif');
    });

    it('harus melempar error jika jumlah pinjaman tidak valid', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      expect(() => koperasi.ajukanPinjaman(anggota.id, 0, 12, 12)).toThrow();
      expect(() => koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 0)).toThrow();
    });
  });

  describe('setujuiPinjaman & tolakPinjaman', () => {
    it('harus menyetujui pinjaman yang diajukan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      const disetujui = koperasi.setujuiPinjaman(pinjaman.id);

      expect(disetujui.status).toBe(StatusPinjaman.DISETUJUI);
      expect(disetujui.tanggalDisetujui).toBeDefined();
    });

    it('harus menolak pinjaman yang diajukan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      const ditolak = koperasi.tolakPinjaman(pinjaman.id);

      expect(ditolak.status).toBe(StatusPinjaman.DITOLAK);
    });

    it('harus melempar error saat menyetujui pinjaman yang sudah disetujui', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      koperasi.setujuiPinjaman(pinjaman.id);
      expect(() => koperasi.setujuiPinjaman(pinjaman.id)).toThrow('DIAJUKAN');
    });
  });

  describe('cairkanPinjaman', () => {
    it('harus mencairkan pinjaman yang telah disetujui', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      koperasi.setujuiPinjaman(pinjaman.id);
      const cair = koperasi.cairkanPinjaman(pinjaman.id);

      expect(cair.status).toBe(StatusPinjaman.BERJALAN);
    });

    it('harus merekam transaksi pencairan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      koperasi.setujuiPinjaman(pinjaman.id);
      koperasi.cairkanPinjaman(pinjaman.id);

      const riwayat = koperasi.getRiwayatTransaksi(anggota.id);
      const pencairan = riwayat.find((t) => t.jenis === JenisTransaksi.PENCAIRAN_PINJAMAN);
      expect(pencairan).toBeDefined();
      expect(pencairan!.jumlah).toBe(5000000);
    });

    it('harus melempar error jika pinjaman belum disetujui', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      expect(() => koperasi.cairkanPinjaman(pinjaman.id)).toThrow('DISETUJUI');
    });
  });

  describe('bayarAngsuran', () => {
    it('harus mengurangi sisa pokok pinjaman', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 12000000, 0, 12);
      koperasi.setujuiPinjaman(pinjaman.id);
      koperasi.cairkanPinjaman(pinjaman.id);

      const result = koperasi.bayarAngsuran(pinjaman.id, 1000000);
      expect(result.sisaPokok).toBe(11000000);
    });

    it('harus mengubah status pinjaman menjadi LUNAS setelah pelunasan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 0, 5);
      koperasi.setujuiPinjaman(pinjaman.id);
      koperasi.cairkanPinjaman(pinjaman.id);
      koperasi.bayarAngsuran(pinjaman.id, 5000000);

      expect(pinjaman.status).toBe(StatusPinjaman.LUNAS);
      expect(pinjaman.sisaPokok).toBe(0);
    });

    it('harus melempar error jika pinjaman tidak berjalan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const pinjaman = koperasi.ajukanPinjaman(anggota.id, 5000000, 12, 12);
      expect(() => koperasi.bayarAngsuran(pinjaman.id, 500000)).toThrow('BERJALAN');
    });

    it('harus memungkinkan pengajuan pinjaman baru setelah lunas', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const p1 = koperasi.ajukanPinjaman(anggota.id, 5000000, 0, 5);
      koperasi.setujuiPinjaman(p1.id);
      koperasi.cairkanPinjaman(p1.id);
      koperasi.bayarAngsuran(p1.id, 5000000);

      const p2 = koperasi.ajukanPinjaman(anggota.id, 3000000, 10, 6);
      expect(p2.status).toBe(StatusPinjaman.DIAJUKAN);
    });
  });

  describe('laporan keuangan', () => {
    it('harus menghitung total aset dari seluruh simpanan', () => {
      const a1 = koperasi.daftarAnggota(dataAnggota);
      const a2 = koperasi.daftarAnggota({ ...dataAnggota, nik: '3301010101010002' });

      koperasi.setor(a1.id, JenisSimpanan.POKOK, 500000);
      koperasi.setor(a1.id, JenisSimpanan.SUKARELA, 200000);
      koperasi.setor(a2.id, JenisSimpanan.POKOK, 500000);

      expect(koperasi.getTotalAset()).toBe(1200000);
    });

    it('harus menghitung total pinjaman yang sedang berjalan', () => {
      const anggota = koperasi.daftarAnggota(dataAnggota);
      const p = koperasi.ajukanPinjaman(anggota.id, 10000000, 0, 10);
      koperasi.setujuiPinjaman(p.id);
      koperasi.cairkanPinjaman(p.id);
      koperasi.bayarAngsuran(p.id, 2000000);

      expect(koperasi.getTotalPinjamanBerjalan()).toBe(8000000);
    });
  });

  describe('getAllAnggota', () => {
    it('harus mengembalikan semua anggota yang terdaftar', () => {
      koperasi.daftarAnggota(dataAnggota);
      koperasi.daftarAnggota({ ...dataAnggota, nik: '3301010101010002' });

      expect(koperasi.getAllAnggota()).toHaveLength(2);
    });
  });
});
