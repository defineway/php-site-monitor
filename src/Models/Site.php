<?php
namespace App\Models;

use App\Config\Database;

class Site {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO sites (name, url, check_interval, ssl_check_enabled, ssl_check_interval) 
                VALUES (:name, :url, :check_interval, :ssl_check_enabled, :ssl_check_interval)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'url' => $data['url'],
            'check_interval' => $data['check_interval'] ?? 5,
            'ssl_check_enabled' => isset($data['ssl_check_enabled']) ? 1 : 0,
            'ssl_check_interval' => $data['ssl_check_interval'] ?? 86400
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function findAll(): array {
        $sql = "SELECT * FROM sites ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function update(int $id, array $data): bool {
        $sql = "UPDATE sites SET name = :name, url = :url, check_interval = :check_interval, 
                ssl_check_enabled = :ssl_check_enabled, ssl_check_interval = :ssl_check_interval 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'url' => $data['url'],
            'check_interval' => $data['check_interval'] ?? 5,
            'ssl_check_enabled' => isset($data['ssl_check_enabled']) ? 1 : 0,
            'ssl_check_interval' => $data['ssl_check_interval'] ?? 86400
        ]);
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
