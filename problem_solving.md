# Laporan Problem Solving: Undefined Variable pada Views

## Deskripsi Masalah
Terdapat laporan mengenai masalah **"Undefined Variable"** (variabel tidak terdefinisi) yang tersebar di lebih dari 100 baris kode pada folder `app/Views/`. 

Dalam arsitektur *Model-View-Controller* (MVC) yang digunakan oleh CodeIgniter 4, variabel-variabel disuplai secara dinamis dari Controller ke View melalui fungsi `view('nama_view', $data)`. Karena variabel (seperti `$title`, `$content`, `$leads`, dll.) disuntikkan secara dinamis saat proses rander halaman (runtime), *code editor* atau IDE (seperti VS Code dengan ekstensi PHP Intelephense / PHPStan) tidak dapat mendeteksi asal-usul variabel tersebut. 

Hal ini menyebabkan IDE memunculkan ratusan peringatan merah (*linting errors*) **"Undefined Variable"**, meskipun secara aplikasi kode tersebut akan berjalan normal tanpa error ketika dijalankan di browser (kecuali memang datanya tidak dikirim sama sekali dari controller).

## Langkah Problem Solving (Penyelesaian)

Untuk menyelesaikan masalah peringatan *linting* skala besar ini secara efisien, pendekatan manual (memperbaiki satu per satu) akan memakan waktu terlalu lama. Oleh karena itu, langkah penyelesaian otomatis (otomatisasi) dipilih:

1. **Analisis Pola Variabel**
   Saya mengecek file View dan mengidentifikasi pola bahwa variabel *undefined* yang dikeluhkan oleh IDE adalah murni dari *dependency injection* CodeIgniter.

2. **Pembuatan Script Otomatisasi (Automated Fix Script)**
   Saya membuat sebuah *script* PHP khusus (`fix_ide_vars.php`) yang memiliki alur kerja sebagai berikut:
   - Menelusuri seluruh direktori `app/Views/` dan sub-direktorinya secara rekursif.
   - Membaca setiap file berekstensi `.php`.
   - Menggunakan ekspresi reguler (*RegEx*) untuk mengekstrak seluruh penggunaan variabel (`$nama_variabel`).
   - Menyaring variabel bawaan sistem seperti `$_POST`, `$_GET`, dsb., serta variabel yang dideklarasikan langsung di dalam *loop* (seperti `foreach ($data as $item)`).
   - Menghasilkan **PHPDoc block** (`/** @var mixed $variabel */`) untuk mendefinisikan tipe variabel yang hilang.

3. **Injeksi PHPDoc secara Massal**
   *Script* tersebut kemudian menyuntikkan PHPDoc di baris teratas setiap file View yang bermasalah. Deklarasi ini memberitahu *code editor* (IDE) bahwa variabel-variabel tersebut sebenarnya "ada" dan diinjeksi dari luar file, sehingga IDE berhenti menganggapnya sebagai *error*.

4. **Eksekusi dan Validasi**
   Menjalankan *script* tersebut melalui terminal. Lebih dari 45 file View di seluruh modul (admin, resto, customer, leads, dll) telah berhasil diperbarui dan disematkan *Docblock* yang diperlukan.

## Hasil
- **Peringatan Undefined Variable Hilang:** *Tab Problems* atau *Error List* di code editor Anda (VS Code) seharusnya sekarang sudah bersih dari pesan "Undefined variable" di folder View.
- **Code Autocomplete:** Dengan adanya PHPDoc, sistem *autocomplete* pada IDE Anda akan berjalan jauh lebih mulus saat mengedit tampilan.
- **Kode Tetap Aman:** Perbaikan ini hanya memengaruhi dokumentasi IDE (komentar PHPDoc), tidak akan mengganggu performa atau alur kerja aplikasi CodeIgniter sama sekali.
