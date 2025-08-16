<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    
    public function index() {
        $this->requireAuth();
        
        $user = $this->auth->getCurrentUser();
        
        // Get dashboard data based on user role
        if ($this->auth->hasRole('super_admin')) {
            $data = $this->getSuperAdminDashboard();
        } elseif ($this->auth->hasRole('branch_manager')) {
            $data = $this->getBranchManagerDashboard();
        } elseif ($this->auth->hasRole(['chef', 'waiter', 'staff'])) {
            $data = $this->getStaffDashboard();
        } else {
            $data = $this->getDefaultDashboard();
        }
        
        $data['title'] = 'Dashboard';
        
        $this->render('dashboard', $data);
    }
    
    private function getSuperAdminDashboard() {
        $branchModel = new Branch();
        $userModel = new User();
        $qrModel = new QRCode();
        
        return [
            'total_branches' => $branchModel->getTotalBranches(),
            'total_users' => $userModel->getTotalUsers(),
            'total_qr_codes' => $qrModel->getQRCodeStats(),
            'recent_activity' => $this->getRecentActivity(),
            'user_role' => 'super_admin'
        ];
    }
    
    private function getBranchManagerDashboard() {
        $user = $this->auth->getCurrentUser();
        $qrModel = new QRCode();
        
        return [
            'branch_stats' => $qrModel->getQRCodeStats($user['branch_id']),
            'recent_activity' => $this->getRecentActivity($user['branch_id']),
            'user_role' => 'branch_manager'
        ];
    }
    
    private function getStaffDashboard() {
        $user = $this->auth->getCurrentUser();
        
        return [
            'recent_activity' => $this->getRecentActivity($user['branch_id']),
            'user_role' => $user['role']
        ];
    }
    
    private function getDefaultDashboard() {
        return [
            'message' => 'Welcome to the dashboard!',
            'user_role' => 'user'
        ];
    }
    
    private function getRecentActivity($branchId = null) {
        $activityLog = new ActivityLog();
        return $activityLog->getRecentActivity($branchId, 10);
    }
}
?>