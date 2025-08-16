<?php
/**
 * Home Controller
 */

class HomeController extends Controller {
    
    public function index() {
        // Get theme instance from application
        $theme = $this->app->getTheme();
        
        // Get available branches
        $branchModel = new Branch();
        $branches = $branchModel->getActiveBranches();
        
        $data = [
            'title' => 'Welcome to ' . APP_NAME,
            'branches' => $branches,
            'show_branch_selection' => count($branches) > 1
        ];
        
        $this->render('home', $data);
    }
}
?>