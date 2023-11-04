<?php

namespace App\Http\Controllers\Siswa;

use App\AnggotaKelas;
use App\Http\Controllers\Controller;
use App\KehadiranSiswa;
use App\Kelas;
use App\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapKehadiranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Rekap Kehadiran';
        $siswa = Siswa::where('user_id', Auth::user()->id)->first();

        $data_id_kelas = Kelas::where('tapel_id', session()->get('tapel_id'))->get('id');
        $anggota_kelas = AnggotaKelas::whereIn('kelas_id', $data_id_kelas)->where('siswa_id', $siswa->id)->first();
        if (is_null($anggota_kelas)) {
            return back()->with('toast_warning', 'Anda belum masuk ke anggota kelas');
        } else {
            $kehadiran = KehadiranSiswa::where('anggota_kelas_id', $anggota_kelas->id)->first();
            return view('siswa.presensi.index', compact('title', 'siswa', 'kehadiran'));
        }
    }


}
