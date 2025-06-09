<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-200">
            <th class="border border-gray-300 px-4 py-2">Periode</th>
            <th class="border border-gray-300 px-4 py-2">Tanggal Bayar</th>
            <th class="border border-gray-300 px-4 py-2">Jumlah Gaji</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pembayaranGaji as $pembayaran)
            <tr>
                <td class="border border-gray-300 px-4 py-2">
                    {{ \Carbon\Carbon::parse($pembayaran->penggajian->periode)->translatedFormat('F Y') }}
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
