<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$total = $_POST['total'] ?? 0;
$items = json_decode($_POST['items'] ?? '[]', true);

if (empty($email) || empty($phone) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'البيانات ناقصة']);
    exit;
}

$receiptPath = null;
if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === 0) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($_FILES['receipt']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $filepath)) {
        $receiptPath = $filepath;
    }
}

try {
    $orderNumber = 'MJ' . rand(100000, 999999);

    $stmt = $pdo->prepare("INSERT INTO orders (order_id, email, phone, total, STATUS, receipt_path) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$orderNumber, $email, $phone, $total, $receiptPath]);

    echo json_encode([
        'success' => true,
        'order_number' => $orderNumber,
        'message' => 'تم استلام طلبك بنجاح'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
}