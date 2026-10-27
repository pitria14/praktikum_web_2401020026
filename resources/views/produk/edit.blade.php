
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk UMKM</title>
</head>
<body>
    <h1>Edit Produk UMKM</h1>

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

    <form action="{{ route('produk.update', $produk['id']) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label>Kode Produk:</label><br>
            <input
                type="text"
                name="kode_produk"
                value="{{ old('kode_produk', $produk['kode_produk']) }}"
                maxlength="10"
                required
            >
        </p>

        <p>
            <label>Nama Produk:</label><br>
            <input
                type="text"
                name="nama_produk"
                value="{{ old('nama_produk', $produk['nama_produk']) }}"
                minlength="3"
                maxlength="100"
                required
            >
        </p>

        <p>
            <label>SKU:</label><br>
            <input
                type="text"
                name="sku"
                value="{{ old('sku', $produk['sku']) }}"
                minlength="5"
                maxlength="30"
                required
            >
        </p>

        <p>
            <label>Stok:</label><br>
            <input
                type="number"
                name="stok"
                value="{{ old('stok', $produk['stok']) }}"
                min="0"
                max="500"
                required
            >
        </p>

        <p>
            <label>Kategori:</label><br>
            <select name="kategori_produk_id" required>
                <option value="">Pilih kategori</option>

                @foreach ($kategoriProduk as $kategoriItem)
                    <option
                        value="{{ $kategoriItem['id'] }}"
                        {{ old('kategori_produk_id', $produk['kategori_produk_id']) == $kategoriItem['id'] ? 'selected' : '' }}
                    >
                        {{ $kategoriItem['nama_kategori'] }}
                    </option>
                @endforeach
            </select>
        </p>

        <button type="submit">Simpan Perubahan</button>
        <a href="{{ route('produk.index') }}">Kembali</a>
    </form>
</body>
</html>
