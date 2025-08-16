<?php
/**
 * Menu Controller
 */

class MenuController extends Controller {
    
    public function index($branchId) {
        $branchModel = new Branch();
        $branch = $branchModel->find($branchId);
        
        if (!$branch || !$branch['is_active']) {
            $this->session->setFlash('error', 'Branch not found.');
            $this->redirect(url('/branches'));
        }
        
        // Get menu categories for this branch
        $db = Database::getInstance();
        $sql = "SELECT * FROM menu_categories WHERE branch_id = :branch_id AND is_active = 1 ORDER BY display_order";
        $db->query($sql);
        $db->bind(':branch_id', $branchId);
        $categories = $db->resultSet();
        
        // Get all menu items for this branch
        $sql = "SELECT mi.*, mc.name as category_name 
                FROM menu_items mi
                JOIN menu_categories mc ON mi.category_id = mc.id
                WHERE mc.branch_id = :branch_id AND mi.is_available = 1
                ORDER BY mc.display_order, mi.display_order, mi.name";
        $db->query($sql);
        $db->bind(':branch_id', $branchId);
        $menuItems = $db->resultSet();
        
        // Get menu items for each category
        foreach ($categories as &$category) {
            $sql = "SELECT * FROM menu_items WHERE category_id = :category_id AND is_available = 1 ORDER BY display_order";
            $db->query($sql);
            $db->bind(':category_id', $category['id']);
            $category['items'] = $db->resultSet();
        }
        
        $data = [
            'title' => 'Menu - ' . $branch['name'],
            'branch' => $branch,
            'categories' => $categories,
            'menuItems' => $menuItems
        ];
        
        $this->render('menu', $data);
    }
    
    public function category($branchId, $categoryId) {
        $branchModel = new Branch();
        $branch = $branchModel->find($branchId);
        
        if (!$branch || !$branch['is_active']) {
            $this->session->setFlash('error', 'Branch not found.');
            $this->redirect(url('/branches'));
        }
        
        // Get category details
        $db = Database::getInstance();
        $sql = "SELECT * FROM menu_categories WHERE id = :id AND branch_id = :branch_id AND is_active = 1";
        $db->query($sql);
        $db->bind(':id', $categoryId);
        $db->bind(':branch_id', $branchId);
        $category = $db->single();
        
        if (!$category) {
            $this->session->setFlash('error', 'Category not found.');
            $this->redirect(url('/menu/' . $branchId));
        }
        
        // Get menu items for this category
        $sql = "SELECT * FROM menu_items WHERE category_id = :category_id AND is_available = 1 ORDER BY display_order";
        $db->query($sql);
        $db->bind(':category_id', $categoryId);
        $items = $db->resultSet();
        
        $data = [
            'title' => $category['name'] . ' - ' . $branch['name'],
            'branch' => $branch,
            'category' => $category,
            'items' => $items
        ];
        
        $this->render('menu-category', $data);
    }
    
    public function item($branchId, $itemId) {
        $branchModel = new Branch();
        $branch = $branchModel->find($branchId);
        
        if (!$branch || !$branch['is_active']) {
            $this->session->setFlash('error', 'Branch not found.');
            $this->redirect(url('/branches'));
        }
        
        // Get menu item details
        $db = Database::getInstance();
        $sql = "SELECT mi.*, mc.name as category_name 
                FROM menu_items mi 
                JOIN menu_categories mc ON mi.category_id = mc.id 
                WHERE mi.id = :id AND mi.is_available = 1 AND mc.branch_id = :branch_id";
        $db->query($sql);
        $db->bind(':id', $itemId);
        $db->bind(':branch_id', $branchId);
        $item = $db->single();
        
        if (!$item) {
            $this->session->setFlash('error', 'Menu item not found.');
            $this->redirect(url('/menu/' . $branchId));
        }
        
        $data = [
            'title' => $item['name'] . ' - ' . $branch['name'],
            'branch' => $branch,
            'item' => $item
        ];
        
        $this->render('menu-item', $data);
    }
}
?>