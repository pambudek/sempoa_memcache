<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KEY
 *
 * @author efindiongso
 */
class KEY
{

    //put your code here
    public static $AK = "ak";
    public static $KPO = "kpo";
    public static $IBO = "ibo";
    public static $TC = "tc";
    public static $REGISTER = "1";
    public static $SPP = "2";
    public static $ROYALTI = "3";
    public static $IURANBUKU = "4";
    public static $BUKU = "5";
    public static $BARANG = "6";
    public static $PERLENGKAPAN = "7";
    public static $PENDAFTARAN_GURU = "9";
    /*
     * Status Murid
     */
    public static $STATUSMURIDNONAKTIV = 0;
    public static $STATUSMURIDAKTIV = 1;
    public static $STATUSMURIDCUTI = 2;
    public static $STATUSMURIDNKELUAR = 3;
    public static $STATUSMURIDNLULUS = 4;

    /*
     * Status Guru
     */
    public static $STATUSGURUNONQUALIFIED = 0;
    public static $STATUSGURUQUALIFIED = 1;
    public static $STATUSGURURESIGN = 2;
    public static $STATUSGURUNONAKTIV = 99;
    public static $STATUSGURUINDEXALL = 98;

    /*
     * Status Training
     */
    public static $STATUSABSEN = 0;
    public static $STATUSLULUS = 1;
    public static $STATUSTIDAKLULUS = 2;
    public static $STATUSSAKIT = 3;

    public static $STATUSINDEXALL = 99;
    public static $STATUSALL = "All";
    /*
     * Status Pembayaran
     */
    public static $STATUS_NEW = 0;
    public static $STATUS_PAID = 1;
    public static $STATUS_DISCOUNT_100 = 2;
    public static $STATUS_CANCEL = 99;
    public static $NEW = "New";
    public static $Paid = "Paid";
    public static $Cancel = "Cancel";
    public static $WARNA_HIJAU = "#81C784";
    public static $WARNA_BIRU = "#64B5F6";
    public static $WARNA_MERAH = "#FF8A65";
    public static $WARNA_ABU = "#DEDEDE";

// UNDO First Payment

    public static $MAX_UNDO_FIRST_PAYMENT = 30;
//    public static $MAX_UNDO_SPP = 90;
    public static $MAX_UNDO_SPP = 180;
//    public static $MAX_UNDO_IURAN_BUKU = 90;
    public static $MAX_UNDO_IURAN_BUKU = 180;
    /*
     * Load
     */
    public static $LIMIT_PROFILE = 10;
    public static $LIMIT_PO = 100;
    public static $LIMIT_PAGE = 20;

    /*
     * Status Guru 
     */
    public static $STATUS_NOTQ = 0;
    public static $STATUS_Q = 1;


    /*
     * 
     */
    public static $TGL_KOSONG = "1970-01-01 07:00:00";
    public static $TGL_KOSONG_TANPA_WAKTU = "1970-01-01";
    public static $AKTIV = "Aktif";
    public static $NON_AKTIV = "Tidak aktif";
    public static $SEKARANG = "Sekarang";

    /*
     * Journal
     */
    public static $DEBET_REGISTRASI_TC = 401;
    public static $DEBET_PERLENGKAPAN_TC = 402;
    public static $DEBET_IURAN_BULANAN_TC = 403;
    public static $DEBET_IURAN_BUKU_TC = 404;
    public static $KREDIT_ROYALTI_TC = 501;
    public static $KREDIT_BUKU_TC = 502;
    public static $KREDIT_BARANG_TC = 503;
    public static $KREDIT_TRAINING_GURU_TC = 504;
    public static $KREDIT_PERLENGKAPAN_TC = 505;
    public static $KREDIT_ALAT_BANTU_MENGAJAR_TC = 506;
    public static $KREDIT_REGISTRASI_GURU_TC = 507;
    public static $DEBET_ROYALTI_IBO = 411;
    public static $DEBET_BUKU_IBO = 412;
    public static $DEBET_BARANG_IBO = 413;
    public static $DEBET_TRAINING_GURU_IBO = 414;
    public static $DEBET_PERLENGKAPAN_IBO = 415;
    public static $DEBET_ALAT_BANTU_MENGAJAR_IBO = 416;
    public static $DEBET_REGISTRASI_GURU_TC = 417;
    public static $KREDIT_ROYALTI_IBO = 511;
    public static $KREDIT_BUKU_IBO = 522;
    public static $KREDIT_BARANG_IBO = 523;
    public static $KREDIT_TRAINING_TRAINER_IBO = 524;
    public static $KREDIT_PERLENGKAPAN_IBO = 525;
    public static $KREDIT_ALAT_BANTU_MENGAJAR_IBO = 526;
    public static $DEBET_ROYALTI_KPO = 421;
    public static $DEBET_BUKU_KPO = 422;
    public static $DEBET_BARANG_KPO = 423;
    public static $DEBET_TRAINING_TRAINER_KPO = 424;
    public static $DEBET_PERLENGKAPAN_KPO = 425;
    public static $DEBET_ALAT_BANTU_MENGAJAR_KPO = 426;

