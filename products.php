<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';

// إضافة منتج
if (isset($_POST['add_product'])) {
    $stmt = $pdo->prepare("INSERT INTO products (name, duration, price, is_featured, is_active) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([
        $_POST['name'],
        $_POST['duration'],
        $_POST['price'],
        isset($_POST['is_featured']) ? 1 : 0
    ]);
    header('Location: products.php');
    exit;
}

// تحديث سعر
if (isset($_POST['update_price'])) {
    $stmt = $pdo->prepare("UPDATE products SET price = ? WHERE id = ?");
    $stmt->execute([$_POST['price'], $_POST['id']]);
    header('Location: products.php');
    exit;
}

// تفعيل / إيقاف
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE products SET is_active = 1 - is_active WHERE id = ?");
    $stmt->execute([$_GET['toggle']]);
    header('Location: products.php');
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>إدارة المنتجات</title>
  <style>
    body { font-family: Tahoma; background: #f5f7fa; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
    th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: right; }
    th { background: #1a73e8; color: #fff; }
    .form-box { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
    input, button { padding: 8px 12px; margin: 4px; }
    .btn { background: #1a73e8; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
  </style>
</head>
<body>
  <h2>إدارة المنتجات</h2>
  <a href="dashboard.php">← العودة للطلبات</a>

  <div class="form-box">
    <h3>إضافة منتج جديد</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="اسم المنتج" required>
      <input type="text" name="duration" placeholder="المدة (مثل: شهر واحد)" required>
      <input type="number" step="0.01" name="price" placeholder="السعر" required>
      <label><input type="checkbox" name="is_featured"> مميز</label>
      <button type="submit" name="add_product" class="btn">إضافة</button>
    </form>
  </div>

  <table>
    <tr>
      <th>ID</th>
      <th>الاسم</th>
      <th>المدة</th>
      <th>السعر</th>
      <th>مميز</th>
      <th>الحالة</th>
      <th>تعديل السعر</th>
      <th>تفعيل</th>
    </tr>
    <?php foreach ($products as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['name']) ?></td>
      <td><?= htmlspecialchars($p['duration']) ?></td>
      <td><?= $p['price'] ?> ر.س</td>
      <td><?= $p['is_featured'] ? 'نعم' : 'لا' ?></td>
      <td><?= $p['is_active'] ? 'مفعل' : 'موقوف' ?></td>
      <td>
        <form method="POST" style="display:flex;gap:6px;">
          <input type="hidden" name="id" value="<?= $p['id'] ?>">
          <input type="number" step="0.01" name="price" value="<?= $p['price'] ?>" style="width:80px;">
          <button type="submit" name="update_price" class="btn">حفظ</button>
        </form>
      </td>
      <td>
        <a href="?toggle=<?= $p['id'] ?>" class="btn"><?= $p['is_active'] ? 'إيقاف' : 'تفعيل' ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>