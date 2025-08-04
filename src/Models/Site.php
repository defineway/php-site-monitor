<?php
namespace App\Models;

use App\Config\Database;
use App\Models\User;

class Site {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data, int $userId): int {
        $sql = "INSERT INTO sites (user_id, name, url, check_interval, ssl_check_enabled, ssl_check_interval) 
                VALUES (:user_id, :name, :url, :check_interval, :ssl_check_enabled, :ssl_check_interval)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'name' => $data['name'],
            'url' => $data['url'],
            'check_interval' => $data['check_interval'] ?? 5,
            'ssl_check_enabled' => isset($data['ssl_check_enabled']) ? 1 : 0,
            'ssl_check_interval' => $data['ssl_check_interval'] ?? 86400
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function findAll(User $user): array {
        if ($user->isAdmin()) {
            $sql = "SELECT s.*, u.username FROM sites s JOIN users u ON s.user_id = u.id ORDER BY s.name";
            $stmt = $this->db->query($sql);
        } else {
            $sql = "SELECT * FROM sites WHERE user_id = :user_id ORDER BY name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $user->getId()]);
        }
        return $stmt->fetchAll();
    }
    
    public function findById(int $id, User $user): ?array {
        $sql = "SELECT * FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $site = $stmt->fetch();
        
        if (!$site) {
            return null;
        }
        
        if (!$user->isAdmin() && $site['user_id'] !== $user->getId()) {
            return null; // Not authorized
        }
        
        return $site;
    }
    
    public function update(int $id, array $data, User $user): bool {
        $site = $this->findById($id, $user);
        if (!$site) {
            return false; // Or throw exception
        }

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
    
    public function delete(int $id, User $user): bool {
        $site = $this->findById($id, $user);
        if (!$site) {
            return false; // Or throw exception
        }

        $sql = "DELETE FROM sites WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
