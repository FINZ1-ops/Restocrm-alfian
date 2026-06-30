<?php
/**
 * @var string|null $name
 * @var string|null $email
 * @var string|null $role
 */
/**
 * View: settings/index.php
 * Halaman Settings sederhana — menampilkan info dasar akun yang login.
 * Placeholder; dapat dikembangkan nanti (misal ganti password) tanpa
 * mengubah route atau dropdown yang memanggilnya.
 */
ob_start(); ?>

<div class="mb-4">
    <h4 class="fw-bold">Settings</h4>
    <p class="text-muted">Informasi akun Anda yang sedang login.</p>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted" style="width: 160px;">Nama</td>
                <td class="fw-semibold"><?= esc($name ?? '-') ?></td>
            </tr>
            <tr>
                <td class="text-muted">Email</td>
                <td class="fw-semibold"><?= esc($email ?? '-') ?></td>
            </tr>
            <tr>
                <td class="text-muted">Role</td>
                <td class="fw-semibold"><?= esc(ucwords(str_replace('_', ' ', $role ?? ''))) ?></td>
            </tr>
        </table>
    </div>
</div>

<p class="text-muted small mt-3">Pengaturan lanjutan akan tersedia di versi berikutnya.</p>

<?= ob_get_clean() ?>