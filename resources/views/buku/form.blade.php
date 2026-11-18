
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Buku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f4;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background: #198754;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .kembali {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>{{ isset($buku) ? 'Edit Buku' : 'Tambah Buku' }}</h2>

    @if($errors->any())
        <div class="error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($buku) ? route('buku.update', $buku->id) : route('buku.store') }}"
          method="POST">

        @csrf

        @if(isset($buku))
            @method('PUT')
        @endif

        <label>Kode Buku</label>
        <input type="text" name="kode"
            value="{{ old('kode', $buku->kode ?? '') }}"
            maxlength="10" required>

        <label>Judul Buku</label>
        <input type="text" name="judul"
            value="{{ old('judul', $buku->judul ?? '') }}"
            required>

        <label>Kategori</label>
        <select name="kategori_id" required>
            <option value="">-- Pilih Kategori --</option>

            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}"
                    {{ old('kategori_id', $buku->kategori_id ?? '') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama }}
                </option>
            @endforeach
        </select>

        <button type="submit">
            {{ isset($buku) ? 'Update Buku' : 'Simpan Buku' }}
        </button>

        <a href="{{ route('buku.index') }}" class="kembali">
            Kembali
        </a>

    </form>

</div>

</body>
</html>
