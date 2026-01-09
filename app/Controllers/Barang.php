<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Barang_model;
use CodeIgniter\Files\File;

class Barang extends Controller
{
    protected $db;
    protected $session;
    protected $request;
    protected $validation;
    protected Barang_model $barangModel;
    protected string $viewPath = 'CRUD/';
    protected string $uploadDir;

    public function __construct()
    {
        $this->db = db_connect();
        $this->session = session();
        $this->barangModel = new Barang_model();
        $this->request = \Config\Services::request();
        $this->validation = \Config\Services::validation();
        $this->uploadDir = WRITEPATH . 'uploads/barang/';
        if(!is_dir($this->uploadDir)){
            mkdir($this->uploadDir, 0755, true);
        }
        helper('form', 'url');
    }

    public function index()
    {
        $data = [
        'title' => 'Data Barang',
        'getBarang' => $this->barangModel->getBarang(),
        ];
        echo view('CRUD/header_view', $data);
        echo view('CRUD/barang_view', $data);
        echo view('CRUD/footer_view', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Data Barang',
        ];
        echo view('CRUD/header_view', $data);
        echo view('CRUD/tambah_view', $data);
        echo view('CRUD/footer_view', $data);
    }

    public function add()
    {
        $rules = [
            'nama' => 'required',
            'qty' => 'required|integer',
            'beli' => 'required|integer',
            'jual' => 'required|integer',
            'gambar' => 'if_exist|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,2048]',
        ];
        if(! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal');
        }
        $namaGambar = null;
        $fileGambar = $this->request->getFile('gambar');
        if($fileGambar && $fileGambar->isValid() && ! $fileGambar->hasMoved()) {
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move($this->uploadDir, $namaGambar);
        }

        $data = [
            'nama_barang' => $this->request->getPost('nama'),
            'qty' => $this->request->getPost('qty'),
            'harga_beli' => $this->request->getPost('beli'),
            'harga_jual' => $this->request->getPost('jual'),
            'gambar' => $namaGambar,
        ];
        $this->barangModel->saveBarang($data);
        return redirect()->to(base_url('barang'))->with('success', 'Sukses Tambah Data Barang');
    }

    public function edit($id)
    {
        $barang = $this->barangModel->getBarang($id)->getRow();
        if(! isset($barang)) {
            return redirect()->to(base_url('barang'))->with('error', 'ID barang '.$id.' Tidak ditemukan');
        }
        $data = [
            'title' => 'Edit' . $barang->nama_barang,
            'barang' => $barang,
        ];
        echo view('CRUD/header_view', $data);
        echo view('CRUD/edit_view', $data);
        echo view('CRUD/footer_view', $data);
    }

    public function update()
    {
        $rules = [
            'id_barang' => 'required|integer',
            'nama' => 'required',
            'qty' => 'required|integer',
            'beli' => 'required|integer',
            'jual' => 'required|integer',
            'gambar' => 'if_exist|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,2048]',
        ];
        if(! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal');
        }

        $id = $this->request->getPost('id_barang');
        $barangLama = $this->barangModel->getBarang($id)->getRow();
        if(! isset($barangLama)) {
            return redirect()->to(base_url('barang'))->with('error', 'ID barang '.$id.' Tidak ditemukan');
        }

        $gambarLama = $barangLama->gambar ?? null;
        $namaGambar = $gambarLama;
        
        $fileGambar = $this->request->getFile('gambar');
        if($fileGambar && $fileGambar->isValid() && ! $fileGambar->hasMoved()) {
            $namaGambar = $fileGambar->getRandomName();
            $fileGambar->move($this->uploadDir, $namaGambar);
            if(! empty($gambarLama)) {
                $pathLama = $this->uploadDir . $gambarLama;
                if(is_file($pathLama)) {
                    unlink($pathLama);
                }
            }
        }
        $data = [
            'nama_barang' => $this->request->getPost('nama'),
            'qty' => $this->request->getPost('qty'),
            'harga_beli' => $this->request->getPost('beli'),
            'harga_jual' => $this->request->getPost('jual'),
            'gambar' => $namaGambar,
        ];
        $this->barangModel->editBarang($data, $id);
        return redirect()->to(base_url('barang'))->with('success', 'Sukses Update Data Barang');
    }

    public function hapus($id)
    {
        $barang = $this->barangModel->getBarang($id)->getRow();
        if(! isset($barang)) {
            return redirect()->to(base_url('barang'))->with('error', 'ID barang '.$id.' Tidak ditemukan');
        }
        if(! empty($barang->gambar)) {
            $path = $this->uploadDir . $barang->gambar;
            if(is_file($path)) {
                unlink($path);
            }
        }
        $this->barangModel->hapusBarang($id);
        return redirect()->to(base_url('barang'))->with('success', 'Sukses Hapus Data Barang');
    }

    public function gambar($filename)
    {
        $filename = basename($filename);
        $path = $this->uploadDir . $filename;
        if(! is_file($path)) {
            return $this->response->setStatusCode(404, 'File tidak ditemukan');
        }
        $file = new File($path);
        return $this->response->setHeader('Content-Type', $file->getMimeType())->setHeader('Content-Length', $file->getSize())->setBody(file_get_contents($path));
    }
}
