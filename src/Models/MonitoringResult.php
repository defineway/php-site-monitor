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

class MonitoringResult {
	private $id;
	private $siteId;
	private $checkType;
	private $status;
	private $responseTime;
	private $statusCode;
	private $sslExpiryDate;
	private $errorMessage;
	private $checkedAt;

	private Site $site; // Site object for the site being monitored

	/**
	 * Constructor to initialize the MonitoringResult object with data
	 * @param array $data
	 */
	public function __construct(array $data = []) {
		$this->id = $data['id'] ?? null;
		$this->siteId = $data['site_id'] ?? null;
		$this->checkType = $data['check_type'] ?? null;
		$this->status = $data['status'] ?? null;
		$this->responseTime = $data['response_time'] ?? null;
		$this->statusCode = $data['status_code'] ?? null;
		$this->sslExpiryDate = $data['ssl_expiry_date'] ?? null;
		$this->errorMessage = $data['error_message'] ?? null;
		$this->checkedAt = $data['checked_at'] ?? null;

		$this->site = new Site(); // Initialize with an empty Site object
		if( $this->siteId ) {
			$this->site->setId($data['site_id']);
		}
	}

	public function setId(int $id): void {
		$this->id = $id;
	}

	public function getId(): ?int {
		return $this->id;
	}

	public function setSiteId(int $siteId): void {
		$this->siteId = $siteId;
		$this->site->setId($siteId);
	}

	public function getSiteId(): ?int {
		return $this->siteId;
	}

	public function getCheckType(): ?string {
		return $this->checkType;
	}

	public function getStatus(): ?string {
		return $this->status;
	}

	public function getResponseTime(): ?float {
		return $this->responseTime;
	}

	public function getStatusCode(): ?int {
		return $this->statusCode;
	}

	public function getSslExpiryDate(): ?string {
		return $this->sslExpiryDate;
	}

	public function getErrorMessage(): ?string {
		return $this->errorMessage;
	}

	public function getCheckedAt(): ?string {
		return $this->checkedAt;
	}

	public function getSite(): Site {
		return $this->site;
	}

	public function setSite(Site $site): void {
		$this->site = $site;
	}
}