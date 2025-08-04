<?php
/**
 * PHP Site Monitor
 *
 * @author Sushovan Mukherjee
 * @copyright 2025 Defineway Technologies Private Limited
 * @link https://defineway.com
 * @contact sushovan@defineway.com
 *
 * Licensed under the MIT License with Attribution Clause.
 * You must retain visible credit to the company ("Powered by Defineway Technologies Private Limited")
 * in the user interface and documentation of any derivative works or public deployments.
 */
namespace App\Services;

use App\Config\Database;
use App\Models\User;
use App\Models\Site;

class SiteService {
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

    /**
     * Find all sites listed in the database
     * This method retrieves all sites from the database, regardless of the user.
     * @return Site[] Array of Site objects
     * @throws \Exception if database query fails
     */
    public function findAll(): array {
        $sql = "SELECT s.*, u.username FROM sites s JOIN users u ON s.user_id = u.id ORDER BY s.name";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetchAll();
        
        if (!$row) {
            return []; // No sites found
        }
        
        return array_map(fn($item) => new Site($item), $row);
    }
    
    /**
     * Find all sites for a user or all sites if the user is an admin
     * @return Site[] Array of Site objects
     * @throws \Exception if database query fails
     */
    public function findAllByUser(User $user): array {
        if ($user->isAdmin()) {
            $sql = "SELECT s.*, u.username FROM sites s JOIN users u ON s.user_id = u.id ORDER BY s.name";
            $stmt = $this->db->query($sql);
        } else {
            $sql = "SELECT * FROM sites WHERE user_id = :user_id ORDER BY name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $user->getId()]);
        }
        $row = $stmt->fetchAll();
        if (!$row) {
            return []; // No sites found
        }
        return array_map(fn($item) => new Site($item), $row);
    }
    
    public function findById(int $id, User $user): ?Site {
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

        return new Site($site);
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
