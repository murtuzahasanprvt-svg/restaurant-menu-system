<?php
/**
 * Sample Addon Installation Script
 */

// This script runs when the addon is installed

$db = Database::getInstance();

// Create additional tables for the sample addon
$sql = "CREATE TABLE IF NOT EXISTS sample_addon_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    setting_type VARCHAR(20) DEFAULT 'string',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$db->query($sql);
$db->execute();

// Insert default settings
$defaultSettings = [
    [
        'setting_key' => 'sample_enabled',
        'setting_value' => '1',
        'setting_type' => 'boolean',
        'description' => 'Enable sample addon functionality'
    ],
    [
        'setting_key' => 'sample_discount_rate',
        'setting_value' => '0.1',
        'setting_type' => 'decimal',
        'description' => 'Default discount rate for sample addon'
    ],
    [
        'setting_key' => 'sample_welcome_message',
        'setting_value' => 'Welcome to our restaurant!',
        'setting_type' => 'string',
        'description' => 'Welcome message displayed by sample addon'
    ]
];

foreach ($defaultSettings as $setting) {
    $sql = "INSERT IGNORE INTO sample_addon_settings 
            (setting_key, setting_value, setting_type, description) 
            VALUES (:setting_key, :setting_value, :setting_type, :description)";
    
    $db->query($sql);
    $db->bind(':setting_key', $setting['setting_key']);
    $db->bind(':setting_value', $setting['setting_value']);
    $db->bind(':setting_type', $setting['setting_type']);
    $db->bind(':description', $setting['description']);
    $db->execute();
}

// Create sample addon assets directory
$assetsDir = ADDONS_PATH . '/sample-addon/assets';
if (!file_exists($assetsDir)) {
    mkdir($assetsDir, 0755, true);
}

// Create CSS and JS directories
if (!file_exists($assetsDir . '/css')) {
    mkdir($assetsDir . '/css', 0755, true);
}

if (!file_exists($assetsDir . '/js')) {
    mkdir($assetsDir . '/js', 0755, true);
}

if (!file_exists($assetsDir . '/images')) {
    mkdir($assetsDir . '/images', 0755, true);
}

// Create sample CSS file
$sampleCss = "/* Sample Addon CSS */
.sample-addon-widget {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 15px;
    margin: 10px 0;
}

.sample-addon-widget h3 {
    color: #e74c3c;
    margin-top: 0;
}

.sample-addon-button {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.sample-addon-button:hover {
    background: #c0392b;
}";

file_put_contents($assetsDir . '/css/sample.css', $sampleCss);

// Create sample JS file
$sampleJs = "// Sample Addon JavaScript
function sampleAddonTest() {
    alert('Sample addon is working!');
    
    // Make API call to sample endpoint
    fetch('/api/sample', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({
            action: 'test',
            timestamp: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Sample addon API response:', data);
        showNotification('Sample addon test completed!', 'success');
    })
    .catch(error => {
        console.error('Sample addon error:', error);
        showNotification('Sample addon test failed!', 'error');
    });
}

// Initialize sample addon functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sample addon initialized');
    
    // Add sample widget to page if it exists
    const sampleContainer = document.getElementById('sample-addon-container');
    if (sampleContainer) {
        sampleContainer.innerHTML = `
            <div class=\"sample-addon-widget\">
                <h3>Sample Addon Widget</h3>
                <p>This widget was added by the sample addon.</p>
                <button class=\"sample-addon-button\" onclick=\"sampleAddonTest()\">Test Addon</button>
            </div>
        `;
    }
});";

file_put_contents($assetsDir . '/js/sample.js', $sampleJs);

// Log installation
error_log("Sample addon installed successfully at " . date('Y-m-d H:i:s'));

echo "Sample addon installation completed successfully!\n";
?>