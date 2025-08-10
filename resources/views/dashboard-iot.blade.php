@extends('layouts.app')

@push('script-header')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

<?php 
  $totalPelanggan = \App\Models\Pelanggan::all()->count();
  $totalTagihan = \App\Models\Tagihan::all()->count();

  $tagihan = \App\Models\Tagihan::whereNull('deleted_at')->get();
  $tagihanLimit4 = \App\Models\Tagihan::orderBy('tagihanId', 'desc')->limit(4)->get();
  
  $tagihan->transform(function($item) {
      $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
      $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
      return $item;
  });

  $totalSemuaTagihanBelumLunas = $tagihan->whereIn('tagihanStatus', ['Pending', 'Belum Lunas'])->sum('tagihanJumlahTotal');
  $totalSemuaTagihanLunas = $tagihan->where('tagihanStatus', 'Lunas')->sum('tagihanJumlahTotal');
?>
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard IoT</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard IoT</li>
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
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Total Pelanggan</span>
                <span class="info-box-number">
                  {{ $totalPelanggan }}
                  <small></small>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-invoice"></i></span>

              <div class="info-box-content">
                <span class="info-box-text" data-toggle="tooltip" data-placement="top" title="Total tagihan yang belum dibayar">Total Tagihan</span>
                <span class="info-box-number">{{ $totalTagihan }}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
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
          <div class="col-12 col-sm-6 col-md-3">
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
          <div class="col-md-6">
            <div class="form-group d-flex align-items-center flex-nowrap">
              <label for="customerFilter" class="mr-2">Pilih Pelanggan:</label>
              <select id="customerFilter" class="form-control">
                <option value="">Semua Pelanggan</option>
              </select>
            </div>
          </div>

          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Penggunaan Air</h3>
              </div>
              <div class="card-body">
                <div id="chart" class="w-100"></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Penggunaan Air Tanpa 0</h3>
              </div>
              <div class="card-body">
                <div id="chartNotNull" class="w-100"></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Total Penggunaan</h3>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group d-flex align-items-center flex-nowrap">
                      <label for="periodeFilter" class="mr-2">Pilih Periode:</label>
                      <select id="periodeFilter" class="form-control">
                        <option value="daily">Harian</option>
                        <option value="monthly">Bulanan</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-between">
                  <div id="summary-chart" class="w-50 mt-3"></div>
                  <div id="summary-chartLine" class="w-50 mt-3"></div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
</div>
    
@endsection

@push('scripts')
<script>
  let chart;
  let chartNotNull;  

  // Fetch daftar pelanggan untuk dropdown
  fetch('{{ route('api.getPelanggan') }}')
      .then(response => response.json())
      .then(customers => {
          let select = document.getElementById("customerFilter");
          customers.forEach(customer => {
              let option = document.createElement("option");
              option.value = customer.pelangganKode;
              option.textContent = customer.pelangganKode + ' - ' + customer.pelangganNama;
              select.appendChild(option);
          });
      });

      function fetchData(pelangganKode = '') {
    let url = '{{ route('api.getWaterUsageChart') }}';
    if (pelangganKode) {
        url += `?pelangganKode=${pelangganKode}`;
    }

    // Show loading indicator
    document.querySelector("#chart").innerHTML = '<div class="loading">Loading...</div>';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.length) {
                document.querySelector("#chart").innerHTML = '<div class="loading">Tidak ada data tersedia</div>';
                return;
            }

            let seriesData = data.map(d => ({
                x: new Date(d.datetime).getTime(),
                y: d.total_usage
            }));

            let minX = Math.min(...seriesData.map(d => d.x));
            let maxX = Math.max(...seriesData.map(d => d.x));

            let options = {
                chart: {
                    id: 'area-datetime',
                    type: 'area',
                    height: 350,
                    zoom: {
                        autoScaleYaxis: true,
                    }
                },
                series: [{ name: 'Pemakaian Air (m³)', data: seriesData }],
                xaxis: {
                    type: 'datetime',
                    min: minX,
                    max: maxX,
                    tickAmount: 6,
                    labels: {
                        datetimeUTC: false
                    }
                },
                tooltip: {
                    x: {
                        format: 'dd MMM yyyy HH:mm:ss'
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 100]
                    }
                }
            };

            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.querySelector("#chart").innerHTML = '<div class="loading">Gagal memuat data</div>';
        })
        .finally(() => {
            let loadingElement = document.querySelector("#chart .loading");
            if (loadingElement) {
                loadingElement.remove();
            }
        });
}


  function fetchDataNotNull(pelangganKode = '') {
    let url = '{{ route('api.getWaterUsageChartNotNull') }}';
    if (pelangganKode) {
        url += `?pelangganKode=${pelangganKode}`;
    }

    // Show loading indicator
    document.querySelector("#chartNotNull").innerHTML = '<div class="loading">Loading...</div>';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.length) {
                document.querySelector("#chartNotNull").innerHTML = '<div class="loading">Tidak ada data tersedia</div>';
                return;
            }

            let seriesData = data.map(d => ({
                x: new Date(d.datetime).getTime(),
                y: d.total_usage
            }));

            let minX = Math.min(...seriesData.map(d => d.x));
            let maxX = Math.max(...seriesData.map(d => d.x));

            let options = {
                chart: {
                    id: 'area-datetime',
                    type: 'area',
                    height: 350,
                    zoom: {
                        autoScaleYaxis: true
                    }
                },
                series: [{ name: 'Pemakaian Air (m³)', data: seriesData }],
                xaxis: {
                    type: 'datetime',
                    min: minX,
                    max: maxX,
                    tickAmount: 6,
                    labels: {
                        datetimeUTC: false
                    }
                },
                tooltip: {
                  x: {
                        format: 'dd MMM yyyy HH:mm:ss'
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 100]
                    }
                },
            };

            if (chartNotNull) {
                chartNotNull.updateOptions(options);
            } else {
                chartNotNull = new ApexCharts(document.querySelector("#chartNotNull"), options);
                chartNotNull.render();
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.querySelector("#chartNotNull").innerHTML = '<div class="loading">Gagal memuat data</div>';
        })
        .finally(() => {
            let loadingElement = document.querySelector("#chartNotNull .loading");
            if (loadingElement) {
                loadingElement.remove();
            }
        });
}


