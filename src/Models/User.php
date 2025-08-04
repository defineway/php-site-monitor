<?php
namespace App\Models;

class User {
    private $id;
    private $username;
    private $email;
    private $role;
    private $is_active;
    private $created_at;
    private $updated_at;
    private $password_hash;

    public function __construct($userData = null) {
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

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setUsername(string $username): void {
        $this->username = $username;
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
}
