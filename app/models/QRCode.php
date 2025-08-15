<?php
/**
 * QR Code Model
 */

class QRCode extends Model {
    protected $table = 'qr_codes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'branch_id', 'table_id', 'qr_code', 'qr_image_url', 'is_active'
    ];
    protected $timestamps = true;

    public function getQRCodeByCode($qrCode) {
        $sql = "SELECT qc.*, b.name as branch_name, b.address as branch_address, 
                       t.table_number, t.capacity 
                FROM {$this->table} qc
                JOIN branches b ON qc.branch_id = b.id
                JOIN tables t ON qc.table_id = t.id
                WHERE qc.qr_code = :qr_code AND qc.is_active = 1
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':qr_code', $qrCode);
        return $this->db->single();
    }

    public function getQRCodesByBranch($branchId) {
        $sql = "SELECT qc.*, t.table_number, t.capacity, t.location
                FROM {$this->table} qc
                JOIN tables t ON qc.table_id = t.id
                WHERE qc.branch_id = :branch_id
                ORDER BY t.table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getQRCodesByTable($tableId) {
        $sql = "SELECT qc.*, b.name as branch_name
                FROM {$this->table} qc
                JOIN branches b ON qc.branch_id = b.id
                WHERE qc.table_id = :table_id
                ORDER BY qc.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':table_id', $tableId);
        return $this->db->resultSet();
    }

    public function generateUniqueQRCode() {
        do {
            $qrCode = 'QR-' . strtoupper(bin2hex(random_bytes(8)));
        } while ($this->qrCodeExists($qrCode));
        
        return $qrCode;
    }

    public function createQRCode($branchId, $tableId) {
        // Generate unique QR code
        $qrCode = $this->generateUniqueQRCode();
        
        // Generate QR code image
        $qrImage = $this->generateQRImage($qrCode);
        
        // Create QR code record
        $data = [
            'branch_id' => $branchId,
            'table_id' => $tableId,
            'qr_code' => $qrCode,
            'qr_image_url' => $qrImage,
            'is_active' => 1
        ];
        
        return $this->create($data);
    }

    public function regenerateQRCode($qrCodeId) {
        // Get existing QR code
        $existing = $this->find($qrCodeId);
        if (!$existing) {
            return false;
        }
        
        // Generate new unique QR code
        $newQRCode = $this->generateUniqueQRCode();
        
        // Generate new QR code image
        $newQRImage = $this->generateQRImage($newQRCode);
        
        // Update QR code record
        $data = [
            'qr_code' => $newQRCode,
            'qr_image_url' => $newQRImage
        ];
        
        return $this->update($qrCodeId, $data);
    }

    public function activateQRCode($qrCodeId) {
        return $this->update($qrCodeId, ['is_active' => 1]);
    }

    public function deactivateQRCode($qrCodeId) {
        return $this->update($qrCodeId, ['is_active' => 0]);
    }

    public function getBranchQRCodesWithStats($branchId) {
        $sql = "SELECT qc.*, t.table_number, t.capacity, t.location,
                       COUNT(o.id) as total_orders,
                       MAX(o.created_at) as last_order_date
                FROM {$this->table} qc
                JOIN tables t ON qc.table_id = t.id
                LEFT JOIN orders o ON qc.id = o.qr_code_id
                WHERE qc.branch_id = :branch_id
                GROUP BY qc.id
                ORDER BY t.table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getQRCodeStats($branchId = null) {
        $sql = "SELECT 
                    COUNT(*) as total_qr_codes,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_qr_codes,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_qr_codes
                FROM {$this->table}";
        
        $params = [];
        
        if ($branchId) {
            $sql .= " WHERE branch_id = :branch_id";
            $params[':branch_id'] = $branchId;
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->single();
    }

    public function getMostUsedQRCodes($branchId = null, $limit = 10) {
        $sql = "SELECT qc.*, b.name as branch_name, t.table_number,
                       COUNT(o.id) as order_count
                FROM {$this->table} qc
                JOIN branches b ON qc.branch_id = b.id
                JOIN tables t ON qc.table_id = t.id
                LEFT JOIN orders o ON qc.id = o.qr_code_id
                WHERE qc.is_active = 1";
        
        $params = [];
        
        if ($branchId) {
            $sql .= " AND qc.branch_id = :branch_id";
            $params[':branch_id'] = $branchId;
        }
        
        $sql .= " GROUP BY qc.id
                  ORDER BY order_count DESC
                  LIMIT :limit";
        
        $params[':limit'] = $limit;
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->resultSet();
    }

    public function validateQRCode($qrCode) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE qr_code = :qr_code AND is_active = 1";
        
        $this->db->query($sql);
        $this->db->bind(':qr_code', $qrCode);
        $result = $this->db->single();
        
        return $result['count'] > 0;
    }

    public function getQRCodeByBranchAndTable($branchId, $tableId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE branch_id = :branch_id AND table_id = :table_id AND is_active = 1
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':table_id', $tableId);
        return $this->db->single();
    }

    private function qrCodeExists($qrCode) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE qr_code = :qr_code";
        $this->db->query($sql);
        $this->db->bind(':qr_code', $qrCode);
        $result = $this->db->single();
        return $result['count'] > 0;
    }

    private function generateQRImage($qrCode) {
        // Generate unique filename
        $filename = 'qr_' . $qrCode . '.png';
        $filepath = QR_CODE_PATH . '/' . $filename;
        
        // Create QR code image (simplified version)
        // In a real implementation, you would use a QR code library like endroid/qr-code
        $image = imagecreatetruecolor(QR_CODE_SIZE, QR_CODE_SIZE);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Fill background
        imagefill($image, 0, 0, $white);
        
        // Create a simple pattern (in real implementation, this would be actual QR code data)
        $hash = crc32($qrCode);
        for ($i = 0; $i < QR_CODE_SIZE; $i += 10) {
            for ($j = 0; $j < QR_CODE_SIZE; $j += 10) {
                if (($hash + $i + $j) % 3 == 0) {
                    imagefilledrectangle($image, $i, $j, $i + 8, $j + 8, $black);
                }
            }
        }
        
        // Add QR code text
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagestring($image, 1, 5, QR_CODE_SIZE - 15, $qrCode, $textColor);
        
        // Save image
        imagepng($image, $filepath);
        imagedestroy($image);
        
        return 'qrcodes/' . $filename;
    }

    public function cleanupOldQRCodes($days = 30) {
        $sql = "DELETE FROM {$this->table} 
                WHERE is_active = 0 AND created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $this->db->query($sql);
        $this->db->bind(':days', $days, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function batchGenerateQRCodes($branchId) {
        // Get all tables for the branch
        $tableModel = new Table();
        $tables = $tableModel->getBranchTables($branchId);
        
        $generated = [];
        foreach ($tables as $table) {
            // Check if QR code already exists for this table
            $existing = $this->getQRCodeByBranchAndTable($branchId, $table['id']);
            
            if (!$existing) {
                $qrCodeId = $this->createQRCode($branchId, $table['id']);
                if ($qrCodeId) {
                    $generated[] = [
                        'table_id' => $table['id'],
                        'table_number' => $table['table_number'],
                        'qr_code_id' => $qrCodeId
                    ];
                }
            }
        }
        
        return $generated;
    }

    public function exportQRCodes($branchId = null) {
        $sql = "SELECT qc.qr_code, b.name as branch_name, t.table_number, 
                       qc.created_at, qc.is_active
                FROM {$this->table} qc
                JOIN branches b ON qc.branch_id = b.id
                JOIN tables t ON qc.table_id = t.id";
        
        $params = [];
        
        if ($branchId) {
            $sql .= " WHERE qc.branch_id = :branch_id";
            $params[':branch_id'] = $branchId;
        }
        
        $sql .= " ORDER BY b.name, t.table_number";
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->resultSet();
    }
}
?>