    /*
     * Jenis Biaya
     */
    public static $BIAYA_REGISTRASI = 1;
    public static $BIAYA_BIAYA_SPP = 2;
    public static $BIAYA_ROYALTI = 3;
    public static $BIAYA_IURAN_BUKU = 4;
    public static $BIAYA_BUKU = 5;
    public static $BIAYA_PERLENGKAPAN_JUNIOR = 7;
    public static $BIAYA_PERLENGKAPAN_FOUNDATION = 8;
    public static $BIAYA_PENDAFTARAN_GURU = 9;
    public static $BIAYA_ALAT_BANTU_NGAJAR = 10;


    /*
     *
     */
    public static $BIAYA_SPP_TYPE_1 = 2;
    public static $BIAYA_SPP_TYPE_2 = 11;

    /*
     *
     */
    public static $LEVEL_JUNIOR1 = 1;
    public static $LEVEL_JUNIOR2 = 2;
    public static $LEVEL_F1 = 3;
    public static $LEVEL_F2 = 4;
    public static $LEVEL_I1 = 6;
    public static $LEVEL_I2 = 7;
    public static $LEVEL_I3 = 8;
    public static $LEVEL_A1 = 9;
    public static $LEVEL_A2 = 10;
    public static $LEVEL_A3 = 11;
    public static $LEVEL_GM1 = 12;
    public static $LEVEL_GM2 = 13;
    public static $LEVEL_GM3 = 14;

    /*
     * 
     */
// Error buat create TC
    public static $ERROR_ORG_TC_KOSONG = "Kode TC tidak boleh kosong!";
    public static $ERROR_ORG_TC_TDK_SAMA_IBO = "Kode TC tidak sama dengan Kode IBO!";
    public static $ERROR_ORG_TC_KURANG_5 = "Kode TC harus 5 Digit!";
// Error buat create IBO
    public static $ERROR_ORG_IBO_KOSONG = "Kode IBO tidak boleh kosong!";
    public static $ERROR_ORG_IBO_KURANG_2 = "Kode TC harus 2 Digit!";
    public static $ERROR_NAMA = "Nama tidak boleh kosong!";
    public static $ERROR_ALAMAT = "Alamat tidak boleh kosong!";
    public static $ERROR_EMAIL = "Email tidak boleh kosong!";
    public static $ERROR_PROPINSI = "Propinsi tidak boleh kosong!";
    public static $ERROR_TEL = "Telefon  tidak boleh kosong!";

    /*
     * Training
     */
    public static $JENIS_TRAINING_EVALUASI = "Evaluasi";
    public static $JENIS_TRAINING_MATERI = "Materi";

    /*
     * Barang, BUku dan Perlengkapan
     */
    public static $JENIS_BIAYA_BARANG = 0;
    public static $JENIS_BIAYA_BUKU = 1;
    public static $JENIS_BIAYA_PERLENGKAPAN = 2;
    public static $JB_BARANG = "Barang";
    public static $JB_BUKU = "Buku";
    public static $JB_PERLENGKAPAN = "Perlengkapan";
    /*
     * Object dr System
     */
    public static $GURU = "Guru";
    public static $MURID = "Murid";
    public static $TRAINER = "Trainer";


