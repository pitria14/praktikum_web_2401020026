 
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Produk UMKM</title>
</head>
<body>
    <h1>Sistem Data Produk UMKM</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p>{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Tambah Produk</h2>

    <form action="{{ route('produk.store') }}" method="POST">
        @csrf

        <p>
            <label>Kode Produk:</label><br>
            <input type="text" name="kode_produk"
                value="{{ old('kode_produk') }}" required>
        </p>

        <p>
            <label>Nama Produk:</label><br>
            <input type="text" name="nama_produk"
                value="{{ old('nama_produk') }}" required>
        </p>

        <p>
            <label>SKU:</label><br>
            <input type="text" name="sku"
                value="{{ old('sku') }}" required>
        </p>

        <p>
            <label>Stok:</label><br>
            <input type="number" name="stok"
                min="0" max="500" value="{{ old('stok') }}" required>
        </p>

        <p>
            <label>Kategori:</label><br>
            <select name="kategori_produk_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategoriProduk as $kat)
                    <option value="{{ $kat['id'] }}"
                        {{ old('kategori_produk_id') == $kat['id'] ? 'selected' : '' }}>
                        {{ $kat['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Tambah Produk</button>
    </form>

    <hr>

    <h2>Daftar Produk</h2>

    <form action="{{ route('produk.index') }}" method="GET">
        <label>Cari kode, nama, atau SKU:</label>
        <input type="text" name="cari" value="{{ $cari }}">

        <label>Filter Kategori:</label>
        <select name="kategori">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriProduk as $kat)
                <option value="{{ $kat['id'] }}"
                    {{ $kategori == $kat['id'] ? 'selected' : '' }}>
                    {{ $kat['nama_kategori'] }}
                </option>
            @endforeach
        </select>

        <button type="submit">Cari / Filter</button>
        <a href="{{ route('produk.index') }}">Reset</a>
    </form>

    <br>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>SKU</th>
                <th>Stok</th>
                <th>Kategori</th>
                <th>Status Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($produk as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['kode_produk'] }}</td>
                    <td>{{ $item['nama_produk'] }}</td>
                    <td>{{ $item['sku'] }}</td>
                    <td>{{ $item['stok'] }}</td>
                    <td>{{ $item['nama_kategori'] }}</td>
                    <td>{{ statusStok($item['stok']) }}</td>
                    <td>
                        <a href="{{ route('produk.edit', $item['id']) }}">Edit</a>

                        <form action="{{ route('produk.destroy', $item['id']) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Data produk tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
