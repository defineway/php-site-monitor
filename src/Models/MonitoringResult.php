<?php
namespace App\Models;

use App\Config\Database;

class MonitoringResult {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO monitoring_results (site_id, check_type, status, response_time, status_code, ssl_expiry_date, error_message) 
                VALUES (:site_id, :check_type, :status, :response_time, :status_code, :ssl_expiry_date, :error_message)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }
    
    public function findBySiteId(int $siteId, int $limit = 50): array {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':site_id', $siteId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getLatestStatus(int $siteId): ?array {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['site_id' => $siteId]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function getLatestStatusForAllSites(): array {
        $sql = "SELECT mr1.* FROM monitoring_results mr1
                INNER JOIN (
                    SELECT site_id, MAX(checked_at) as max_checked_at
                    FROM monitoring_results
                    GROUP BY site_id
                ) mr2 ON mr1.site_id = mr2.site_id AND mr1.checked_at = mr2.max_checked_at";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
