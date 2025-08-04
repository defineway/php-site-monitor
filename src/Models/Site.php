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

        if( isset($data['user_id']) ) {
            if( !is_int($data['user_id']) ) {
                throw new \InvalidArgumentException("User ID must be an integer.");
            }
            $this->user_id = $data['user_id'];
            $this->user->setId($data['user_id']);
        }

        if( isset($data['username']) ) {
            if( !is_string($data['username']) ) {
                throw new \InvalidArgumentException("Username must be a string.");
            }
            $this->user->setUsername($data['username']);
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

    public function getUser(): User {
        return $this->user;
    }
}
