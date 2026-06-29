<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — RESTOCRM</title>
    <meta name="description" content="Daftarkan akun Anda ke RESTOCRM dan mulai kelola restoran secara digital.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            overflow-x: hidden;
        }

        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: blobFloat 8s ease-in-out infinite alternate;
            pointer-events: none;
        }
        .blob-1 { width: 500px; height: 500px; background: #6366f1; top: -100px; left: -100px; }
        .blob-2 { width: 400px; height: 400px; background: #8b5cf6; bottom: -100px; right: -100px; animation-delay: -3s; }
        .blob-3 { width: 300px; height: 300px; background: #ec4899; top: 40%; left: 40%; animation-delay: -6s; }

        @keyframes blobFloat {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, -30px) scale(1.05); }
        }

        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left Branding (hidden on mobile) */
        .branding-panel {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }
        @media (min-width: 992px) { .branding-panel { display: flex; } }

        .branding-logo { font-size: 32px; font-weight: 800; letter-spacing: -1px; margin-bottom: 48px; }
        .branding-logo span {
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .branding-tagline { font-size: 38px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; letter-spacing: -1px; }
        .branding-desc { font-size: 15px; color: rgba(255,255,255,0.6); line-height: 1.7; max-width: 400px; margin-bottom: 40px; }

        .steps { list-style: none; padding: 0; margin: 0; counter-reset: step; }
        .steps li {
            counter-increment: step;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            margin-bottom: 18px;
        }
        .steps li::before {
            content: counter(step);
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: white;
            flex-shrink: 0;
        }

        /* Form Panel */
        .form-panel {
            width: 100%;
            max-width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            border-left: 1px solid rgba(255,255,255,0.08);
        }
        @media (max-width: 991px) { .form-panel { max-width: 100%; } }

        .form-box { width: 100%; max-width: 420px; }

        .mobile-logo { font-size: 24px; font-weight: 800; color: white; margin-bottom: 32px; }
        .mobile-logo span {
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @media (min-width: 992px) { .mobile-logo { display: none !important; } }

        .form-title { font-size: 28px; font-weight: 800; color: white; margin-bottom: 6px; letter-spacing: -0.5px; }
        .form-subtitle { font-size: 14px; color: rgba(255,255,255,0.5); margin-bottom: 28px; }

        .form-floating label { color: rgba(255,255,255,0.4); font-size: 14px; }
        .form-floating .form-control {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            color: white;
            padding: 16px 16px 16px 48px;
            font-size: 15px;
            height: 58px;
            transition: border-color 0.2s, background 0.2s;
        }
        .form-floating .form-control::placeholder { color: transparent; }
        .form-floating .form-control:focus {
            background: rgba(255,255,255,0.1);
            border-color: #a78bfa;
            box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.15);
            color: white;
        }
        .form-floating .form-control:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px rgba(48, 43, 99, 0.9) inset;
            -webkit-text-fill-color: white;
        }

        .input-icon { position: relative; }
        .input-icon .icon-left {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4); font-size: 18px; z-index: 5; pointer-events: none;
        }
        .input-icon .toggle-password {
            position: absolute; right: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4); font-size: 18px; z-index: 5;
            cursor: pointer; background: none; border: none; padding: 0; line-height: 1;
        }
        .input-icon .toggle-password:hover { color: rgba(255,255,255,0.8); }

        .btn-register {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 15px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99, 102, 241, 0.55);
            color: white; opacity: 0.95;
        }

        .alert-glass {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            color: white;
            font-size: 14px;
        }
        .alert-glass.alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .alert-glass.alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .error-hint {
            color: rgba(252, 129, 129, 0.9);
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .password-strength {
            height: 4px;
            border-radius: 4px;
            background: rgba(255,255,255,0.1);
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            border-radius: 4px;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }
        .strength-text {
            font-size: 11px;
            margin-top: 4px;
            color: rgba(255,255,255,0.4);
        }

        .link-auth { color: #a78bfa; text-decoration: none; font-weight: 600; }
        .link-auth:hover { color: #c4b5fd; text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.25);
            font-size: 13px;
            margin: 20px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="auth-wrapper">
        <!-- Branding Panel -->
        <div class="branding-panel">
            <div class="branding-logo">RESTO<span>CRM</span></div>
            <div class="branding-tagline">Mulai Perjalanan<br>Digital Anda.</div>
            <p class="branding-desc">Bergabunglah dengan ratusan restoran yang telah mempercayakan operasional digital mereka kepada RESTOCRM.</p>
            <ol class="steps">
                <li>Daftarkan akun Anda dalam hitungan menit</li>
                <li>Lengkapi profil dan data restoran</li>
                <li>Mulai terima pesanan melalui QR Code</li>
                <li>Pantau semua laporan dari satu dashboard</li>
            </ol>
        </div>

        <!-- Form Panel -->
        <div class="form-panel">
            <div class="form-box">
                <div class="mobile-logo">RESTO<span>CRM</span></div>

                <h1 class="form-title">Buat Akun Baru</h1>
                <p class="form-subtitle">Isi data di bawah untuk memulai pendaftaran</p>

                <?php
/**
 * @var mixed $error
 * @var mixed $errors
 * @var mixed $oldName
 * @var mixed $oldEmail
 */
if (!empty($error)): ?>
                    <div class="alert alert-glass alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-glass alert-danger mb-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <strong>Periksa kembali isian Anda:</strong>
                        </div>
                        <ul class="mb-0 ps-4 mt-1" style="font-size: 13px;">
                            <?php foreach ($errors as $e): ?>
                                <li><?= esc($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('auth/register') ?>" novalidate>
                    <?= csrf_field() ?>

                    <!-- Nama -->
                    <div class="mb-3">
                        <div class="input-icon">
                            <i class="icon-left bi bi-person"></i>
                            <div class="form-floating">
                                <input type="text" id="name" name="name" class="form-control" placeholder="Nama" value="<?= esc($oldName ?? '') ?>" required autocomplete="name">
                                <label for="name">Nama Lengkap</label>
                            </div>
                        </div>
                        <?php if (!empty($errors['name'])): ?>
                            <div class="error-hint"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <div class="input-icon">
                            <i class="icon-left bi bi-envelope"></i>
                            <div class="form-floating">
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email" value="<?= esc($oldEmail ?? '') ?>" required autocomplete="email">
                                <label for="email">Alamat Email</label>
                            </div>
                        </div>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="error-hint"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <div class="input-icon">
                            <i class="icon-left bi bi-lock"></i>
                            <div class="form-floating">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required autocomplete="new-password" oninput="checkStrength(this.value)">
                                <label for="password">Password (min. 8 karakter)</label>
                            </div>
                            <button type="button" class="toggle-password" onclick="togglePass('password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="strength-text" id="strengthText">Masukkan password</div>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error-hint"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <div class="input-icon">
                            <i class="icon-left bi bi-shield-lock"></i>
                            <div class="form-floating">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Konfirmasi" required autocomplete="new-password">
                                <label for="confirm_password">Konfirmasi Password</label>
                            </div>
                            <button type="button" class="toggle-password" onclick="togglePass('confirm_password', this)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <?php if (!empty($errors['confirm_password'])): ?>
                            <div class="error-hint"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" id="btnRegister" class="btn btn-register mb-4">
                        <i class="bi bi-person-plus-fill me-2"></i>Buat Akun Sekarang
                    </button>
                </form>

                <div class="divider">sudah punya akun?</div>

                <p class="text-center mb-0" style="color: rgba(255,255,255,0.5); font-size: 14px;">
                    <a href="/auth/login" class="link-auth"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk ke Akun Anda</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePass(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        }

        function checkStrength(val) {
            const bar = document.getElementById('strengthBar');
            const txt = document.getElementById('strengthText');
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { pct: '0%',   color: 'transparent', label: 'Masukkan password' },
                { pct: '25%',  color: '#ef4444',      label: '😕 Lemah — tambahkan angka dan simbol' },
                { pct: '50%',  color: '#f97316',      label: '🙂 Cukup — coba tambahkan huruf besar' },
                { pct: '75%',  color: '#eab308',      label: '😊 Bagus — hampir sempurna!' },
                { pct: '100%', color: '#22c55e',      label: '💪 Sangat kuat!' },
            ];

            bar.style.width = levels[score].pct;
            bar.style.background = levels[score].color;
            txt.textContent = levels[score].label;
            txt.style.color = score >= 3 ? levels[score].color : 'rgba(255,255,255,0.4)';
        }

        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btnRegister');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Membuat akun...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
