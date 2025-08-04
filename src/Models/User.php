<?php
namespace App\Models;

use App\Config\Database;

class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $role;
    private $is_active;
    private $created_at;
    private $updated_at;
    private $password_hash;

    public function __construct($userData = null) {
        $this->db = Database::getInstance()->getConnection();
        if ($userData) {
            $this->id = $userData['id'];
            $this->username = $userData['username'];
            $this->email = $userData['email'];
            $this->role = $userData['role'];
            $this->is_active = $userData['is_active'];
            $this->created_at = $userData['created_at'] ?? null;
            $this->updated_at = $userData['updated_at'] ?? null;
            $this->password_hash = $userData['password_hash'] ?? null;
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getRole(): ?string {
        return $this->role;
    }

    public function isActive(): bool {
        return (bool)$this->is_active;
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    public function getPasswordHash(): ?string {
        return $this->password_hash;
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
        return $result ? new self($result) : null;
    }
    
    public function findByEmail(string $email): ?User {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ? new self($result) : null;
    }
    
    public function findById(int $id): ?User {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? new self($result) : null;
    }
    
    public function findByIdActive(int $id): ?User {
        $sql = "SELECT * FROM users WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? new self($result) : null;
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
        $sql = "SELECT id, username, email, role, is_active, created_at, updated_at FROM users ORDER BY username";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        $users = [];
        foreach ($rows as $row) {
            $users[] = new self($row);
        }
        return $users;
    }
    
    public function findAllActive(): array {
        $sql = "SELECT id, username, email, role, is_active, created_at, updated_at FROM users WHERE is_active = 1 ORDER BY username";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();
        $users = [];
        foreach ($rows as $row) {
            $users[] = new self($row);
        }
        return $users;
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        $params = ['id' => $id];
        
        // Handle each field properly
        if (isset($data['username'])) {
            $fields[] = "username = :username";
            $params['username'] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }
        
        if (isset($data['is_active'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = (bool)$data['is_active'];
        }
        
        // Handle status as is_active (backwards compatibility)
        if (isset($data['status'])) {
            $fields[] = "is_active = :is_active";
            $params['is_active'] = $data['status'] === 'active' ? 1 : 0;
        }

        if (isset($data['password'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($fields)) {
            return false;
        }

        // Add updated_at timestamp
        $fields[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete(int $id): bool {
        // Soft delete by setting is_active to false
        $sql = "UPDATE users SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function activate(int $id): bool {
        // Reactivate user by setting is_active to true
        $sql = "UPDATE users SET is_active = 1, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function hardDelete(int $id): bool {
        // Permanent delete - use with caution
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function getUserStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_users,
                    COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_users,
                    COUNT(CASE WHEN role = 'admin' THEN 1 END) as admin_users
                FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }
    
    public function findByRole(string $role): array {
        $sql = "SELECT id, username, email, role, is_active, created_at, updated_at 
                FROM users WHERE role = :role ORDER BY username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['role' => $role]);
        $rows = $stmt->fetchAll();
        $users = [];
        foreach ($rows as $row) {
            $users[] = new self($row);
        }
        return $users;
    }
}
