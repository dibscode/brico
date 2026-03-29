import { randomUUID } from 'crypto';
import {
  Anggota,
  Simpanan,
  Pinjaman,
  Transaksi,
  StatusAnggota,
  JenisSimpanan,
  StatusPinjaman,
  JenisTransaksi,
} from './types';

export class KoperasiBankRakyatIndonesia {
  private anggotaList: Map<string, Anggota> = new Map();
  private simpananList: Map<string, Simpanan> = new Map();
  private pinjamanList: Map<string, Pinjaman> = new Map();
  private transaksiList: Transaksi[] = [];
  private nomorAnggotaCounter: number = 1;

  private generateNomorAnggota(): string {
    const tahun = new Date().getFullYear();
    const nomor = String(this.nomorAnggotaCounter++).padStart(5, '0');
    return `BRI-KOP-${tahun}-${nomor}`;
  }

  daftarAnggota(data: Omit<Anggota, 'id' | 'nomorAnggota' | 'tanggalBergabung' | 'status'>): Anggota {
    const id = randomUUID();
    const anggota: Anggota = {
      id,
      nomorAnggota: this.generateNomorAnggota(),
      nama: data.nama,
      nik: data.nik,
      alamat: data.alamat,
      telepon: data.telepon,
      tanggalBergabung: new Date(),
      status: StatusAnggota.AKTIF,
    };

    this.anggotaList.set(id, anggota);

    // Otomatis buat simpanan pokok, wajib, dan sukarela
    ([JenisSimpanan.POKOK, JenisSimpanan.WAJIB, JenisSimpanan.SUKARELA] as JenisSimpanan[]).forEach((jenis) => {
      const simpanan: Simpanan = {
        id: randomUUID(),
        anggotaId: id,
        jenis,
        saldo: 0,
      };
      this.simpananList.set(simpanan.id, simpanan);
    });

    return anggota;
  }

  getAnggota(anggotaId: string): Anggota {
    const anggota = this.anggotaList.get(anggotaId);
    if (!anggota) {
      throw new Error(`Anggota dengan ID ${anggotaId} tidak ditemukan`);
    }
    return anggota;
  }

  getAllAnggota(): Anggota[] {
    return Array.from(this.anggotaList.values());
  }

  nonaktifkanAnggota(anggotaId: string): Anggota {
    const anggota = this.getAnggota(anggotaId);
    const pinjamanAktif = this.getPinjamanAnggota(anggotaId).filter(
      (p) => p.status === StatusPinjaman.BERJALAN || p.status === StatusPinjaman.DISETUJUI
    );
    if (pinjamanAktif.length > 0) {
      throw new Error('Anggota masih memiliki pinjaman aktif');
    }
    anggota.status = StatusAnggota.TIDAK_AKTIF;
    return anggota;
  }

  getSimpananAnggota(anggotaId: string): Simpanan[] {
    this.getAnggota(anggotaId);
    return Array.from(this.simpananList.values()).filter((s) => s.anggotaId === anggotaId);
  }

  setor(anggotaId: string, jenisSimpanan: JenisSimpanan, jumlah: number): Simpanan {
    if (jumlah <= 0) {
      throw new Error('Jumlah setoran harus lebih dari 0');
    }
    const anggota = this.getAnggota(anggotaId);
    if (anggota.status !== StatusAnggota.AKTIF) {
      throw new Error('Hanya anggota aktif yang dapat melakukan setoran');
    }

    const simpanan = Array.from(this.simpananList.values()).find(
      (s) => s.anggotaId === anggotaId && s.jenis === jenisSimpanan
    );
    if (!simpanan) {
      throw new Error(`Rekening simpanan ${jenisSimpanan} tidak ditemukan`);
    }

    simpanan.saldo += jumlah;

    const transaksi: Transaksi = {
      id: randomUUID(),
      anggotaId,
      jenis: JenisTransaksi.SETORAN_SIMPANAN,
      jumlah,
      keterangan: `Setoran simpanan ${jenisSimpanan}`,
      tanggal: new Date(),
      referensiId: simpanan.id,
    };
    this.transaksiList.push(transaksi);

    return simpanan;
  }

  tarik(anggotaId: string, jenisSimpanan: JenisSimpanan, jumlah: number): Simpanan {
    if (jumlah <= 0) {
      throw new Error('Jumlah penarikan harus lebih dari 0');
    }
    if (jenisSimpanan === JenisSimpanan.POKOK || jenisSimpanan === JenisSimpanan.WAJIB) {
      throw new Error('Simpanan pokok dan wajib tidak dapat ditarik selama masih menjadi anggota');
    }
    const anggota = this.getAnggota(anggotaId);
    if (anggota.status !== StatusAnggota.AKTIF) {
      throw new Error('Hanya anggota aktif yang dapat melakukan penarikan');
    }

    const simpanan = Array.from(this.simpananList.values()).find(
      (s) => s.anggotaId === anggotaId && s.jenis === jenisSimpanan
    );
    if (!simpanan) {
      throw new Error(`Rekening simpanan ${jenisSimpanan} tidak ditemukan`);
    }
    if (simpanan.saldo < jumlah) {
      throw new Error('Saldo tidak mencukupi');
    }

    simpanan.saldo -= jumlah;

    const transaksi: Transaksi = {
      id: randomUUID(),
      anggotaId,
      jenis: JenisTransaksi.PENARIKAN_SIMPANAN,
      jumlah,
      keterangan: `Penarikan simpanan ${jenisSimpanan}`,
      tanggal: new Date(),
      referensiId: simpanan.id,
    };
    this.transaksiList.push(transaksi);

    return simpanan;
  }

