<?php 
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Barang_model;

class Barang extends Controller
{
    private $uploadPath = 'uploads/barang/';
    public function index()
    {
        $model = new Barang_model;
        $data['title']     = 'Data Barang';
        $data['getBarang'] = $model->getBarang();
        echo view('header_view', $data);
        echo view('barang_view', $data);
        echo view('footer_view', $data);
    }

    public function tambah()
    {
        $data['title']     = 'Tambah Data Barang';
        echo view('header_view', $data);
        echo view('tambah_view', $data);
        echo view('footer_view', $data);
    }

    public function add()
{
    $model = new Barang_model;

    // default: tidak ada gambar
    $namaGambar = null;

    $fileGambar = $this->request->getFile('gambar'); // name="gambar" di form [web:94]
    if ($fileGambar && $fileGambar->isValid() && ! $fileGambar->hasMoved()) {
        $namaGambar = $fileGambar->getRandomName(); // nama acak [web:105]
        $fileGambar->move($this->uploadPath, $namaGambar); // pindahkan file [web:105]
    }

    $data = array(
        'nama_barang' => $this->request->getPost('nama'),
        'qty' => $this->request->getPost('qty'),
        'harga_beli' => $this->request->getPost('beli'),
        'harga_jual' => $this->request->getPost('jual'),
        'gambar' => $namaGambar,
    );

    $model->saveBarang($data);

    echo '<script>
            alert("Sukses Tambah Data Barang");
            window.location="'.base_url('barang').'"
        </script>';
}


    public function edit($id)
    {
        $model = new Barang_model;
        $getBarang = $model->getBarang($id)->getRow();
        if(isset($getBarang))
        {
            $data['barang'] = $getBarang;
            $data['title']  = 'Edit '.$getBarang->nama_barang;

            echo view('header_view', $data);
            echo view('edit_view', $data);
            echo view('footer_view', $data);

        }else{

            echo '<script>
                    alert("ID barang '.$id.' Tidak ditemukan");
                    window.location="'.base_url('barang').'"
                </script>';
        }
    }

    public function update()
{
    $model = new Barang_model;
    $id = $this->request->getPost('id_barang');

    // ambil data lama untuk tahu nama file gambar sebelumnya
    $barangLama = $model->getBarang($id)->getRow();
    $gambarLama = isset($barangLama->gambar) ? $barangLama->gambar : null;

    $namaGambar = $gambarLama; // default tetap pakai gambar lama

    $fileGambar = $this->request->getFile('gambar'); // [web:94]
    if ($fileGambar && $fileGambar->isValid() && ! $fileGambar->hasMoved()) {
        $namaGambar = $fileGambar->getRandomName(); // [web:105]
        $fileGambar->move($this->uploadPath, $namaGambar); // [web:105]

        // hapus file lama (kalau ada)
        if (!empty($gambarLama) && file_exists($this->uploadPath . $gambarLama)) {
            unlink($this->uploadPath . $gambarLama);
        }
    }

    $data = array(
        'nama_barang' => $this->request->getPost('nama'),
        'qty' => $this->request->getPost('qty'),
        'harga_beli' => $this->request->getPost('beli'),
        'harga_jual' => $this->request->getPost('jual'),
        'gambar' => $namaGambar,
    );

    $model->editBarang($data, $id);

    echo '<script>
            alert("Sukses Edit Data Barang");
            window.location="'.base_url('barang').'"
        </script>';
}


    public function hapus($id)
{
    $model = new Barang_model;
    $getBarang = $model->getBarang($id)->getRow();

    if (isset($getBarang)) {
        // hapus file gambar dulu (kalau ada)
        if (!empty($getBarang->gambar) && file_exists($this->uploadPath . $getBarang->gambar)) {
            unlink($this->uploadPath . $getBarang->gambar);
        }

        $model->hapusBarang($id);

        echo '<script>
                alert("Sukses Hapus Data Barang");
                window.location="'.base_url('barang').'"
            </script>';
    } else {
        echo '<script>
                alert("ID barang '.$id.' Tidak ditemukan");
                window.location="'.base_url('barang').'"
            </script>';
    }
}

}