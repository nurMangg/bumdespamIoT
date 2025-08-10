@extends('layouts.app')

<?php 
  $totalPelanggan = \App\Models\Pelanggan::all()->count();
  $totalTagihan = \App\Models\Tagihan::all()->count();

  $user = \App\Models\Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
  $tagihan = \App\Models\Tagihan::where('tagihanPelangganId', $user->pelangganId)->get();
  $tagihanLimit4 = \App\Models\Tagihan::where('tagihanPelangganId', $user->pelangganId)->orderBy('tagihanId', 'desc')->limit(10)->get();
  
  $tagihan->transform(function($item) {
      $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
      $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
      return $item;
  });

  $totalSemuaTagihanBelumLunas = $tagihan->where('tagihanStatus', 'Belum Lunas')->sum('tagihanJumlahTotal');
  $totalSemuaTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->sum('tagihanTotal');

?>
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->

        <div class="row">
          @if (Hash::check('password', Auth::user()->password))
          <div class="col-12">
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
              Anda masih menggunakan password default, silakan ubah password default Anda</a>.
            </div>
          </div>
          @endif

          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-6">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-circle"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Tagihan Belum Lunas</span>
                <span class="info-box-number">Rp. {{ number_format($totalSemuaTagihanBelumLunas, 0, ',', '.') }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-6">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Tagihan Lunas</span>
                <span class="info-box-number">Rp. {{ number_format($totalSemuaTagihanLunas, 0, ',', '.') }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          
        </div>

        <div class="row">
          {{-- <div class="col-md-6"> --}}
            {{-- <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Online Store Visitors</h3>
                  <a href="javascript:void(0);">View Report</a>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">820</span>
                    <span>Visitors Over Time</span>
                  </p>
                  <p class="ml-auto d-flex flex-column text-right">
                    <span class="text-success">
                      <i class="fas fa-arrow-up"></i> 12.5%
                    </span>
                    <span class="text-muted">Since last week</span>
                  </p>
                </div>
                <!-- /.d-flex -->
  
                <div class="position-relative mb-4">
                  <canvas id="visitors-chart" height="200"></canvas>
                </div>
  
                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This Week
                  </span>
  
                  <span>
                    <i class="fas fa-square text-gray"></i> Last Week
                  </span>
                </div>
              </div>
            </div> --}}
            <!-- /.card -->

            <!-- /.card -->
          {{-- </div> --}}

          <div class="col-md-12">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Tagihan Baru</h3>
                <div class="card-tools">
                  {{-- <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-download"></i>
                  </a>
                  <a href="#" class="btn btn-tool btn-sm">
                    <i class="fas fa-bars"></i>
                  </a> --}}
                </div>
              </div>
              <div class="card-body table-responsive p-0">
                <table class="table table-striped table-valign-middle">
                  <thead>
                  <tr>
                    <th>Tagihan Kode</th>
                    <th>Nama Pelanggan</th>
                    <th>Pemakaian Air (m3)</th>
                    <th>Tagihan</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach ($tagihanLimit4 as $item)
                  <tr>
                    <td>{{ $item->tagihanKode }}</td>
                    <td>{{ $item->pelanggan->pelangganNama }}</td>
                    <td>{{ $item->tagihanMAwal }} - {{ $item->tagihanMAkhir }} m3</td>
                    <td>Rp. {{ number_format(($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif, 0, ',', '.') }}</td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
</div>
    
@endsection


