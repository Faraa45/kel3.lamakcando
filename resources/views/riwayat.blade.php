<!DOCTYPE html>
<html lang="en">
  <head>
    <title>FoodMart - Riwayat Pemesanan</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{asset('css/vendor.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('style.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
      .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
      }
      .list-group-item img {
        border-radius: 4px;
      }
      .card-body h6 {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
      }
      .empty-message {
        text-align: center;
        padding: 50px;
        color: #6c757d;
        font-size: 1.1em;
      }
    </style>
  </head>

  <body>

    @extends('layout') {{-- Ensure your main layout is extended --}}

    @section('konten')
    <div class="container py-4">
        <h1 class="mb-4">Riwayat Pesanan Anda</h1>

        @if($transaksi->isEmpty())
            <div class="empty-message">
                <p>Anda belum memiliki riwayat pesanan.</p>
                <p>Mulai belanja sekarang di <a href="{{ url('/daftarmenu') }}">Halaman Utama</a>!</p>
            </div>
        @else
            <div class="row">
                @foreach($transaksi as $transaksi_item)
                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-1">Faktur: <span class="text-primary">{{ $transaksi_item->no_faktur }}</span></h5>
                                <p class="mb-0 text-muted">Tanggal: {{ \Carbon\Carbon::parse($transaksi_item->tgl)->format('d F Y H:i') }}</p>
                                <p class="mb-0 text-muted">Status: <span class="badge {{ $transaksi_item->status == 'bayar' ? 'bg-success' : 'bg-warning' }}">{{ ucfirst($transaksi_item->status) }}</span></p>
                            </div>
                            <div class="card-body">
                                <h6>Detail Pesanan:</h6>
                                <ul class="list-group mb-3">
                                    @if(isset($detail_menu[$transaksi_item->id]) && !$detail_menu[$transaksi_item->id]->isEmpty())
                                        @foreach($detail_menu[$transaksi_item->id] as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    @if($item->foto_menu)
                                                        <img src="{{ asset('storage/menu/' . $item->foto_menu) }}" alt="{{ $item->nama_menu }}" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                                                    @else
                                                        <img src="{{ asset('images/default_menu.png') }}" alt="No Image" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item->nama_menu }}</h6>
                                                        <small class="text-muted">Jumlah: {{ $item->jumlah_dibeli }} x Rp. {{ number_format($item->harga_jual, 0, ',', '.') }}</small>
                                                    </div>
                                                </div>
                                                <strong class="text-dark">Rp. {{ number_format($item->total_per_item, 0, ',', '.') }}</strong>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="list-group-item text-center text-muted">
                                            Tidak ada detail menu untuk transaksi ini.
                                            {{-- This might happen if there's a transaction record but no associated items in `penjualan_menu` --}}
                                        </li>
                                    @endif
                                </ul>
                                <div class="d-flex justify-content-end align-items-center mt-3">
                                    <h4 class="font-weight-bold text-success">Total Tagihan: Rp. {{ number_format($transaksi_item->tagihan, 0, ',', '.') }}</h4>
                                    {{-- This is the corrected line --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endsection

    {{-- Footer and scripts --}}
    <footer>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 copyright">
                <p>© {{ date('Y') }} Foodmart. All rights reserved.</p>
              </div>
              <div class="col-md-6 credit-link text-start text-md-end">
                <p>Free HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a> Distributed by <a href="https://themewagon">ThemeWagon</a></p>
              </div>
            </div>
          </div>
        </div>
        </footer>
        <div id="footer-bottom">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-6 copyright">
                <p>© 2025 Foodmart. All rights reserved.</p>
              </div>
              <div class="col-md-6 credit-link text-start text-md-end">
                <p>Free HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a> Distributed by <a href="https://themewagon">ThemeWagon</a></p>
              </div>
            </div>
          </div>
        </div>

    <script src="{{asset('js/jquery-1.11.0.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="{{asset('js/plugins.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>

    {{-- SweetAlert2 (if used) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  </body>
</html>