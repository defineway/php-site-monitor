<?php
namespace App\Models;

use App\Config\Database;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create(array $data): int {
        $sql = "INSERT INTO users (username, email, password_hash, role) 
                VALUES (:username, :email, :password_hash, :role)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM users WHERE username = :username AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    public function updateLastLogin(int $userId): bool {
        $sql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
    
    public function findAll(): array {
        $sql = "SELECT id, username, email, role, is_active, created_at FROM users ORDER BY username";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];
        
        foreach (['username', 'email', 'role', 'is_active'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }
        
        if (isset($data['password'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete(int $id): bool {
        // Soft delete by setting is_active to false
        $sql = "UPDATE users SET is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
