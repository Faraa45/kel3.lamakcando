{{-- resources/views/keranjang.blade.php --}}
@extends('layout') {{-- Assuming you have a layout file --}}

@section('konten')
<head>
    {{-- Midtrans Snap.js script --}}
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <style>
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 8px;
        }
        .cart-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        .empty-cart-message {
            text-align: center;
            padding: 50px;
            font-size: 1.2em;
            color: #6c757d;
        }
    </style>
</head>

<body>
<div class="container py-4">
    <h1 class="mb-4">Keranjang Belanja Anda</h1>

    {{-- Success and Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Cart Content --}}
    @if($menu->isEmpty())
        <div class="empty-cart-message">
            <p>Keranjang Anda kosong. Yuk, <a href="{{ url('/daftarmenu') }}">mulai belanja</a>!</p>
        </div>
    @else
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        Isi Keranjang
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($menu as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('storage/menu/' . $item->foto_menu) }}" alt="{{ $item->nama_menu }}" class="cart-item-image">
                                    <div>
                                        <h5 class="mb-1">{{ $item->nama_menu }}</h5>
                                        <p class="mb-0 text-muted">Jumlah: {{ $item->jumlah_dibeli }}</p>
                                        <p class="mb-0 text-muted">Harga Satuan: Rp. {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-0">Rp. {{ number_format($item->total_per_item, 0, ',', '.') }}</h4>
                                    <button class="btn btn-sm btn-outline-danger mt-2 delete-item-btn" data-id="{{ $item->penjualan_menu_id }}">
                                        Hapus
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Cart Summary --}}
            <div class="col-md-4">
                <div class="card cart-summary">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Ringkasan Belanja</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Menu:</span>
                            <span>{{ $menu->count() }} jenis</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Barang:</span>
                            <span>{{ $total_tagihan }} item</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="font-weight-bold">Total Tagihan:</h5>
                            <h5 class="font-weight-bold">Rp. {{ number_format($total_tagihan, 0, ',', '.') }}</h5>
                        </div>

                        @if($total_tagihan > 0 && $snap_token)
                            <button class="btn btn-primary btn-block btn-lg" id="pay-button">Bayar</button>
                        @elseif($total_tagihan > 0 && !$snap_token)
                            <div class="alert alert-warning">
                                Gagal mendapatkan token pembayaran. Silakan coba refresh halaman atau hubungi dukungan.
                            </div>
                        @else
                            <div class="alert alert-info">
                                Tidak ada pembayaran yang harus dilakukan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Midtrans Snap button logic
    @if($total_tagihan > 0 && $snap_token)
        document.getElementById('pay-button').onclick = function(){
            // SnapToken is already available from the controller
            snap.pay('{{ $snap_token }}', {
                onSuccess: function(result){
                    /* You may add your own implementation here */
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        text: 'Terima kasih telah berbelanja.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.href = "{{ url('/riwayat') }}"; // Redirect to history
                    });
                },
                onPending: function(result){
                    /* You may add your own implementation here */
                    Swal.fire({
                        title: 'Pembayaran Tertunda!',
                        text: 'Silakan selesaikan pembayaran Anda.',
                        icon: 'info',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.href = "{{ url('/riwayat') }}"; // Redirect to history
                    });
                },
                onError: function(result){
                    /* You may add your own implementation here */
                    Swal.fire({
                        title: 'Pembayaran Gagal!',
                        text: 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 3000
                    });
                },
                onClose: function(){
                    /* You may add your own implementation here */
                    // User closed the pop-up without completing payment
                    Swal.fire({
                        title: 'Pembayaran Dibatalkan',
                        text: 'Anda menutup pop-up pembayaran.',
                        icon: 'warning',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        };
    @endif

    // Delete item from cart logic
    document.querySelectorAll('.delete-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.id;
            Swal.fire({
                title: 'Hapus Item?',
                text: 'Apakah Anda yakin ingin menghapus menu ini dari keranjang?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/keranjang/hapus/${itemId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload page to update cart
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message,
                                icon: 'error',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus item.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                }
            });
        });
    });
</script>
</body>
