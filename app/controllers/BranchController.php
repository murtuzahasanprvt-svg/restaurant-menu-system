<?php
/**
 * Branch Controller
 */

class BranchController extends Controller {
    
    public function index() {
        $branchModel = new Branch();
        $branches = $branchModel->getActiveBranches();
        
        $data = [
            'title' => 'Our Branches',
            'branches' => $branches
        ];
        
        $this->render('branches', $data);
    }
    
    public function show($id) {
        $branchModel = new Branch();
        $branch = $branchModel->find($id);
        
        if (!$branch || !$branch['is_active']) {
            $this->session->setFlash('error', 'Branch not found.');
            $this->redirect(url('/branches'));
        }
        
        // Get QR codes for this branch
        $qrModel = new QRCode();
        $qrCodes = $qrModel->getQRCodesByBranch($id);
        
        $data = [
            'title' => $branch['name'],
            'branch' => $branch,
            'qr_codes' => $qrCodes
        ];
        
        $this->render('branch', $data);
    }
}
?>