<form action="{{ route('absensi.store') }}" method="POST">
    @csrf
    <label>Pegawai:</label>
    <select name="pegawai_id">
        @foreach($pegawai as $p)
            <option value="{{ $p->id }}">{{ $p->nama }}</option>
        @endforeach
    </select>

    <label>No Absensi:</label>
    <input type="text" name="no_absensi">

    <label>Status:</label>
    <select name="status">
        <option value="Hadir">Hadir</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
    </select>

    <label>Tanggal:</label>
    <input type="datetime-local" name="tgl">

    <button type="submit">Simpan</button>
</form>
