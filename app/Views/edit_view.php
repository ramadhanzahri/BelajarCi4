<div class="container p-5">
    <a href="<?= base_url('barang');?>" class="btn btn-secondary mb-2">Kembali</a>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="card-title">Edit Barang : <?= $barang->nama_barang;?></h4>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('barang/update');?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="">Nama Barang</label>
                    <input type="text" value="<?= $barang->nama_barang;?>" name="nama" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Qty</label>
                    <input type="number" value="<?= $barang->qty;?>" name="qty" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Harga Beli</label>
                    <input type="number" value="<?= $barang->harga_beli;?>" name="beli" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Harga Jual</label>
                    <input type="number" value="<?= $barang->harga_jual;?>" name="jual" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Gambar Saat Ini</label><br>
                <?php if (!empty($barang->gambar)) : ?>
                    <img src="<?= base_url('uploads/barang/'.$barang->gambar); ?>" width="120">
                <?php else : ?>
                    <small>(Belum ada gambar)</small>
                <?php endif; ?>
                </div>
                <div class="form-group">
                <label>Ganti Gambar</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <input type="hidden" value="<?= $barang->id_barang;?>" name="id_barang">
                <button class="btn btn-success">Edit Data</button>
            </form>
        </div>
    </div>
</div>