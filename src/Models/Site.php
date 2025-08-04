<?php
namespace App\Models;

use App\Models\User;

class Site {
    // Fields: id, user_id, name, url, check_interval, ssl_check_enabled, ssl_check_interval, last_checked_at, status, created_at
    private ?int $id = null;
    private ?int $user_id = null;
    private ?string $name = null;
    private ?string $url = null;
    private ?int $check_interval = null;
    private ?bool $ssl_check_enabled = null;
    private ?int $ssl_check_interval = null;
    private ?string $last_checked_at = null;
    private ?string $status = null;
    private ?string $created_at = null;

    private User $user; // User object for the owner of the site

    /**
     * Constructor to initialize the Site object with data
     * @param array $data
     * @throws \InvalidArgumentException if required fields are missing
     * @throws \Exception if data types are incorrect
     */
    public function __construct( array $data = [] ) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->check_interval = $data['check_interval'] ?? null;
        $this->ssl_check_enabled = $data['ssl_check_enabled'] ?? null;
        $this->ssl_check_interval = $data['ssl_check_interval'] ?? null;
        $this->last_checked_at = $data['last_checked_at'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->user = new User();

        if(  ( isset($data['user_id']) && is_int($data['user_id']) ) ) {
            $this->user_id = $data['user_id'];
            $this->user->setId($data['user_id']);
        } else {
            throw new \InvalidArgumentException("User ID must be an integer.");
        }

        if( ( isset($data['username']) ) ) {
            is_string($data['username'])
                ? $this->user->setUsername($data['username'])
                : throw new \InvalidArgumentException("Username must be a string.");
        }
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setUserId(int $userId): void {
        $this->user_id = $userId;
        $this->user->setId($userId);
    }

    public function getUserId(): ?int {
        return $this->user_id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function getCheckInterval(): ?int {
        return $this->check_interval;
    }

    public function isSslCheckEnabled(): bool {
        return (bool)$this->ssl_check_enabled;
    }

    public function getSslCheckInterval(): ?int {
        return $this->ssl_check_interval;
    }

    public function getLastCheckedAt(): ?string {
        return $this->last_checked_at;
    }

    public function getStatus(): ?string {
        return $this->status;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }
}
