<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — <?= esc($restaurant['name']) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --accent: #4f46e5;
            --accent-light: #818cf8;
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --safe-bottom: env(safe-area-inset-bottom, 20px);
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-color);
            color: var(--text-main);
            padding-bottom: calc(100px + var(--safe-bottom));
            -webkit-font-smoothing: antialiased;
        }

        /* ── Header restoran ── */
        .resto-header {
            background: linear-gradient(135deg, var(--accent), #3730a3);
            color: #fff;
            padding: 28px 24px 48px;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
            position: relative;
        }
        .resto-name { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .resto-meta { font-size: 13px; opacity: 0.9; margin-top: 6px; font-weight: 500; }
        .table-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 700;
            margin-top: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* ── Tab kategori sticky ── */
        .category-tabs {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            display: flex;
            overflow-x: auto;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
            padding: 4px 8px;
            margin: -24px 16px 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }
        .category-tabs::-webkit-scrollbar { display: none; }
        .tab-item {
            flex-shrink: 0;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 12px;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 4px;
        }
        .tab-item.active {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transform: translateY(-1px);
        }

        /* ── Section kategori ── */
        .cat-section { padding: 0 16px; margin-top: 24px; }
        .cat-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            padding: 16px 4px 12px;
            letter-spacing: -0.3px;
        }

        /* ── Kartu menu ── */
        .menu-grid { display: flex; flex-direction: column; gap: 14px; margin-bottom: 8px; }
        .menu-card {
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(226, 232, 240, 0.5);
        }
        .menu-card:active {
            transform: scale(0.98);
        }
        .menu-img {
            width: 90px; height: 90px;
            border-radius: 14px;
            object-fit: cover;
            flex-shrink: 0;
            background: #f1f5f9;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .menu-img-placeholder {
            width: 90px; height: 90px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            flex-shrink: 0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .menu-info { flex: 1; min-width: 0; }
        .menu-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }
        .menu-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Badge label menu */
        .menu-label {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 8px;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .label-best_seller { background: #fef9c3; color: #a16207; }
        .label-promo       { background: #fee2e2; color: #dc2626; }
        .label-rekomendasi { background: #dbeafe; color: #1d4ed8; }
        .label-baru        { background: #dcfce7; color: #15803d; }

        .menu-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--accent);
            margin-top: 8px;
        }

        /* Kontrol jumlah item per kartu */
        .qty-control {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
            background: var(--bg-color);
            border-radius: 24px;
            padding: 4px;
        }
        .qty-btn {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-minus { background: #fff; color: var(--text-muted); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .btn-minus.active { color: #dc2626; }
        .btn-plus  { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3); }
        .btn-plus:active, .btn-minus.active:active { transform: scale(0.85); }
        .qty-num   { min-width: 24px; text-align: center; font-size: 15px; font-weight: 700; color: var(--text-main); }

        /* ── Cart bar (Floating Pill) ── */
        .cart-bar {
            position: fixed;
            bottom: calc(16px + var(--safe-bottom));
            left: 16px; right: 16px;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            z-index: 50;
            cursor: pointer;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.1);
            transform: translateY(150px) scale(0.9);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .cart-bar.show { 
            transform: translateY(0) scale(1); 
            opacity: 1;
        }
        .cart-bar:active { transform: scale(0.97); }
        .cart-count {
            background: var(--accent);
            color: #fff;
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-weight: 800;
            margin-right: 12px;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4);
        }
        .cart-label { font-size: 15px; font-weight: 600; letter-spacing: 0.2px; }
        .cart-total { font-size: 16px; font-weight: 800; margin-right: 4px; }
        .cart-arrow { 
            background: rgba(255,255,255,0.15);
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; 
            margin-left: 12px;
        }
        
        /* Animasi pop ketika qty bertambah */
        @keyframes pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .pop-anim { animation: pop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    </style>
</head>
<body>

<!-- Header Restoran -->
<div class="resto-header">
    <div class="resto-name"><?= esc($restaurant['name']) ?></div>
    <?php
/**
 * @var mixed $restaurant
 * @var mixed $table
 * @var mixed $categories
 * @var mixed $menusByCategory
 * @var mixed $cart
 */
if (!empty($restaurant['opening_hours'])): ?>
        <div class="resto-meta">⏰ <?= esc($restaurant['opening_hours']) ?></div>
    <?php endif; ?>
    <span class="table-badge">
        📍 Meja <?= esc($table['table_number']) ?> · <?= esc($table['area_name']) ?>
    </span>
</div>

<!-- Tab Kategori -->
<div class="category-tabs" id="categoryTabs">
    <?php foreach ($categories as $idx => $cat): ?>
    <div class="tab-item <?= $idx === 0 ? 'active' : '' ?>"
         onclick="scrollToCategory('cat-<?= $cat['id'] ?>', this)">
        <?= esc($cat['name']) ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- Daftar Menu per Kategori -->
<?php foreach ($categories as $cat): ?>
    <?php $catMenus = $menusByCategory[$cat['id']] ?? []; ?>
    <?php if (empty($catMenus)) continue; ?>

    <div class="cat-section" id="cat-<?= $cat['id'] ?>">
        <div class="cat-title"><?= esc($cat['name']) ?></div>
        <div class="menu-grid">
            <?php foreach ($catMenus as $menu): ?>
            <div class="menu-card" id="menu-card-<?= $menu['id'] ?>">
                <!-- Gambar menu -->
                <?php if (!empty($menu['image'])): ?>
                    <img src="/<?= esc($menu['image']) ?>" class="menu-img" alt="<?= esc($menu['name']) ?>">
                <?php else: ?>
                    <div class="menu-img-placeholder">🍽️</div>
                <?php endif; ?>

                <!-- Info menu -->
                <div class="menu-info">
                    <div class="menu-name"><?= esc($menu['name']) ?></div>
                    <?php if (!empty($menu['description'])): ?>
                        <div class="menu-desc"><?= esc($menu['description']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($menu['label']) && $menu['label'] !== 'biasa'): ?>
                        <div class="menu-label label-<?= esc($menu['label']) ?>">
                            <?= esc(str_replace('_', ' ', $menu['label'])) ?>
                        </div>
                    <?php endif; ?>
                    <div class="menu-price">Rp <?= number_format((float)$menu['price'], 0, ',', '.') ?></div>
                </div>

                <!-- Kontrol qty -->
                <div class="qty-control">
                    <?php
                    // Cek apakah item ini sudah ada di cart
                    $currentQty = isset($cart[$menu['id']]) ? (int)$cart[$menu['id']]['quantity'] : 0;
                    ?>
                    <?php if ($currentQty > 0): ?>
                        <button class="qty-btn btn-minus active"
                                onclick="updateCart(<?= $menu['id'] ?>, <?= $table['id'] ?>, 'dec')">−</button>
                        <span class="qty-num" id="qty-<?= $menu['id'] ?>"><?= $currentQty ?></span>
                        <button class="qty-btn btn-plus"
                                onclick="updateCart(<?= $menu['id'] ?>, <?= $table['id'] ?>, 'inc')">+</button>
                    <?php else: ?>
                        <button class="qty-btn btn-minus"
                                onclick="updateCart(<?= $menu['id'] ?>, <?= $table['id'] ?>, 'dec')" style="opacity:0;pointer-events:none">−</button>
                        <span class="qty-num" id="qty-<?= $menu['id'] ?>">0</span>
                        <button class="qty-btn btn-plus"
                                onclick="updateCart(<?= $menu['id'] ?>, <?= $table['id'] ?>, 'inc')">+</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<!-- Cart Bar (muncul saat ada item) -->
<div class="cart-bar" id="cartBar" onclick="goToCheckout()">
    <div style="display:flex;align-items:center">
        <div class="cart-count" id="cartCount">0</div>
        <span class="cart-label">Lihat Pesanan</span>
    </div>
    <div style="display:flex;align-items:center">
        <span class="cart-total" id="cartTotal">Rp 0</span>
        <span class="cart-arrow">→</span>
    </div>
</div>

<script>
// State cart di memory (sinkron dengan session via API)
const TABLE_ID   = <?= $table['id'] ?>;
const BASE_URL   = "<?= base_url() ?>";

// Inisialisasi dari data cart yang sudah ada di session (dikirim PHP)
let cartData = <?= json_encode(array_values($cart)) ?>;

// Hitung total & jumlah item dari cartData
function calcCart() {
    let count = 0, total = 0;
    cartData.forEach(i => { count += i.quantity; total += i.subtotal; });
    return { count, total };
}

// Render cart bar
function renderCartBar() {
    const { count, total } = calcCart();
    const bar = document.getElementById('cartBar');
    const countEl = document.getElementById('cartCount');
    
    countEl.textContent = count;
    document.getElementById('cartTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    if (count > 0) {
        bar.classList.add('show');
        // Trigger pop animation on cart count
        countEl.classList.remove('pop-anim');
        void countEl.offsetWidth; // trigger reflow
        countEl.classList.add('pop-anim');
    } else {
        bar.classList.remove('show');
    }
}

// Update qty satu item via AJAX
async function updateCart(menuId, tableId, action) {
    const qtyEl = document.getElementById('qty-' + menuId);
    const current = parseInt(qtyEl.textContent) || 0;

    // action: 'inc' tambah, 'dec' kurang
    const quantity = action === 'inc' ? 1 : -1;
    const postAction = (current + quantity <= 0) ? 'remove' : 'set';
    const newQty = Math.max(0, current + quantity);

    // Optimistic UI — update langsung
    qtyEl.textContent = newQty;
    qtyEl.classList.remove('pop-anim');
    void qtyEl.offsetWidth; // trigger reflow
    qtyEl.classList.add('pop-anim');
    
    updateCardButtons(menuId, newQty);

    try {
        const fd = new FormData();
        fd.append('table_id',  tableId);
        fd.append('menu_id',   menuId);
        fd.append('quantity',  Math.abs(newQty));
        fd.append('action',    postAction);
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        const res  = await fetch(BASE_URL + '/order/add-item', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            // Sinkronkan cart lokal dengan server
            cartData = Object.values(data.cart);
            renderCartBar();
        }
    } catch (e) {
        // Jika gagal, rollback UI
        qtyEl.textContent = current;
        updateCardButtons(menuId, current);
    }
}

// Update tampilan tombol +/- berdasarkan qty
function updateCardButtons(menuId, qty) {
    const card = document.getElementById('menu-card-' + menuId);
    if (!card) return;
    const minus = card.querySelector('.btn-minus');
    const plus  = card.querySelector('.btn-plus');
    if (minus) {
        minus.style.opacity = qty > 0 ? '1' : '0.5';
        minus.style.pointerEvents = qty > 0 ? 'auto' : 'none';
        minus.classList.toggle('active', qty > 0);
    }
}

// Scroll smooth ke section kategori saat klik tab
function scrollToCategory(id, tabEl) {
    // Tandai tab aktif
    document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
    tabEl.classList.add('active');

    const el = document.getElementById(id);
    if (el) {
        // Offset dari sticky header
        const offset = document.getElementById('categoryTabs').offsetHeight + 16;
        const top = el.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
    }
}

// Navigasi ke halaman checkout
function goToCheckout() {
    window.location.href = BASE_URL + '/order/checkout/' + TABLE_ID;
}

// Init
renderCartBar();
</script>
</body>
</html>
