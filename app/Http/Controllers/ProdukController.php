<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;
use PDO;

class ProdukController extends Controller
{
    private function pdo(): PDO
    {
        return DB::connection()->getPdo();
    }

    private function bersihkan($nilai): string
    {
        return trim(strip_tags((string) $nilai));
    }

    private function validasiData(Request $request, $id = null)
    {
        $data = [
            'kode_produk' => $this->bersihkan($request->input('kode_produk')),
            'nama_produk' => $this->bersihkan($request->input('nama_produk')),
            'sku' => $this->bersihkan($request->input('sku')),
            'stok' => $this->bersihkan($request->input('stok')),
            'kategori_produk_id' => $this->bersihkan(
                $request->input('kategori_produk_id')
            ),
        ];

        $validator = Validator::make($data, [
            'kode_produk' => ['required', 'regex:/^[A-Za-z0-9]{10}$/'],
            'nama_produk' => ['required', 'string', 'min:3', 'max:100'],
            'sku' => ['required', 'string', 'min:5', 'max:30'],
            'stok' => ['required', 'integer', 'min:0', 'max:500'],
            'kategori_produk_id' => [
                'required',
                'integer',
                'exists:kategori_produk,id'
            ],
        ], [
            'kode_produk.regex' =>
                'Kode produk harus tepat 10 karakter alfanumerik.',
            'nama_produk.min' =>
                'Nama produk minimal 3 karakter.',
            'sku.min' =>
                'SKU minimal 5 karakter.',
            'stok.integer' =>
                'Stok harus berupa bilangan bulat.',
            'stok.min' =>
                'Stok tidak boleh kurang dari 0.',
            'stok.max' =>
                'Stok tidak boleh lebih dari 500.',
            'kategori_produk_id.exists' =>
                'Kategori yang dipilih tidak tersedia.',
        ]);

        if ($validator->fails()) {
            return [$data, $validator];
        }

        try {
            $pdo = $this->pdo();

            $sql = 'SELECT COUNT(*) FROM produk
                    WHERE (kode_produk = :kode OR sku = :sku)';

            if ($id !== null) {
                $sql .= ' AND id != :id';
            }

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':kode', $data['kode_produk']);
            $stmt->bindValue(':sku', $data['sku']);

            if ($id !== null) {
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            }

            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                $validator->errors()->add(
                    'kode_produk',
                    'Kode produk atau SKU sudah digunakan.'
                );

                return [$data, $validator];
            }

            return [$data, $validator];
        } catch (Throwable $e) {
            report($e);

            $validator->errors()->add(
                'database',
                'Data gagal diperiksa. Silakan coba lagi.'
            );

            return [$data, $validator];
        }
    }

    public function index(Request $request)
    {
        try {
            $pdo = $this->pdo();

            $cari = $this->bersihkan($request->input('cari', ''));
            $kategori = $this->bersihkan(
                $request->input('kategori', '')
            );

            $sql = 'SELECT p.*, k.nama_kategori
                    FROM produk p
                    JOIN kategori_produk k
                    ON p.kategori_produk_id = k.id
                    WHERE 1=1';

            $params = [];

            if ($cari !== '') {
                $sql .= ' AND (
                    p.kode_produk LIKE :cari1
                    OR p.nama_produk LIKE :cari2
                    OR p.sku LIKE :cari3
                )';

                $kata = '%' . $cari . '%';
                $params[':cari1'] = $kata;
                $params[':cari2'] = $kata;
                $params[':cari3'] = $kata;
            }

            if ($kategori !== '') {
                $sql .= ' AND p.kategori_produk_id = :kategori';
                $params[':kategori'] = $kategori;
            }

            $sql .= ' ORDER BY p.id ASC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmtKategori = $pdo->query(
                'SELECT * FROM kategori_produk ORDER BY nama_kategori'
            );
            $kategoriProduk = $stmtKategori->fetchAll(PDO::FETCH_ASSOC);

                        return view('produk.index', compact(
                'produk',
                'kategoriProduk',
                'cari',
                'kategori'
            ));
        } catch (Throwable $e) {
            report($e);

            return response(
                'Data produk gagal ditampilkan. Periksa file storage/logs/laravel.log untuk melihat penyebabnya.',
                500
            );
        }
    }

    public function store(Request $request)
    {
        [$data, $validator] = $this->validasiData($request);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pdo = $this->pdo();

            $stmt = $pdo->prepare(
                'INSERT INTO produk
                (kode_produk, nama_produk, sku, stok,
                 kategori_produk_id, created_at, updated_at)
                VALUES
                (:kode, :nama, :sku, :stok, :kategori, NOW(), NOW())'
            );

            $stmt->execute([
                ':kode' => $data['kode_produk'],
                ':nama' => $data['nama_produk'],
                ':sku' => $data['sku'],
                ':stok' => $data['stok'],
                ':kategori' => $data['kategori_produk_id'],
            ]);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Produk gagal ditambahkan.');
        }
    }

    public function edit($id)
    {
        try {
            $pdo = $this->pdo();

            $stmt = $pdo->prepare(
                'SELECT * FROM produk WHERE id = :id'
            );
            $stmt->execute([':id' => $id]);
            $produk = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produk) {
                return redirect()->route('produk.index')
                    ->with('error', 'Produk tidak ditemukan.');
            }

            $kategoriProduk = $pdo->query(
                'SELECT * FROM kategori_produk ORDER BY nama_kategori'
            )->fetchAll(PDO::FETCH_ASSOC);

            return view('produk.edit', compact(
                'produk',
                'kategoriProduk'
            ));
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('produk.index')
                ->with('error', 'Form edit gagal dibuka.');
        }
    }

    public function update(Request $request, $id)
    {
        [$data, $validator] = $this->validasiData($request, $id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $pdo = $this->pdo();

            $stmt = $pdo->prepare(
                'UPDATE produk SET
                    kode_produk = :kode,
                    nama_produk = :nama,
                    sku = :sku,
                    stok = :stok,
                    kategori_produk_id = :kategori,
                    updated_at = NOW()
                WHERE id = :id'
            );

            $stmt->execute([
                ':kode' => $data['kode_produk'],
                ':nama' => $data['nama_produk'],
                ':sku' => $data['sku'],
                ':stok' => $data['stok'],
                ':kategori' => $data['kategori_produk_id'],
                ':id' => $id,
            ]);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Produk gagal diperbarui.');
        }
    }

    public function destroy($id)
    {
        try {
            $pdo = $this->pdo();

            $stmt = $pdo->prepare(
                'DELETE FROM produk WHERE id = :id'
            );
            $stmt->execute([':id' => $id]);

            return redirect()->route('produk.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('produk.index')
                ->with('error', 'Produk gagal dihapus.');
        }
    }
}
