<?php
namespace App\Services;

use App\Config\Database;
use App\Models\MonitoringResult;

class MonitoringResultService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new monitoring result
     * @param array $data Data for the monitoring result
     * @return int ID of the newly created monitoring result
     */
    public function create(array $data): int {
        $sql = "INSERT INTO monitoring_results (site_id, check_type, status, response_time, status_code, ssl_expiry_date, error_message) 
                VALUES (:site_id, :check_type, :status, :response_time, :status_code, :ssl_expiry_date, :error_message)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Find monitoring results by site ID with optional limit
     * @param int $siteId Site ID to filter results
     * @param int $limit Maximum number of results to return
     * @return MonitoringResult[] Array of MonitoringResult objects
     */
    public function findBySiteId(int $siteId, int $limit = 50): array {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':site_id', $siteId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new MonitoringResult($row), $rows);
    }

    /**
     * Get the latest monitoring result for a specific site
     * @param int $siteId Site ID to get the latest result for
     * @return MonitoringResult|null Latest monitoring result or null if not found
     */
    public function getLatestStatus(int $siteId): ?MonitoringResult {
        $sql = "SELECT * FROM monitoring_results WHERE site_id = :site_id 
                ORDER BY checked_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['site_id' => $siteId]);

        $row = $stmt->fetch();
        return $row ? new MonitoringResult($row) : null;
    }
    
    /**
     * Get the latest status for all sites
     * @return MonitoringResult[] Array of MonitoringResult objects with the latest status for each site
     */
    public function getLatestStatusForAllSites(): array {
        $sql = "SELECT mr1.* FROM monitoring_results mr1
                INNER JOIN (
                    SELECT site_id, MAX(checked_at) as max_checked_at
                    FROM monitoring_results
                    GROUP BY site_id
                ) mr2 ON mr1.site_id = mr2.site_id AND mr1.checked_at = mr2.max_checked_at";
        
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        return array_map(fn($row) => new MonitoringResult($row), $rows);
    }
    
    /**
     * Get the last check time for a specific site and check type
     * @param int $siteId Site ID to get the last check time for
     * @param string $checkType Check type ('uptime' or 'ssl')
     * @return string|null Last check time or null if not found
     */
    public function getLastCheckTime(int $siteId, string $checkType): ?string {
        $sql = "SELECT checked_at FROM monitoring_results 
                WHERE site_id = :site_id AND check_type = :check_type 
                ORDER BY checked_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'site_id' => $siteId,
            'check_type' => $checkType
        ]);

        $result = $stmt->fetch();
        return $result ? $result['checked_at'] : null;
    }
}
