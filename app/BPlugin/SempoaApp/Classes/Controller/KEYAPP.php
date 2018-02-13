<?php

/**
 * Created by PhpStorm.
 * User: efindiongso
 * Date: 1/19/18
 * Time: 9:00 AM
 */
class KEYAPP
{
// Konstanta

    public static $PANJANG_PWD = 6;
    public static $SUCCESS = "Success";
    public static $subjectForgotPasswordParent = "Subject";
    public static $PARENT_ID_KOSONG = "Silahkan logout dan login lagi!";
    public static $GURU_ID_KOSONG = "Username salah!";
    public static $GURU_ID_KOSONG_LOGOUT = "Guru ID salah, silahkan logout dan login lagi!";


    public static $PASSWORD_SALAH = "Password yang Anda masukan salah!";
    public static $TEACHER_TDK_BS_LOGIN = "ID atau Password salah!";
    public static $LOGIN_BERHASIL = "Login berhasil";
    public static $MASUKAN_NAMA_PARENT = "Masukan Nama Baru Anda!";
    public static $MASUKAN_NR_HP_PARENT = "Masukan No HP Baru Anda!";
    public static $MASUKAN_EMAIL_PARENT = "Masukan Email Baru Anda!";
    public static $MASUKAN_NEW_PASSWORD_PARENT = "Masukan Password Baru Anda!";
    public static $MASUKAN_PASSWORD_PARENT = "Masukan Password Anda!";

    public static $PARENT_GANTI_NAMA_SUKSES = "Name berhasil diganti!";
    public static $PARENT_GANTI_EMAIL_SUKSES = "Email berhasil diganti!";
    public static $PARENT_GANTI_HP_SUKSES = "No HP berhasil diganti!";
    public static $PARENT_GANTI_PASSWORD_SUKSES = "Password berhasil diganti!";


    public static $MASUKAN_KODE_SISWA = "Masukan kode Siswa";
    public static $KODE_SISWA_NOT_FOUND = "kode Siswa tidak ditemukan, hubungi Admin TC Anda!";

    public static $NAMA_MURID_KOSONG = "Nama Murid kosong";
    public static $BIRTHDAY_MURID_KOSONG = "Tanggal lahir Murid kosong";


    // Add Anak
    public static $MURID_SDH_PUNYA_PARENT = "Murid sudah mempunyai parent";
    public static $DATA_ANAK_BERHASIL_DISIMPAN = "Data anak berhasil disimpan";
    public static $DATA_ANAK_GAGALL_DISIMPAN = "Data anak gagal disimpan";


    // TOP UP

    // status
    public static $STATUS_TOP_UP_PENDING = 2;
    public static $STATUS_TOP_UP_APPROVED = 1;
    public static $STATUS_TOP_UP_CANCELED = 99;
    public static $KODE_SISWA_KOSONG = "Kode Siswa kosong";
    public static $JUMLAH_DIBELI_KOSONG = "Jumlah yang dibeli kosong!";
    public static $CARA_PEMBAYARAN_KOSONG = "Cara pembayaran kosong!";
    public static $TOP_UP_MSG = "We will process your payment";


    //Add Murid by Guru

    public static $GURU_TDK_NGAJAR_DIKELAS_INI = "Guru ini tidak mengajar di kelas ini";
    public static $ADD_MURID_ID_KOSONG = "Silahkan pilih murid sekali lagi!";
    public static $MURID_GAGAL_DIMASUKAN_KELAS = "Murid gagal dimasukan kedalam kelas";
    public static $MURID_SUDAH_DI_ADD_DALAM_KELAS = "Murid sudah dimasukan kedalam kelas, hubungi ADMIN TC Anda";


    // Absensi Guru
    public static $KELAS_ID_KOSONG_ABSENSI = "Pilih kelas!";
    public static $GURU_TDK_NGAJAR_DI_KELAS_INI = "Guru tidak mengajar di kelas ini!";
    public static $GURU_NGAJAR_TP_WAKTU_BERBEDA = "Hari kelas berbeda";
    public static $GURU_TIDAK_BOLEH_ABSEN = "Guru tidak boleh absen lagi";


    // Account Setting Teacher

    public static $ID_GURU_TIDAK_DITEMUKAN = "Silahkan logout dan login lagi. Data Anda tidak ditemukan di System!";
    public static $MASUKAN_EMAIL_BARU_ANDA = "Masukan Email baru Anda!";
    public static $EMAIL_ANDA_SUKSES_DIGANTI = "Email Anda berhasil diubah!";
    public static $HP_ANDA_SUKSES_DIGANTI = "No. HP Anda berhasil diubah!";
    public static $NO_HP_BARU_TIDAK_ADA = "Masukan No HP baru Anda!";
    public static $PWD_BARU_KOSONG = "Masukan Password baru!";
    public static $PANJANG_PWD_HRS_6 = "Panjang password harus 6";


    public static $TYPE_PARENT = "1";
    public static $TYPE_CHILD = "2";

    // Notif
    public static $NOTIF_TYPE_TOP_UP = 1;
    public static $NOTIF_TYPE_INFORMATION = 2;
    public static $NOTIF_TYPE_CHALLANGE = 3;
    public static $NOTIF_TITLE_TOP_UP = "Request Coin";

    public static $NOTIF_CONTENT_TOP_UP = "Your Child requests the Coin";
    // Murid

    public static $MURID_SALAH_LOGIN = "ID atau Password yg anda masukan salah";
    public static $ID_MURID_KOSONG = "Username anda harus di isi!";
    public static $ID_MURID_KOSONG_LOGOUT = "Username anda kosong, silahkan login kembali";
    public static $MURID_TIDAK_MASUK_HARI_INI = "Murid tidak ada jadwal di hari ini";
    public static $MURID_SALAH_JADWAL = "Murid salah jadwal";
    public static $MURID_TIDAK_BOLEH_ABSEN = "Murid tidak boleh absen lagi";

//    TOPUP ANAK
    public static $NOMOR_BANK_KOSONG = "Nomor bank anda kosong";
    public static $JUMLAH_COIN_KOSOSNG = "Field anda kosong";

    // challange
    public static $MURID_SUDAH_JOIN = "Murid sudah join di challange ini, silahkan tunggu challange berikutnya";

    // Generic
// Absensi murid
    public static $KELAS_SALAH_MURID = "Murid salah kelas";
    public static $HARI_INI_SUDAH_ABSEN = "Hari ini sudah absen";
    public static $BERHASIL_ABSEN = "Murid berhasil diabsen";

}