    /*
     * Biaya Traiing 
     */
    public static $INDEX_TRAINING_SATUAN = 0;
    public static $INDEX_TRAINING_PAKET = 1;
    public static $TRAINING_SATUAN = "Satuan";
    public static $TRAINING_PAKET = "Paket";

    /*
     * 
     */
    public static $LEVEL_GROUP_JUNIOR = 1;
    public static $LEVEL_GROUP_FOUNDATION = 2;
    public static $LEVEL_GROUP_INTERMEDIATE = 3;
    public static $LEVEL_GROUP_ADVANCE = 4;
    public static $LEVEL_GROUP_GRAND_MODULE = 5;
    public static $GROUP_JUNIOR = "Junior";
    public static $GROUP_FOUNDATION = "Foundation";
    public static $GROUP_INTERMEDIATE = "Intermediate";
    public static $GROUP_ADVANCE = "Advance";
//    public static $GROUP_GRAND_MODULE = "Grand Module";
    public static $GROUP_GRAND_MODULE = "Graduate";

    /*
     * Jenis Ujian
     */
    public static $KEY_UJIAN_LAIN = 0;
    public static $KEY_UJIAN_SPT = 1;
    public static $KEY_UJIAN_EGT = 2;
    public static $UJIAN_LAIN = "Lain Lain";
    public static $UJIAN_SPT = "SPT";
    public static $UJIAN_EGT = "EGT";

    /*
     * Status Aktiv
     */
    public static $KEY_STATUS_AKTIV = 1;
    public static $KEY_STATUS_TIDAK_AKTIV = 0;
    public static $STATUS_AKTIV = "Aktif";
    public static $STATUS_TIDAK_AKTIV = "Tidak Aktif";
    public static $KEY_MONTH_ALL = "All";
    public static $KEY_MONTH_JANUARIL = "Januari";
    public static $KEY_MONTH_FEBUARI = "Febuari";
    public static $KEY_MONTH_MARET = "Maret";
    public static $KEY_MONTH_APRIL = "April";
    public static $KEY_MONTH_MEI = "Mei";
    public static $KEY_MONTH_JUNI = "Juni";
    public static $KEY_MONTH_JULI = "Juli";
    public static $KEY_MONTH_AGUSTUSL = "Agustus";
    public static $KEY_MONTH_SEPTEMBER = "September";
    public static $KEY_MONTH_OKTOBER = "Oktober";
    public static $KEY_MONTH_NOVEMBER = "November";
    public static $KEY_MONTH_DESEMBER = "Desember";
    // Reporting Type
    // IBO
    public static $REPORT_REKAP_KUPON_TC = "rekap_kupon_tc";
    public static $JUDUL_REPORT_REKAP_KUPON_TC = " Rekapitulasi Kupon per TC ";
    public static $REPORT_REKAP_SISWA_IBO = "rekap_siswa_ibo";
    public static $JUDUL_REPORT_REKAP_SISWA_IBO = " Rekapitulasi Siswa per TC ";
    public static $REPORT_REKAP_BULANAN_KUPON = "rekap_bulanan_kupon";
    public static $JUDUL_REKAP_BULANAN_KUPON = " Laporan Rekapitulasi Kupon";

    public static $REPORT_REKAP_ABSEN_GURU = "rekap_absen_guru";
    public static $JUDUL_REPORT_REKAP_ABSEN_GURU = " Laporan Absen Coach";

    public static $REPORT_REKAP_LAMA_BELAJAR_MURID_TC = "laporan_belajar_murid_tc";
    public static $JUDUL_REKAP_LAMA_BELAJAR_MURID_TC = " Laporan Lama Belajar Murid satuan Bulanan";


    // KPO
    public static $REPORT_REKAP_JUMLAH_SISWA_STATUS_KPO = "rekap_jumlah_siswa_by_status_kpo";
    public static $JUDUL_REPORT_REKAP_JUMLAH_SISWA_STATUS_KPO = " Laporan Jumlah Siswa Aktif, Cuti dan Keluar";
    public static $REPORT_REKAP_ALL_SISWA_KPO = "rekap_all_siswa";
    public static $JUDUL_REPORT_REKAP_ALL_SISWA_KPO = " Laporan Rekapitulasi Siswa se Indonesia";
    public static $REPORT_REKAP_SISWA_KPO = "rekap_siswa_kpo";
    public static $JUDUL_REPORT_REKAP_SISWA_KPO = " Laporan Rekapitulasi Siswa";

