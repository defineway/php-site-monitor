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
namespace App\Models;

use App\Config\Database;

class Session {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(int $userId, string $ipAddress, string $userAgent): string {
        $sessionId = $this->generateSessionId();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $sql = "INSERT INTO user_sessions (id, user_id, ip_address, user_agent, expires_at) 
                VALUES (:id, :user_id, :ip_address, :user_agent, :expires_at)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => $expiresAt
        ]);
        
        return $sessionId;
    }
    
    public function findBySessionId(string $sessionId): ?array {
        $sql = "SELECT s.*, u.id as user_id, u.username, u.email, u.role 
                FROM user_sessions s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = :session_id AND s.expires_at > NOW() AND u.is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['session_id' => $sessionId]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function delete(string $sessionId): bool {
        $sql = "DELETE FROM user_sessions WHERE id = :session_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['session_id' => $sessionId]);
    }
    
    public function deleteByUserId(int $userId): bool {
        $sql = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId]);
    }
    
    public function cleanup(): int {
        $sql = "DELETE FROM user_sessions WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    private function generateSessionId(): string {
        return bin2hex(random_bytes(64));
    }
    
    public function extend(string $sessionId): bool {
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $sql = "UPDATE user_sessions SET expires_at = :expires_at WHERE id = :session_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'expires_at' => $expiresAt,
            'session_id' => $sessionId
        ]);
    }
}
