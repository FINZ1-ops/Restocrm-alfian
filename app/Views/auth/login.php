<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — RESTOCRM</title>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name"  content="<?= csrf_token() ?>">
    <meta name="description" content="Login ke RESTOCRM, platform CRM & digital menu ordering terbaik untuk restoran Anda.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f0c29;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            overflow: hidden;
        }

        /* Animated background blobs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: blobFloat 8s ease-in-out infinite alternate;
            pointer-events: none;
        }
        .blob-1 { width: 500px; height: 500px; background: #6366f1; top: -100px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 400px; height: 400px; background: #8b5cf6; bottom: -100px; right: -100px; animation-delay: -3s; }
        .blob-3 { width: 300px; height: 300px; background: #ec4899; top: 40%; left: 30%; animation-delay: -6s; }

        @keyframes blobFloat {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, -30px) scale(1.05); }
        }

        /* Layout */
        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left Branding Panel */
        .branding-panel {
            flex: 1;
            display: none;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }
        @media (min-width: 992px) { .branding-panel { display: flex; } }

        .branding-logo {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 48px;
        }
        .branding-logo span { 
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .branding-tagline {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .branding-desc {
            font-size: 16px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            max-width: 400px;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-bottom: 14px;
        }
        .feature-list li .icon {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Right Form Panel */
        .form-panel {
            width: 100%;
            max-width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            border-left: 1px solid rgba(255,255,255,0.08);
        }
        @media (max-width: 991px) { .form-panel { max-width: 100%; } }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .mobile-logo {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin-bottom: 32px;
            letter-spacing: -0.5px;
        }
        .mobile-logo span {
            background: linear-gradient(135deg, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @media (min-width: 992px) { .mobile-logo { display: none !important; } }

        .form-title {
            font-size: 28px;
            font-weight: 800;
            color: white;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .form-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 32px;
        }

        /* Form Controls */
        .form-floating label {
            color: rgba(255,255,255,0.4);
            font-size: 14px;
        }
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

        .input-icon {
            position: relative;
        }
        .input-icon .icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 18px;
            z-index: 5;
            pointer-events: none;
        }
        .input-icon .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 18px;
            z-index: 5;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
        .input-icon .toggle-password:hover { color: rgba(255,255,255,0.8); }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            border-radius: 12px;
            padding: 15px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99, 102, 241, 0.55);
            color: white;
            opacity: 0.95;
        }
        .btn-login:active { transform: translateY(0); }

        /* Alerts */
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

        .link-auth {
            color: #a78bfa;
            text-decoration: none;
            font-weight: 600;
        }
        .link-auth:hover { color: #c4b5fd; text-decoration: underline; }

        .error-hint { 
            color: rgba(252, 129, 129, 0.9); 
            font-size: 12px; 
            margin-top: 6px; 
            display: flex; 
            align-items: center; 
            gap: 4px; 
        }
    </style>
</head>
<body>
    <!-- Background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="auth-wrapper">
        <!-- Left Branding Panel -->
        <div class="branding-panel">
            <div class="branding-logo">RESTO<span>CRM</span></div>
            <div class="branding-tagline">Kelola Restoran<br>Lebih Cerdas.</div>
            <p class="branding-desc">Platform all-in-one untuk manajemen pesanan, CRM pelanggan, laporan penjualan, dan menu digital berbasis QR Code.</p>
            <ul class="feature-list">
                <li>
                    <span class="icon"><i class="bi bi-qr-code-scan"></i></span>
                    <span>QR Code ordering langsung dari meja pelanggan</span>
                </li>
                <li>
                    <span class="icon"><i class="bi bi-people-fill"></i></span>
                    <span>CRM terintegrasi untuk loyalitas pelanggan</span>
                </li>
                <li>
                    <span class="icon"><i class="bi bi-graph-up-arrow"></i></span>
                    <span>Laporan penjualan harian & bulanan real-time</span>
                </li>
                <li>
                    <span class="icon"><i class="bi bi-lightning-charge-fill"></i></span>
                    <span>Notifikasi dapur & kasir secara instan</span>
                </li>
            </ul>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">
            <div class="form-box">
                <div class="mobile-logo">RESTO<span>CRM</span></div>

                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

                <!-- Flash messages -->
                <?php
/**
 * @var mixed $error
 * @var mixed $oldEmail
 * @var mixed $errors
 */
if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-glass alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-glass alert-success d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= esc(session()->getFlashdata('success')) ?>
                    </div>
                <?php endif; ?>

                <!-- Inline error (from controller) -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-glass alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <?= esc($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('auth/login') ?>" novalidate>
                    <?= csrf_field() ?>

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
                    <div class="mb-4">
                        <div class="input-icon">
                            <i class="icon-left bi bi-lock"></i>
                            <div class="form-floating">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
                                <label for="password">Password</label>
                            </div>
                            <button type="button" class="toggle-password" onclick="togglePass('password', this)">
                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                            </button>
                        </div>
                        <?php if (!empty($errors['password'])): ?>
                            <div class="error-hint"><i class="bi bi-exclamation-circle"></i> <?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" id="btnLogin" class="btn btn-login mb-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Akun
                    </button>
                </form>

                <div class="divider">atau</div>

                <p class="text-center mb-0" style="color: rgba(255,255,255,0.5); font-size: 14px;">
                    Belum punya akun? <a href="/auth/register" class="link-auth">Daftar Sekarang</a>
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

    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); // tahan dulu jangan langsung submit
                            
        const btn = document.getElementById('btnLogin');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
        btn.disabled = true;
                            
        // Ambil token terbaru dari server sebelum submit
        fetch('/auth/token', { method: 'GET' })
            .then(res => res.json())
            .then(data => {
                // Update nilai input hidden CSRF dengan token terbaru
                document.querySelector('input[name="' + data.tokenName + '"]').value = data.tokenHash;
                this.submit(); // baru submit form
            })
            .catch(() => {
                // Kalau gagal fetch token, submit langsung saja
                this.submit();
            });
    });
    </script>
</body>
</html>