    public static $REPORT_REKAP_PENJUALAN_B_K_S = "rekap_penjualan_buku_kupon";
    public static $JUDUL_REPORT_REKAP_PENJUALAN_B_K_S = " Laporan Siswa, Buku dan Kupon Per IBO dan Per Tahun";

    public static $REPORT_REKAP_PERKEMBANGAN_IBO = "rekap_perkembangan_ibo";
    public static $JUDUL_REKAP_PERKEMBANGAN_IBO = " Laporan Perkembangan IBO berdasarkan Rata rata jumlah Siswa, Coach per TC Per Tahun";


    public static $REPORT_REKAP_KUPON_TC_LVL = "rekap_kupon_tc_lvl";
    public static $REPORT_REKAP_SISWA_IBO_TC_LVL = "rekap_siswa_tc_lvl";

    public static $JUDUL_REPORT_REKAP_SISWA_TC = " Rekapitulasi Siswa TC";
    public static $REPORT_REKAP_BULANAN_KUPON_TC_LVL = "rekap_bulanan_kupon_tc_lvl";

    public static $PATTERN_NAME = "^([a-zA-Z]+(?:\.)?(?:(?:'| )[a-zA-Z]+(?:\.)?)*)$";

    public static $TITLEDAFTARKELAS = "Daftar Kelas";
    public static $TITLEPERMINTAANSERTIFIKAT = "Permintaan Sertifikat";
    public static $TITLESERTIFIKATTERCETAK = "Sertifikat Tercetak";

    // Kurikulum

    public static $KURIKULUM_BARU = 0;
    public static $KURIKULUM_LAMA = 1;

    public static $KURIKULUM_BARU_TEXT = "Baru";
    public static $KURIKULUM_LAMA_TEXT = "Lama";

    public static $TEXT_LEVEL = "Level";
    public static $TEXT_KURIKULUM = "Kurikulum";

    public static $BUKU_AVAILABLE = 1;
    public static $BUKU_NON_AVAILABLE = 0;

    public static $BUKU_AVAILABLE_TEXT = "Available";
    public static $BUKU_NON_AVAILABLE_TEXT = "Non Available";
    public static $BUKU_RUSAK_TEXT = "Rusak";

    public static $MIN_JUMLAH_KUPON = 10;
    public static $MIN_JUMLAH_BUKU = 10;

    public static $AWALAN_FIRST_PAYMENT = "FP";

//$arrBiaya = array("Barang", "Buku", "Perlengkapan");

    public static $JENIS_BARANG_TEXT = "Barang";
    public static $JENIS_BUKU_TEXT = "Buku";
    public static $JENIS_PERLENGKAPAN_TEXT = "Perlengkapan";

    public static $JENIS_BARANG = 0;
    public static $JENIS_BUKU = 1;
    public static $JENIS_PERLENGKAPAN = 2;

    public static $BUKU_NON_AVAILABLE_ALIAS = 0;
    public static $BUKU_AVAILABLE_ALIAS = 1;
    public static $BUKU_RUSAK_ALIAS = 99;

    public static $RETOUR_STATUS_CLAIM_ALIAS = 0;
    public static $RETOUR_STATUS_CLAIMED_ALIAS = 1;

    public static $RETOUR_STATUS_CLAIM_TEXT = "Claim";
    public static $RETOUR_STATUS_CLAIMED_TEXT = "Claimed";

    // Inovice
    public static $INVOICE_NAIK_KELAS = 1;


    // TYPE EMAIL
    public static $TYPE_EMAIL_SPP = "SPP";
    public static $TYPE_EMAIL_BUKU = "Buku";
    public static $TYPE_EMAIL_FP = "FP";


    // Subject EMAIL;
    public static $SUBJECT_SPP = "SEMPOASIP - Bukti Pembayaran Bulanan";
    public static $SUBJECT_PEMBELIAN_BUKU = "SEMPOASIP - Pembelian Buku";
    public static $SUBJECT_FIRST_PAYMENT = "SEMPOASIP - Bukti Pembayaran Pertama";
}