  ajukanPinjaman(
    anggotaId: string,
    jumlahPokok: number,
    bungaPersenPerTahun: number,
    tenorBulan: number
  ): Pinjaman {
    if (jumlahPokok <= 0) throw new Error('Jumlah pinjaman harus lebih dari 0');
    if (tenorBulan <= 0) throw new Error('Tenor pinjaman harus lebih dari 0 bulan');
    if (bungaPersenPerTahun < 0) throw new Error('Bunga tidak boleh negatif');

    const anggota = this.getAnggota(anggotaId);
    if (anggota.status !== StatusAnggota.AKTIF) {
      throw new Error('Hanya anggota aktif yang dapat mengajukan pinjaman');
    }

    const pinjamanAktif = this.getPinjamanAnggota(anggotaId).filter(
      (p) => p.status === StatusPinjaman.BERJALAN || p.status === StatusPinjaman.DISETUJUI
    );
    if (pinjamanAktif.length > 0) {
      throw new Error('Anggota masih memiliki pinjaman aktif');
    }

    const bungaBulanan = bungaPersenPerTahun / 100 / 12;
    let angsuranBulanan: number;
    if (bungaBulanan === 0) {
      angsuranBulanan = jumlahPokok / tenorBulan;
    } else {
      angsuranBulanan =
        (jumlahPokok * bungaBulanan * Math.pow(1 + bungaBulanan, tenorBulan)) /
        (Math.pow(1 + bungaBulanan, tenorBulan) - 1);
    }

    const pinjaman: Pinjaman = {
      id: randomUUID(),
      anggotaId,
      jumlahPokok,
      bungaPersenPerTahun,
      tenorBulan,
      angsuranBulanan: Math.round(angsuranBulanan),
      sisaPokok: jumlahPokok,
      status: StatusPinjaman.DIAJUKAN,
      tanggalPengajuan: new Date(),
    };

    this.pinjamanList.set(pinjaman.id, pinjaman);
    return pinjaman;
  }

  setujuiPinjaman(pinjamanId: string): Pinjaman {
    const pinjaman = this.getPinjaman(pinjamanId);
    if (pinjaman.status !== StatusPinjaman.DIAJUKAN) {
      throw new Error('Hanya pinjaman dengan status DIAJUKAN yang dapat disetujui');
    }
    pinjaman.status = StatusPinjaman.DISETUJUI;
    pinjaman.tanggalDisetujui = new Date();
    return pinjaman;
  }

  tolakPinjaman(pinjamanId: string): Pinjaman {
    const pinjaman = this.getPinjaman(pinjamanId);
    if (pinjaman.status !== StatusPinjaman.DIAJUKAN) {
      throw new Error('Hanya pinjaman dengan status DIAJUKAN yang dapat ditolak');
    }
    pinjaman.status = StatusPinjaman.DITOLAK;
    return pinjaman;
  }

  cairkanPinjaman(pinjamanId: string): Pinjaman {
    const pinjaman = this.getPinjaman(pinjamanId);
    if (pinjaman.status !== StatusPinjaman.DISETUJUI) {
      throw new Error('Hanya pinjaman dengan status DISETUJUI yang dapat dicairkan');
    }
    pinjaman.status = StatusPinjaman.BERJALAN;

    const transaksi: Transaksi = {
      id: randomUUID(),
      anggotaId: pinjaman.anggotaId,
      jenis: JenisTransaksi.PENCAIRAN_PINJAMAN,
      jumlah: pinjaman.jumlahPokok,
      keterangan: 'Pencairan pinjaman',
      tanggal: new Date(),
      referensiId: pinjamanId,
    };
    this.transaksiList.push(transaksi);

    return pinjaman;
  }

  bayarAngsuran(pinjamanId: string, jumlah: number): Pinjaman {
    const pinjaman = this.getPinjaman(pinjamanId);
    if (pinjaman.status !== StatusPinjaman.BERJALAN) {
      throw new Error('Hanya pinjaman dengan status BERJALAN yang dapat dibayar angsurannya');
    }
    if (jumlah <= 0) {
      throw new Error('Jumlah angsuran harus lebih dari 0');
    }
    if (jumlah > pinjaman.sisaPokok) {
      jumlah = pinjaman.sisaPokok;
    }

    pinjaman.sisaPokok -= jumlah;
    if (pinjaman.sisaPokok <= 0) {
      pinjaman.sisaPokok = 0;
      pinjaman.status = StatusPinjaman.LUNAS;
    }

    const transaksi: Transaksi = {
      id: randomUUID(),
      anggotaId: pinjaman.anggotaId,
      jenis: JenisTransaksi.ANGSURAN_PINJAMAN,
      jumlah,
      keterangan: `Pembayaran angsuran pinjaman`,
      tanggal: new Date(),
      referensiId: pinjamanId,
    };
    this.transaksiList.push(transaksi);

    return pinjaman;
  }

  getPinjaman(pinjamanId: string): Pinjaman {
    const pinjaman = this.pinjamanList.get(pinjamanId);
    if (!pinjaman) {
      throw new Error(`Pinjaman dengan ID ${pinjamanId} tidak ditemukan`);
    }
    return pinjaman;
  }

  getPinjamanAnggota(anggotaId: string): Pinjaman[] {
    return Array.from(this.pinjamanList.values()).filter((p) => p.anggotaId === anggotaId);
  }

  getRiwayatTransaksi(anggotaId: string): Transaksi[] {
    return this.transaksiList.filter((t) => t.anggotaId === anggotaId);
  }

  getTotalAset(): number {
    return Array.from(this.simpananList.values()).reduce((total, s) => total + s.saldo, 0);
  }

  getTotalPinjamanBerjalan(): number {
    return Array.from(this.pinjamanList.values())
      .filter((p) => p.status === StatusPinjaman.BERJALAN)
      .reduce((total, p) => total + p.sisaPokok, 0);
  }
}
