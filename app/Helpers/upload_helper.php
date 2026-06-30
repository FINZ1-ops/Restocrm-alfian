<?php

/**
 * Helper Upload
 *
 * Terapkan validasi upload gambar secara terpusat, sesuai brief:
 * "Upload gambar hanya jpg, jpeg, png, webp. Batasi ukuran upload gambar."
 *
 * Dipakai dari semua titik upload gambar di project ini (logo resto,
 * foto menu, gambar QRIS, bukti pembayaran customer) supaya aturan
 * validasinya konsisten dan tidak ditulis ulang di 4+ tempat berbeda.
 */

if (!function_exists('validate_uploaded_image')) {
    /**
     * Validasi file upload gambar (tipe & ukuran).
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     * @param int $maxSizeKb Ukuran maksimal dalam KB (default 2048 KB / 2MB)
     * @return array{valid: bool, message: string|null}
     */
    function validate_uploaded_image($file, int $maxSizeKb = 2048): array
    {
        // Tidak ada file diupload — ini valid (field opsional di banyak form)
        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return ['valid' => true, 'message' => null];
        }

        if (!$file->isValid()) {
            return ['valid' => false, 'message' => 'File upload tidak valid: ' . $file->getErrorString()];
        }

        $allowedExt  = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

        $ext = strtolower($file->getClientExtension());

        // getMimeType() bergantung pada ekstensi PHP 'fileinfo'. Jika
        // ekstensi ini tidak aktif di server (umum di sebagian setup
        // Laragon/XAMPP default), hasilnya bisa 'application/octet-stream'
        // untuk SEMUA file — termasuk gambar yang sah — yang membuat
        // validasi ini menolak upload yang sebenarnya benar.
        // Jadi: kalau MIME terdeteksi generic seperti itu, jangan langsung
        // tolak — fallback ke validasi berdasarkan ekstensi file saja.
        $mime = $file->getMimeType();
        $mimeIsGeneric = in_array($mime, ['application/octet-stream', 'text/plain', null, false], true);

        if ($mimeIsGeneric) {
            if (!in_array($ext, $allowedExt, true)) {
                return ['valid' => false, 'message' => 'Format file harus jpg, jpeg, png, atau webp.'];
            }
        } else {
            if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
                return ['valid' => false, 'message' => 'Format file harus jpg, jpeg, png, atau webp.'];
            }
        }

        $sizeKb = $file->getSize() / 1024;
        if ($sizeKb > $maxSizeKb) {
            return ['valid' => false, 'message' => "Ukuran file maksimal {$maxSizeKb} KB."];
        }

        return ['valid' => true, 'message' => null];
    }

    /**
     * Validasi lalu pindahkan file upload ke folder tujuan dengan nama acak.
     * Mengembalikan path relatif (untuk disimpan di database) jika berhasil,
     * atau melempar exception berisi pesan error jika validasi gagal.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     * @param string $destinationFolder Folder relatif di bawah public/uploads/, contoh: 'menus'
     * @param int $maxSizeKb
     * @return string|null Path relatif (contoh: 'uploads/menus/abc123.jpg'), atau null jika tidak ada file
     * @throws \RuntimeException jika file ada tapi tidak valid
     */
    function move_validated_upload($file, string $destinationFolder, int $maxSizeKb = 2048): ?string
    {
        if (!$file || !$file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $result = validate_uploaded_image($file, $maxSizeKb);
        if (!$result['valid']) {
            throw new \RuntimeException($result['message']);
        }

        $targetDir = rtrim(FCPATH, '/') . '/uploads/' . trim($destinationFolder, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = $file->getRandomName();
        $file->move($targetDir, $fileName);

        return 'uploads/' . trim($destinationFolder, '/') . '/' . $fileName;
    }
}