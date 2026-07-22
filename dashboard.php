<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config.php';

if (isset($_POST['update_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET STATUS = ? WHERE order_id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: dashboard.php');
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>لوحة الإدارة</title>
  <style>
    body { font-family: Tahoma; background: #f5f7fa; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: right; }
    th { background: #1a73e8; color: #fff; }
    select, button { padding: 6px 10px; }
    .top-bar { margin-bottom: 20px; }
    .top-bar a { background: #1a73e8; color: #fff; padding: 8px 16px; text-decoration: none; border-radius: 6px; margin-left: 10px; }
    .logout { background: #e53935 !important; }
  </style>
</head>
<body>
  <div class="top-bar">
    <a href="products.php">إدارة المنتجات</a>
    <a href="logout.php" class="logout">تسجيل خروج</a>
  </div>

  <h2>الطلبات</h2>
  <table>
    <tr>
      <th>رقم الطلب</th>
      <th>البريد</th>
      <th>الجوال</th>
      <th>المبلغ</th>
      <th>الحالة</th>
      <th>التاريخ</th>
      <th>تغيير الحالة</th>
    </tr>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td><?= htmlspecialchars($o['order_id']) ?></td>
      <td><?= htmlspecialchars($o['email']) ?></td>
      <td><?= htmlspecialchars($o['phone']) ?></td>
      <td><?= $o['total'] ?> ر.س</td>
      <td><?= $o['STATUS'] ?></td>
      <td><?= $o['created_at'] ?></td>
      <td>
        <form method="POST" style="display:flex;gap:6px;">
          <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
          <select name="status">
            <option value="pending">قيد الانتظار</option>
            <option value="processing">قيد المعالجة</option>
            <option value="completed">مكتمل</option>
            <option value="cancelled">ملغي</option>
          </select>
          <button type="submit" name="update_status">حفظ</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>