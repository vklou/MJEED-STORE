<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

$orderNumber = $_GET['order'] ?? '';

if (empty($orderNumber)) {
    echo json_encode(['success' => false, 'message' => 'رقم الطلب مطلوب']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$orderNumber]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'الطلب غير موجود']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'order' => [
            'order_id' => $order['order_id'],
            'email' => $order['email'],
            'phone' => $order['phone'],
            'total' => $order['total'],
            'status' => $order['STATUS'],
            'created_at' => $order['created_at']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}