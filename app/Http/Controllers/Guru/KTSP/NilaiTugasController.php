<?php

namespace App\Http\Controllers\Guru\KTSP;

use App\AnggotaKelas;
use App\Exports\FormatImportTugasKTSPExport;
use App\Guru;
use App\Http\Controllers\Controller;
use App\Imports\NilaiTugasKTSPImport;
use App\Kelas;
use App\KtspNilaiTugas;
use App\Pembelajaran;
use App\Tapel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class NilaiTugasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Rata-Rata Nilai Tugas';
        $tapel = Tapel::findorfail(session()->get('tapel_id'));

        $guru = Guru::where('user_id', Auth::user()->id)->first();
        $id_kelas = Kelas::where('tapel_id', $tapel->id)->get('id');

        $data_penilaian = Pembelajaran::where('guru_id', $guru->id)->whereIn('kelas_id', $id_kelas)->where('status', 1)->orderBy('mapel_id', 'ASC')->orderBy('kelas_id', 'ASC')->get();

        foreach ($data_penilaian as $penilaian) {
            $data_anggota_kelas = AnggotaKelas::where('kelas_id', $penilaian->kelas_id)->get();
            $data_nilai_tugas = KtspNilaiTugas::where('pembelajaran_id', $penilaian->id)->get();

            $penilaian->jumlah_anggota_kelas = count($data_anggota_kelas);
            $penilaian->jumlah_telah_dinilai = count($data_nilai_tugas);
        }

        return view('guru.ktsp.nilaitugas.index', compact('title', 'data_penilaian'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);
        $data_anggota_kelas = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)->get();

        $data_nilai_tugas = KtspNilaiTugas::where('pembelajaran_id', $pembelajaran->id)->get();

        if (count($data_nilai_tugas) == 0) {
            $title = 'Input Rata-Rata Nilai Tugas';
            return view('guru.ktsp.nilaitugas.create', compact('title', 'pembelajaran', 'data_anggota_kelas'));
        } else {
            $title = 'Edit Rata-Rata Nilai Tugas';
            return view('guru.ktsp.nilaitugas.edit', compact('title', 'pembelajaran', 'data_nilai_tugas'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($request->anggota_kelas_id)) {
            return back()->with('toast_error', 'Data siswa tidak ditemukan');
        } else {
            for ($cound_siswa = 0; $cound_siswa < count($request->anggota_kelas_id); $cound_siswa++) {

                if ($request->nilai[$cound_siswa] >= 0 && $request->nilai[$cound_siswa] <= 100) {
                    $data_nilai = array(
                        'pembelajaran_id'  => $request->pembelajaran_id,
                        'anggota_kelas_id'  => $request->anggota_kelas_id[$cound_siswa],
                        'nilai'  => ltrim($request->nilai[$cound_siswa]),
                        'created_at'  => Carbon::now(),
                        'updated_at'  => Carbon::now(),
                    );
                    $data_nilai_siswa[] = $data_nilai;
                } else {
                    return back()->with('toast_error', 'Nilai harus berisi antara 0 s/d 100');
                }
            }
            $store_data_nilai = $data_nilai_siswa;
            KtspNilaiTugas::insert($store_data_nilai);
            return redirect('guru/nilaitugas')->with('toast_success', 'Data nilai tugas berhasil disimpan.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        for ($cound_siswa = 0; $cound_siswa < count($request->anggota_kelas_id); $cound_siswa++) {

            if ($request->nilai[$cound_siswa] >= 0 && $request->nilai[$cound_siswa] <= 100) {
                $nilai = KtspNilaiTugas::where('pembelajaran_id', $id)->where('anggota_kelas_id', $request->anggota_kelas_id[$cound_siswa])->first();

                $data_nilai = [
                    'nilai'  => ltrim($request->nilai[$cound_siswa]),
                    'updated_at'  => Carbon::now(),
                ];
                $nilai->update($data_nilai);
            } else {
                return back()->with('toast_error', 'Nilai harus berisi antara 0 s/d 100');
            }
        }
        return redirect('guru/nilaitugas')->with('toast_success', 'Data nilai tugas berhasil diedit.');
    }

    public function format_import(Request $request)
    {
        $pembelajaran = Pembelajaran::findorfail($request->pembelajaran_id);
        $cek_anggota = AnggotaKelas::where('kelas_id', $pembelajaran->kelas_id)->get();
        if (count($cek_anggota) == 0) {
            return back()->with('toast_error', 'Belum ditemukan data anggota kelas');
        } else {
            $filename = 'format_import_nilai_tugas ' . $pembelajaran->mapel->ringkasan_mapel . ' ' . $pembelajaran->kelas->nama_kelas . ' ' . date('Y-m-d H_i_s') . '.xls';
            $id = $pembelajaran->id;
            return Excel::download(new FormatImportTugasKTSPExport($id), $filename);
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new NilaiTugasKTSPImport, $request->file('file_import'));
            return back()->with('toast_success', 'Data nilai tugas berhasil diimport');
        } catch (\Throwable $th) {
            return back()->with('toast_error', 'Maaf, format data tidak sesuai');
        }
    }
}
