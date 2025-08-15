<?php
/**
 * Order Controller
 */

class OrderController extends Controller {
    
    public function create($qrCode) {
        $qrModel = new QRCode();
        $qrData = $qrModel->getQRCodeByCode($qrCode);
        
        if (!$qrData) {
            $this->session->setFlash('error', 'Invalid QR code.');
            $this->redirect('/branches');
        }
        
        // Get menu for this branch
        $branchId = $qrData['branch_id'];
        $branchModel = new Branch();
        $branch = $branchModel->find($branchId);
        
        // Get menu categories and items
        $db = Database::getInstance();
        $sql = "SELECT * FROM menu_categories WHERE branch_id = :branch_id AND is_active = 1 ORDER BY display_order";
        $db->query($sql);
        $db->bind(':branch_id', $branchId);
        $categories = $db->resultSet();
        
        foreach ($categories as &$category) {
            $sql = "SELECT * FROM menu_items WHERE category_id = :category_id AND is_available = 1 ORDER BY display_order";
            $db->query($sql);
            $db->bind(':category_id', $category['id']);
            $category['items'] = $db->resultSet();
        }
        
        $data = [
            'title' => 'Place Order - ' . $branch['name'],
            'branch' => $branch,
            'qr_data' => $qrData,
            'categories' => $categories
        ];
        
        $this->render('order', $data);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }
        
        $this->requireAuth();
        
        $data = $_POST;
        
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
            $items = json_decode($data['items'], true);
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
    
    public function status($orderNumber) {
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
            $this->session->setFlash('error', 'Order not found.');
            $this->redirect('/');
        }
        
        // Get order items
        $sql = "SELECT oi.*, mi.name as item_name 
                FROM order_items oi 
                JOIN menu_items mi ON oi.menu_item_id = mi.id 
                WHERE oi.order_id = :order_id";
        
        $db->query($sql);
        $db->bind(':order_id', $order['id']);
        $items = $db->resultSet();
        
        $data = [
            'title' => 'Order Status - ' . $orderNumber,
            'order' => $order,
            'items' => $items
        ];
        
        $this->render('order-status', $data);
    }
    
    public function tracking($orderNumber) {
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
        
        // Get order status history
        $sql = "SELECT * FROM order_status_history WHERE order_id = :order_id ORDER BY created_at DESC";
        $db->query($sql);
        $db->bind(':order_id', $order['id']);
        $history = $db->resultSet();
        
        $this->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'history' => $history
            ]
        ]);
    }
}
?>