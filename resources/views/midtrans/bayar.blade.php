<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang Anda</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{env('MIDTRANS_CLIENT_KEY')}}"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Keranjang Belanja Anda</h4>
            </div>
            <div class="card-body">
                <h2>Keranjang Anda</h2>

                @if($menu->count() > 0)
                    @foreach($menu as $item)
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="{{ Storage::url($item->foto) }}" class="img-fluid rounded-start" alt="{{ $item->nama_menu }}">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $item->nama_menu }}</h5>
                                        <p class="card-text">Jumlah Pembelian: {{ $item->jumlah_dibeli }} Unit</p>
                                        <p class="card-text"><strong>Total : Rp {{ number_format($item->total_per_item, 0, ',', '.') }}</strong></p>
                                        {{-- Form to delete item --}}
                                        <form action="{{ url('/hapus/' . $item->menu_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?');">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- You can add a section for the total cart amount here if needed --}}
                     <li class="list-group-item d-flex justify-content-between">
                          <span>Total Belanja</span>
                          <strong>{{ rupiah($total_tagihan) }}</strong>
                     </li>
                     <br>
                    <button id="pay-button" class="w-100 btn btn-primary btn-lg">Lanjutkan Pembayaran</button>


                @else
                    <p>Keranjang Anda kosong.</p>
                @endif

            </div>
        </div>
    </div>

    <div class="container mt-3 mb-4">
        <a href="{{ url('/depan') }}" class="text-decoration-none text-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 5px;"><path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>Kembali ke Galeri</a>
    </div>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
    // Pastikan Midtrans Snap.js sudah dimuat
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        window.snap.pay('{{$snap_token}}', {
        onSuccess: function(result){
            console.log('Pembayaran berhasil:', result);
            window.location.href = "{{ url('/pembayaran/autorefresh/') }}";
        },
        onPending: function(result){
            console.log('Pembayaran tertunda:', result);
            window.location.href = "{{ url('/pembayaran/autorefresh/') }}";
        },
        onError: function(result){
            console.log('Pembayaran gagal:', result);
            alert("Pembayaran gagal. Silakan coba lagi.");
        },
        onClose: function(){
            alert("Anda menutup pop-up pembayaran sebelum menyelesaikan transaksi.");
        }
        });
    });
    </script>

</body>
</html>
