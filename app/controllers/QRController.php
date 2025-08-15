<?php
/**
 * QR Code Controller
 */

class QRController extends Controller {
    private $qrModel;
    private $tableModel;
    private $branchModel;

    public function __construct() {
        parent::__construct();
        $this->qrModel = new QRCode();
        $this->tableModel = new Table();
        $this->branchModel = new Branch();
    }

    public function scan($code) {
        // Validate QR code
        if (!$this->qrModel->validateQRCode($code)) {
            $this->session->setFlash('error', 'Invalid QR code.');
            $this->redirect(url('/'));
        }

        // Get QR code details
        $qrData = $this->qrModel->getQRCodeByCode($code);
        
        if (!$qrData) {
            $this->session->setFlash('error', 'QR code not found.');
            $this->redirect(url('/'));
        }

        // Check if user can access this branch
        if ($this->auth->isLoggedIn() && !$this->auth->canAccessBranch($qrData['branch_id'])) {
            $this->session->setFlash('error', 'You do not have permission to access this branch.');
            $this->redirect(url('/dashboard'));
        }

        // Log QR code scan
        $this->logActivity('qr_scan', "QR code scanned for table {$qrData['table_number']} at {$qrData['branch_name']}");

        // Redirect to order page with table information
        $this->redirect(url('/order/' . $code));
    }

    public function generate($branchId, $tableId) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Check permissions
        if (!$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($branchId)) {
            $this->session->setFlash('error', 'You do not have permission to generate QR codes for this branch.');
            $this->redirectBack();
        }

        // Check if table exists and belongs to branch
        $table = $this->tableModel->find($tableId);
        if (!$table || $table['branch_id'] != $branchId) {
            $this->session->setFlash('error', 'Table not found.');
            $this->redirectBack();
        }

        // Generate QR code
        $qrCodeId = $this->qrModel->createQRCode($branchId, $tableId);
        
        if ($qrCodeId) {
            $this->session->setFlash('success', 'QR code generated successfully.');
            $this->logActivity('qr_generate', "QR code generated for table {$table['table_number']}");
        } else {
            $this->session->setFlash('error', 'Failed to generate QR code.');
        }

