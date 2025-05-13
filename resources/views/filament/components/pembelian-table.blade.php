<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-200">
            <th class="border border-gray-300 px-4 py-2">No Faktur</th>
            <th class="border border-gray-300 px-4 py-2">Vendor</th>
            <th class="border border-gray-300 px-4 py-2">Tanggal</th>
            <th class="border border-gray-300 px-4 py-2">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border border-gray-300 px-4 py-2">{{ $pembelian->no_faktur_pembelian }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $pembelian->vendor->nama_vendor }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $pembelian->tanggal }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ rupiah($pembelian->total_tagihan, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