// Inisialisasi data pertama kali
fetchData();
fetchDataNotNull();

// Event listener saat filter pelanggan berubah
document.getElementById("customerFilter").addEventListener("change", function () {
    let selectedCustomer = this.value;
    fetchData(selectedCustomer);
    fetchDataNotNull(selectedCustomer);

    // Fetch summary data
    fetchSummaryData('daily', selectedCustomer);
});


  // Summary
  function fetchSummaryData(periode = 'daily', pelangganKode = '') {
      let url = '{{ route('api.getWaterUsageSummary') }}' + `?periode=${periode}`;
      if (pelangganKode) {
          url += `&pelangganKode=${pelangganKode}`;
      }

      // Show loading indicator
      document.querySelector("#summary-chart").innerHTML = '<div>Loading...</div>';

      fetch(url)
          .then(response => response.json())
          .then(data => {
              let seriesData = data.map(d => ({
                  x: d.period,
                  y: d.total_usage
              }));

              let options = {
                  chart: { type: 'bar', height: 350 },
                  series: [{ name: 'Total Pemakaian (m³)', data: seriesData }],
                  xaxis: { type: 'category', title: { text: periode === 'monthly' ? 'Bulan' : 'Tanggal' } }
              };

              // Clear loading indicator
              document.querySelector("#summary-chart").innerHTML = '';

              let summaryChart = new ApexCharts(document.querySelector("#summary-chart"), options);
              summaryChart.render();
          })
          .catch(() => {
              // Handle errors and clear loading indicator
              document.querySelector("#summary-chart").innerHTML = '<div>Error loading data</div>';
          });
  }

  function fetchSummaryDataLine(periode = 'daily', pelangganKode = '') {
      let url = '{{ route('api.getWaterUsageSummary') }}' + `?periode=${periode}`;
      if (pelangganKode) {
          url += `&pelangganKode=${pelangganKode}`;
      }

      // Show loading indicator
      document.querySelector("#summary-chartLine").innerHTML = '<div>Loading...</div>';

      fetch(url)
          .then(response => response.json())
          .then(data => {
              let seriesData = data.map(d => ({
                  x: d.period,
                  y: d.total_usage
              }));

              let options = {
                  chart: { type: 'line', height: 350 },
                  series: [{ name: 'Total Pemakaian (m³)', data: seriesData }],
                  xaxis: { type: 'category', title: { text: periode === 'monthly' ? 'Bulan' : 'Tanggal' } },
                  dataLabels: {enabled: false},
                  stroke: {curve: 'straight'},
              };

              // Clear loading indicator
              document.querySelector("#summary-chartLine").innerHTML = '';

              let summaryChartLine = new ApexCharts(document.querySelector("#summary-chartLine"), options);
              summaryChartLine.render();
          })
          .catch(() => {
              // Handle errors and clear loading indicator
              document.querySelector("#summary-chartLine").innerHTML = '<div>Error loading data</div>';
          });
  }

// Load data awal
fetchSummaryData();
fetchSummaryDataLine();

// Event listener untuk filter
document.getElementById("periodeFilter").addEventListener("change", function () {
    pelangganKode = document.getElementById("customerFilter").value ?? '';
    fetchSummaryData(this.value, pelangganKode);
    fetchSummaryDataLine(this.value, pelangganKode);
});
</script>
@endpush
