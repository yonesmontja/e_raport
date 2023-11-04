@include('layouts.main.header')
@include('layouts.sidebar.admin')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">{{$title}}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item "><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{$title}}</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- ./row -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-calendar-check"></i> {{$title}}</h3>
            </div>

            <div class="card-body">
              <div class="callout callout-info">
                <form action="{{ route('rekapkehadiran.store') }}" method="POST">
                  @csrf
                  <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Kelas</label>
                    <div class="col-sm-10">
                      <select class="form-control select2" name="kelas_id" style="width: 100%;" required onchange="this.form.submit();">
                        <option value="" disabled>-- Pilih Kelas --</option>
                        @foreach($data_kelas->sortBy('tingkatan_kelas') as $kls)
                        <option value="{{$kls->id}}" @if($kls->id == $kelas->id) selected @endif>{{$kls->nama_kelas}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </form>
              </div>


              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="bg-info">
                    <tr>
                      <th class="text-center" rowspan="2" style="width: 5%;">No</th>
                      <th class="text-center" rowspan="2" style="width: 5%;">NIS</th>
                      <th class="text-center" rowspan="2" style="width: 40%;">Nama Siswa</th>
                      <th class="text-center" rowspan="2" style="width: 5%;">L/P</th>
                      <th class="text-center" colspan="3" style="width: 45%;">Jumlah</th>
                    </tr>
                    <tr>
                      <th class="text-center" style="width: 15%;">Sakit</th>
                      <th class="text-center" style="width: 15%;">Izin</th>
                      <th class="text-center" style="width: 15%;">Tanpa Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $no = 0; ?>
                    @foreach($data_anggota_kelas->sortBy('siswa.nama_lengkap') as $anggota_kelas)
                    <?php $no++; ?>
                    <tr>
                      <td class="text-center">{{$no}}</td>
                      <td class="text-center">{{$anggota_kelas->siswa->nis}}</td>
                      <td>{{$anggota_kelas->siswa->nama_lengkap}}</td>
                      <td class="text-center">{{$anggota_kelas->siswa->jenis_kelamin}}</td>
                      @if(!is_null($anggota_kelas->kehadiran_siswa))
                      <td class="text-center">{{$anggota_kelas->kehadiran_siswa->sakit}}</td>
                      <td class="text-center">{{$anggota_kelas->kehadiran_siswa->izin}}</td>
                      <td class="text-center">{{$anggota_kelas->kehadiran_siswa->tanpa_keterangan}}</td>
                      @else
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      @endif
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

          </div>
          <!-- /.card -->
        </div>

      </div>
      <!-- /.row -->
    </div>
    <!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@include('layouts.main.footer')