<?php
/**
 * SCRIPT DIAGNOSTIK SEMENTARA — bukan bagian dari aplikasi RESTOCRM.
 *
 * Tujuan: cari tahu persis kenapa upload logo "diam saja" tidak tersimpan.
 * Taruh file ini di: D:\laragon\www\restocrm_fixed\public\debug_upload.php
 * Lalu buka di browser: http://restocrm_fixed.test/debug_upload.php
 *
 * SETELAH SELESAI DEBUGGING, HAPUS FILE INI — jangan ikut di-commit ke git
 * atau dibawa ke server production, karena bisa expose info sensitif.
 */
?>
<!DOCTYPE html>
<html>
<head><title>Debug Upload</title></head>
<body style="font-family: monospace; padding: 20px;">

<h2>1. Cek ekstensi PHP yang aktif</h2>
<p>fileinfo extension aktif? <b><?= extension_loaded('fileinfo') ? 'YA ✅' : 'TIDAK ❌ (INI KEMUNGKINAN PENYEBABNYA)' ?></b></p>
<p>upload_max_filesize: <b><?= ini_get('upload_max_filesize') ?></b></p>
<p>post_max_size: <b><?= ini_get('post_max_size') ?></b></p>

<hr>

<h2>2. Form test upload</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" accept="image/*">
    <button type="submit">Upload &amp; Cek</button>
</form>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<hr>
<h2>3. Hasil $_FILES mentah (PHP native, tanpa CodeIgniter)</h2>
<pre><?php print_r($_FILES); ?></pre>

<?php if (!empty($_FILES['test_file']['tmp_name']) && is_uploaded_file($_FILES['test_file']['tmp_name'])): ?>
<h2>4. Deteksi MIME pakai beberapa cara berbeda</h2>
<?php
$tmpPath = $_FILES['test_file']['tmp_name'];

echo "<p>Dari \$_FILES['type'] (TIDAK BISA DIPERCAYA, ini cuma kiriman browser): <b>" . $_FILES['test_file']['type'] . "</b></p>";

if (function_exists('mime_content_type')) {
    echo "<p>Dari mime_content_type(): <b>" . mime_content_type($tmpPath) . "</b></p>";
} else {
    echo "<p>mime_content_type() TIDAK TERSEDIA</p>";
}

if (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    echo "<p>Dari finfo class: <b>" . $finfo->file($tmpPath) . "</b></p>";
} else {
    echo "<p>Class finfo TIDAK TERSEDIA ❌ (ini akar masalah getMimeType() CodeIgniter)</p>";
}

$ext = strtolower(pathinfo($_FILES['test_file']['name'], PATHINFO_EXTENSION));
echo "<p>Ekstensi file dari nama: <b>{$ext}</b></p>";
?>

<h2>5. Coba pindahkan file secara manual (tanpa CodeIgniter)</h2>
<?php
$targetDir = __DIR__ . '/uploads/debug_test';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}
$targetFile = $targetDir . '/' . basename($_FILES['test_file']['name']);

if (move_uploaded_file($tmpPath, $targetFile)) {
    echo "<p style='color:green'><b>BERHASIL dipindahkan ke: {$targetFile}</b></p>";
    echo "<img src='uploads/debug_test/" . basename($_FILES['test_file']['name']) . "' style='max-width:200px'>";
} else {
    echo "<p style='color:red'><b>GAGAL move_uploaded_file(). Cek permission folder: {$targetDir}</b></p>";
}
?>
<?php else: ?>
<p style="color:red">File tidak terupload dengan benar di level PHP — cek php.ini atau permission folder temp.</p>
<?php endif; ?>
<?php endif; ?>

</body>
</html>
