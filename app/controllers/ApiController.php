<?php
/**
 * API Controller
 */

class ApiController extends Controller {
    
    public function menuItems() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $branchId = $data['branch_id'] ?? null;
        $categoryId = $data['category_id'] ?? null;
        
        if (empty($branchId)) {
            $this->json(['success' => false, 'message' => 'Branch ID is required.'], 400);
        }
        
        $db = Database::getInstance();
        
        if ($categoryId) {
            $sql = "SELECT mi.*, mc.name as category_name 
                    FROM menu_items mi 
                    JOIN menu_categories mc ON mi.category_id = mc.id 
                    WHERE mi.category_id = :category_id AND mi.is_available = 1 AND mc.branch_id = :branch_id 
                    ORDER BY mi.display_order";
            
            $db->query($sql);
            $db->bind(':category_id', $categoryId);
            $db->bind(':branch_id', $branchId);
        } else {
            $sql = "SELECT mi.*, mc.name as category_name 
                    FROM menu_items mi 
                    JOIN menu_categories mc ON mi.category_id = mc.id 
                    WHERE mi.is_available = 1 AND mc.branch_id = :branch_id 
                    ORDER BY mc.display_order, mi.display_order";
            
            $db->query($sql);
            $db->bind(':branch_id', $branchId);
        }
        
        $items = $db->resultSet();
        
        $this->json([
            'success' => true,
            'data' => $items
        ]);
    }
    
    public function createOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['branch_id', 'table_id', 'items'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->json(['success' => false, 'message' => "$field is required."], 400);
            }
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Generate order number
            $orderNumber = 'ORD' . date('YmdHis') . rand(100, 999);
            
            // Calculate totals
            $items = $data['items'];
            $totalAmount = 0;
            
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            
            // Calculate tax (assuming 8.5% tax rate)
            $taxAmount = $totalAmount * 0.085;
            $finalAmount = $totalAmount + $taxAmount;
            
            // Create order
            $sql = "INSERT INTO orders (order_number, branch_id, table_id, customer_name, customer_phone, order_type, status, total_amount, tax_amount, final_amount, special_instructions) 
                    VALUES (:order_number, :branch_id, :table_id, :customer_name, :customer_phone, :order_type, :status, :total_amount, :tax_amount, :final_amount, :special_instructions)";
            
            $db->query($sql);
            $db->bind(':order_number', $orderNumber);
            $db->bind(':branch_id', $data['branch_id']);
            $db->bind(':table_id', $data['table_id']);
            $db->bind(':customer_name', $data['customer_name'] ?? null);
            $db->bind(':customer_phone', $data['customer_phone'] ?? null);
            $db->bind(':order_type', $data['order_type'] ?? 'dine_in');
            $db->bind(':status', 'pending');
            $db->bind(':total_amount', $totalAmount);
            $db->bind(':tax_amount', $taxAmount);
            $db->bind(':final_amount', $finalAmount);
            $db->bind(':special_instructions', $data['special_instructions'] ?? null);
            $db->execute();
            
            $orderId = $db->lastInsertId();
            
            // Create order items
            foreach ($items as $item) {
                $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, total_price) 
                        VALUES (:order_id, :menu_item_id, :quantity, :unit_price, :total_price)";
                
                $db->query($sql);
                $db->bind(':order_id', $orderId);
                $db->bind(':menu_item_id', $item['id']);
                $db->bind(':quantity', $item['quantity']);
                $db->bind(':unit_price', $item['price']);
                $db->bind(':total_price', $item['price'] * $item['quantity']);
                $db->execute();
            }
            
            $db->commit();
            
            $this->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => [
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'total_amount' => $finalAmount
                ]
            ]);
            
        } catch (Exception $e) {
            $db->rollBack();
            $this->json(['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()], 500);
        }
    }
    
    public function orderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $orderNumber = $data['order_number'] ?? null;
        
        if (empty($orderNumber)) {
            $this->json(['success' => false, 'message' => 'Order number is required.'], 400);
        }
        
        $db = Database::getInstance();
        
        $sql = "SELECT o.*, b.name as branch_name, t.table_number 
                FROM orders o 
                JOIN branches b ON o.branch_id = b.id 
                LEFT JOIN tables t ON o.table_id = t.id 
                WHERE o.order_number = :order_number";
        
        $db->query($sql);
        $db->bind(':order_number', $orderNumber);
        $order = $db->single();
        
        if (!$order) {
            $this->json(['success' => false, 'message' => 'Order not found.'], 404);
        }
        
        // Get order items
        $sql = "SELECT oi.*, mi.name as item_name 
                FROM order_items oi 
                JOIN menu_items mi ON oi.menu_item_id = mi.id 
                WHERE oi.order_id = :order_id";
        
        $db->query($sql);
        $db->bind(':order_id', $order['id']);
        $items = $db->resultSet();
        
        $this->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'items' => $items
            ]
        ]);
    }
    
    public function validateQR() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $qrCode = $data['qr_code'] ?? '';
        
        if (empty($qrCode)) {
            $this->json(['success' => false, 'message' => 'QR code is required.'], 400);
        }
        
        $qrModel = new QRCode();
        $isValid = $qrModel->validateQRCode($qrCode);
        
        if ($isValid) {
            $qrData = $qrModel->getQRCodeByCode($qrCode);
            $this->json([
                'success' => true,
                'message' => 'Valid QR code.',
                'data' => [
                    'qr_code' => $qrData['qr_code'],
                    'branch_name' => $qrData['branch_name'],
                    'branch_address' => $qrData['branch_address'],
                    'table_number' => $qrData['table_number'],
                    'table_capacity' => $qrData['table_capacity']
                ]
            ]);
        } else {
            $this->json(['success' => false, 'message' => 'Invalid QR code.'], 400);
        }
    }
}
?>