        $this->redirectBack();
    }

    public function regenerate($qrCodeId) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Get QR code details
        $qrCode = $this->qrModel->find($qrCodeId);
        if (!$qrCode) {
            $this->session->setFlash('error', 'QR code not found.');
            $this->redirectBack();
        }

        // Check permissions
        if (!$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($qrCode['branch_id'])) {
            $this->session->setFlash('error', 'You do not have permission to regenerate this QR code.');
            $this->redirectBack();
        }

        // Regenerate QR code
        if ($this->qrModel->regenerateQRCode($qrCodeId)) {
            $this->session->setFlash('success', 'QR code regenerated successfully.');
            $this->logActivity('qr_regenerate', "QR code regenerated for ID: {$qrCodeId}");
        } else {
            $this->session->setFlash('error', 'Failed to regenerate QR code.');
        }

        $this->redirectBack();
    }

    public function download($qrCodeId) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Get QR code details
        $qrCode = $this->qrModel->find($qrCodeId);
        if (!$qrCode) {
            $this->session->setFlash('error', 'QR code not found.');
            $this->redirectBack();
        }

        // Check permissions
        if (!$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($qrCode['branch_id'])) {
            $this->session->setFlash('error', 'You do not have permission to download this QR code.');
            $this->redirectBack();
        }

        // Get file path
        $filePath = PUBLIC_PATH . '/' . $qrCode['qr_image_url'];
        
        if (!file_exists($filePath)) {
            $this->session->setFlash('error', 'QR code image not found.');
            $this->redirectBack();
        }

        // Set headers for download
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="qr_code_' . $qrCode['qr_code'] . '.png"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function batchGenerate($branchId) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Check permissions
        if (!$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($branchId)) {
            $this->session->setFlash('error', 'You do not have permission to generate QR codes for this branch.');
            $this->redirectBack();
        }

        // Generate QR codes for all tables
        $generated = $this->qrModel->batchGenerateQRCodes($branchId);
        
        if (!empty($generated)) {
            $count = count($generated);
            $this->session->setFlash('success', "Successfully generated {$count} QR codes.");
            $this->logActivity('qr_batch_generate', "Batch generated {$count} QR codes for branch ID: {$branchId}");
        } else {
            $this->session->setFlash('info', 'All tables already have QR codes.');
        }

        $this->redirectBack();
    }

    public function toggleStatus($qrCodeId) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Get QR code details
        $qrCode = $this->qrModel->find($qrCodeId);
        if (!$qrCode) {
            $this->session->setFlash('error', 'QR code not found.');
            $this->redirectBack();
        }

        // Check permissions
        if (!$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($qrCode['branch_id'])) {
            $this->session->setFlash('error', 'You do not have permission to modify this QR code.');
            $this->redirectBack();
        }

        // Toggle status
        $newStatus = $qrCode['is_active'] ? 0 : 1;
        
        if ($newStatus) {
            $this->qrModel->activateQRCode($qrCodeId);
            $this->session->setFlash('success', 'QR code activated successfully.');
            $this->logActivity('qr_activate', "QR code activated for ID: {$qrCodeId}");
        } else {
            $this->qrModel->deactivateQRCode($qrCodeId);
            $this->session->setFlash('success', 'QR code deactivated successfully.');
            $this->logActivity('qr_deactivate', "QR code deactivated for ID: {$qrCodeId}");
        }

        $this->redirectBack();
    }

    public function export($branchId = null) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Check permissions for branch-specific export
        if ($branchId && !$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($branchId)) {
            $this->session->setFlash('error', 'You do not have permission to export QR codes for this branch.');
            $this->redirectBack();
        }

        // Get QR codes data
        $qrCodes = $this->qrModel->exportQRCodes($branchId);
        
        if (empty($qrCodes)) {
            $this->session->setFlash('error', 'No QR codes found to export.');
            $this->redirectBack();
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="qr_codes_' . date('Y-m-d') . '.csv"');
        
        // Create CSV content
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['QR Code', 'Branch', 'Table Number', 'Created At', 'Status']);
        
        // Add data
        foreach ($qrCodes as $qrCode) {
            fputcsv($output, [
                $qrCode['qr_code'],
                $qrCode['branch_name'],
                $qrCode['table_number'],
                $qrCode['created_at'],
                $qrCode['is_active'] ? 'Active' : 'Inactive'
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function validateQR() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request method.'], 405);
        }

        $qrCode = $_POST['qr_code'] ?? '';
        
        if (empty($qrCode)) {
            $this->json(['success' => false, 'message' => 'QR code is required.'], 400);
        }

        // Validate QR code
        $isValid = $this->qrModel->validateQRCode($qrCode);
        
        if ($isValid) {
            $qrData = $this->qrModel->getQRCodeByCode($qrCode);
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

    public function stats($branchId = null) {
        $this->requireAuth(['super_admin', 'branch_manager']);

        // Check permissions for branch-specific stats
        if ($branchId && !$this->auth->hasRole('super_admin') && !$this->auth->canAccessBranch($branchId)) {
            $this->json(['success' => false, 'message' => 'Permission denied.'], 403);
        }

        // Get QR code statistics
        $stats = $this->qrModel->getQRCodeStats($branchId);
        
        // Get most used QR codes
        $mostUsed = $this->qrModel->getMostUsedQRCodes($branchId, 5);
        
        $this->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'most_used' => $mostUsed
            ]
        ]);
    }

    protected function logActivity($action, $description = null) {
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            $this->activityLogModel->logActivity($user['id'], $action, $description);
        }
    }
}
?>