<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCP\ICache;
use OCP\ICacheFactory;

/**
 * Rate limiting service to prevent abuse of endpoints
 */
class RateLimitService {
	private ICache $cache;
	private const RATE_LIMIT_PREFIX = 'dashlink_ratelimit_';

	public function __construct(ICacheFactory $cacheFactory) {
		$this->cache = $cacheFactory->createDistributed(self::RATE_LIMIT_PREFIX);
	}

	/**
	 * Check if an action is rate limited for a specific identifier
	 *
	 * @param string $action Action identifier (e.g., 'import', 'upload')
	 * @param string $identifier User ID or IP address
	 * @param int $maxAttempts Maximum attempts allowed in time window
	 * @param int $windowSeconds Time window in seconds
	 * @return bool True if rate limit exceeded, false otherwise
	 */
	public function isRateLimited(string $action, string $identifier, int $maxAttempts, int $windowSeconds): bool {
		$key = $this->getCacheKey($action, $identifier);
		$attempts = (int) $this->cache->get($key);

		if ($attempts >= $maxAttempts) {
			return true;
		}

		// Increment counter and set expiration
		$this->cache->set($key, $attempts + 1, $windowSeconds);
		return false;
	}

	/**
	 * Manually increment rate limit counter
	 *
	 * @param string $action Action identifier
	 * @param string $identifier User ID or IP address
	 * @param int $windowSeconds Time window in seconds
	 */
	public function incrementAttempts(string $action, string $identifier, int $windowSeconds): void {
		$key = $this->getCacheKey($action, $identifier);
		$attempts = (int) $this->cache->get($key);
		$this->cache->set($key, $attempts + 1, $windowSeconds);
	}

	/**
	 * Reset rate limit for a specific action and identifier
	 *
	 * @param string $action Action identifier
	 * @param string $identifier User ID or IP address
	 */
	public function resetRateLimit(string $action, string $identifier): void {
		$key = $this->getCacheKey($action, $identifier);
		$this->cache->remove($key);
	}

	/**
	 * Get current attempt count
	 *
	 * @param string $action Action identifier
	 * @param string $identifier User ID or IP address
	 * @return int Current attempt count
	 */
	public function getAttempts(string $action, string $identifier): int {
		$key = $this->getCacheKey($action, $identifier);
		return (int) $this->cache->get($key);
	}

	/**
	 * Generate cache key for rate limiting
	 *
	 * @param string $action Action identifier
	 * @param string $identifier User ID or IP address
	 * @return string Cache key
	 */
	private function getCacheKey(string $action, string $identifier): string {
		return $action . '_' . hash('sha256', $identifier);
	}
}
