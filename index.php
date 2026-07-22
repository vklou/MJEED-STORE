<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT id, name, duration, price, is_featured FROM products WHERE is_active = 1 ORDER BY id");
$dbProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MJEEDSTORE – يوتيوب بريميوم</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
<style>
  :root { --blue: #1a73e8; --dark: #0d1b2e; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Tajawal', sans-serif; background: #f5f8fc; color: var(--dark); }
  header { background: #fff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 12px rgba(0,0,0,0.06); position: sticky; top: 0; z-index: 50; }
  .logo { font-size: 1.5rem; font-weight: 900; color: var(--blue); }
  .header-btns button { margin-right: 10px; padding: 9px 18px; border-radius: 50px; border: none; cursor: pointer; font-family: 'Tajawal'; font-weight: 700; }
  .track-btn { background: transparent; border: 1.5px solid var(--blue) !important; color: var(--blue); }
  .cart-btn { background: var(--blue); color: #fff; }
  .hero { background: linear-gradient(135deg, #0d47a1, #1a73e8); color: #fff; text-align: center; padding: 70px 20px; }
  .hero h1 { font-size: 2.4rem; margin-bottom: 12px; }
  .products { max-width: 1100px; margin: 50px auto; padding: 0 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
  .card { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); position: relative; }
  .card.featured { background: linear-gradient(160deg, #1a73e8, #0d47a1); color: #fff; }
  .badge { position: absolute; top: 16px; left: 16px; background: #FFD700; color: #000; font-size: 0.75rem; font-weight: 900; padding: 4px 12px; border-radius: 50px; }
  .price { font-size: 2rem; font-weight: 900; margin: 16px 0; }
  .btn { width: 100%; padding: 13px; border: none; border-radius: 10px; background: var(--blue); color: #fff; font-weight: 700; cursor: pointer; font-family: 'Tajawal'; }
  .card.featured .btn { background: #fff; color: #0d47a1; }
  footer { background: var(--dark); color: #aaa; text-align: center; padding: 30px 20px; margin-top: 60px; font-size: 0.9rem; }
  footer a { color: #60b3f0; text-decoration: none; }
  .modal { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: none; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
  .modal.open { display: flex; }
  .modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 440px; max-height: 90vh; overflow-y: auto; padding: 28px; }
  input { width: 100%; padding: 12px; margin: 8px 0 16px; border: 1.5px solid #ddd; border-radius: 8px; font-family: 'Tajawal'; }
  .toast { position: fixed; bottom: 24px; right: 24px; background: #1b5e20; color: #fff; padding: 14px 22px; border-radius: 8px; display: none; z-index: 200; }
</style>
</head>
<body>

<header>
  <div class="logo">MJEEDSTORE</div>
  <div class="header-btns">
    <button class="track-btn" onclick="openTrack()">تتبع الطلب</button>
    <button class="cart-btn" onclick="openCart()">السلة (<span id="cart-count">0</span>)</button>
  </div>
</header>

<section class="hero">
  <h1>يوتيوب بريميوم</h1>
  <p>اشتراك أصلي يُضاف على حسابك مباشرة</p>
</section>

<div class="products" id="products"></div>

<footer>
  <p><strong style="color:#fff;">MJEEDSTORE</strong> – +966 508 071 671</p>
  <p style="margin-top:10px;">
    <a href="about.php">عن المطور</a> &nbsp;|&nbsp;
    <a href="login.php">لوحة الإدارة</a>
  </p>
</footer>

<!-- Cart -->
<div class="modal" id="cart-modal">
  <div class="modal-box">
    <h3>السلة</h3>
    <div id="cart-items" style="margin:20px 0;"></div>
    <button class="btn" onclick="goCheckout()">إتمام الطلب</button>
    <button onclick="closeCart()" style="width:100%;margin-top:10px;padding:10px;border:1px solid #ddd;background:#fff;border-radius:8px;">إغلاق</button>
  </div>
</div>

<!-- Checkout -->
<div class="modal" id="checkout-modal">
  <div class="modal-box">
    <h3>إتمام الطلب</h3>
    <div id="checkout-summary" style="margin:16px 0;"></div>
    <input type="email" id="email" placeholder="البريد الإلكتروني" dir="ltr">
    <input type="tel" id="phone" placeholder="رقم الجوال" dir="ltr">
    <input type="file" id="receipt" accept="image/*,.pdf">
    <button class="btn" id="submit-btn" onclick="submitOrder()">تأكيد الطلب</button>
    <button onclick="closeCheckout()" style="width:100%;margin-top:10px;padding:10px;border:1px solid #ddd;background:#fff;border-radius:8px;">إلغاء</button>
  </div>
</div>

<!-- Success -->
<div class="modal" id="success-modal">
  <div class="modal-box" style="text-align:center;">
    <div style="font-size:3.5rem;">🎉</div>
    <h3>تم استلام طلبك</h3>
    <div id="order-num" style="font-size:1.6rem;font-weight:900;margin:16px 0;"></div>
    <button class="btn" onclick="closeSuccess()">حسناً</button>
  </div>
</div>

<!-- Track -->
<div class="modal" id="track-modal">
  <div class="modal-box">
    <h3>تتبع الطلب</h3>
    <input type="text" id="track-input" placeholder="رقم الطلب" dir="ltr">
    <button class="btn" onclick="trackOrder()">بحث</button>
    <div id="track-result" style="margin-top:16px;"></div>
    <button onclick="closeTrack()" style="width:100%;margin-top:12px;padding:10px;border:1px solid #ddd;background:#fff;border-radius:8px;">إغلاق</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
let products = <?= json_encode($dbProducts, JSON_UNESCAPED_UNICODE) ?>;
let cart = [];
let selectedFile = null;

function renderProducts() {
  document.getElementById('products').innerHTML = products.map(p => `
    <div class="card ${p.is_featured == 1 ? 'featured' : ''}">
      ${p.is_featured == 1 ? '<div class="badge">الأكثر مبيعاً</div>' : ''}
      <div style="font-size:1.2rem;font-weight:700;">${p.name}</div>
      <div style="opacity:0.8;margin-top:4px;">${p.duration}</div>
      <div class="price">${p.price} ر.س</div>
      <button class="btn" onclick="addToCart(${p.id})">أضف للسلة</button>
    </div>
  `).join('');
}

function addToCart(id) {
  const p = products.find(x => x.id == id);
  const existing = cart.find(c => c.id == id);
  if (existing) existing.qty++;
  else cart.push({ ...p, qty: 1 });
  updateCartCount();
  showToast('تمت الإضافة للسلة');
}

function updateCartCount() {
  document.getElementById('cart-count').textContent = cart.reduce((s, c) => s + c.qty, 0);
}

function openCart() {
  const total = cart.reduce((s, c) => s + c.price * c.qty, 0);
  document.getElementById('cart-items').innerHTML = cart.length === 0
    ? '<p style="text-align:center;color:#888;">السلة فارغة</p>'
    : cart.map(c => `<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #eee;">${c.name} (${c.duration}) × ${c.qty}<strong>${c.price * c.qty} ر.س</strong></div>`).join('') +
      `<div style="font-weight:900;margin-top:16px;font-size:1.1rem;">الإجمالي: ${total} ر.س</div>`;
  document.getElementById('cart-modal').classList.add('open');
}
function closeCart() { document.getElementById('cart-modal').classList.remove('open'); }

function goCheckout() {
  closeCart();
  const total = cart.reduce((s, c) => s + c.price * c.qty, 0);
  document.getElementById('checkout-summary').innerHTML = cart.map(c =>
    `<div style="display:flex;justify-content:space-between;margin-bottom:6px;">${c.name} × ${c.qty}<span>${c.price * c.qty} ر.س</span></div>`
  ).join('') + `<div style="font-weight:900;margin-top:10px;">الإجمالي: ${total} ر.س</div>`;
  document.getElementById('checkout-modal').classList.add('open');
}
function closeCheckout() { document.getElementById('checkout-modal').classList.remove('open'); }

document.getElementById('receipt').addEventListener('change', e => selectedFile = e.target.files[0]);

function submitOrder() {
  const email = document.getElementById('email').value.trim();
  const phone = document.getElementById('phone').value.trim();
  if (!email || !phone || !selectedFile) { showToast('املأ كل الحقول'); return; }

  const btn = document.getElementById('submit-btn');
  btn.disabled = true; btn.textContent = 'جاري الإرسال...';

  const total = cart.reduce((s, c) => s + c.price * c.qty, 0);
  const formData = new FormData();
  formData.append('email', email);
  formData.append('phone', phone);
  formData.append('total', total);
  formData.append('items', JSON.stringify(cart));
  formData.append('receipt', selectedFile);

  fetch('order.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false; btn.textContent = 'تأكيد الطلب';
      if (data.success) {
        cart = []; updateCartCount();
        closeCheckout();
        document.getElementById('order-num').textContent = '#' + data.order_number;
        document.getElementById('success-modal').classList.add('open');
      } else showToast(data.message || 'خطأ');
    })
    .catch(() => { btn.disabled = false; btn.textContent = 'تأكيد الطلب'; showToast('خطأ في الاتصال'); });
}

function closeSuccess() { document.getElementById('success-modal').classList.remove('open'); }
function openTrack() { document.getElementById('track-modal').classList.add('open'); }
function closeTrack() { document.getElementById('track-modal').classList.remove('open'); }

function trackOrder() {
  const val = document.getElementById('track-input').value.trim();
  const result = document.getElementById('track-result');
  if (!val) { result.innerHTML = '<p style="color:red;">أدخل رقم الطلب</p>'; return; }
  result.innerHTML = 'جاري البحث...';
  fetch('track.php?order=' + encodeURIComponent(val))
    .then(r => r.json())
    .then(data => {
      if (!data.success) { result.innerHTML = `<p style="color:red;">${data.message}</p>`; return; }
      const o = data.order;
      const map = { pending: 'قيد الانتظار', processing: 'قيد المعالجة', completed: 'مكتمل', cancelled: 'ملغي' };
      result.innerHTML = `
        <div style="background:#f0f7ff;padding:14px;border-radius:10px;line-height:1.8;">
          <div>رقم الطلب: <strong>${o.order_id}</strong></div>
          <div>الحالة: <strong>${map[o.status] || o.status}</strong></div>
          <div>المبلغ: <strong>${o.total} ر.س</strong></div>
          <div>التاريخ: <strong>${o.created_at}</strong></div>
        </div>`;
    })
    .catch(() => result.innerHTML = '<p style="color:red;">حدث خطأ</p>');
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3000);
}

renderProducts();
</script>
</body>
</html>