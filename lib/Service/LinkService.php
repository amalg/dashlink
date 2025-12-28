<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCA\DashLink\Db\Link;
use OCA\DashLink\Db\LinkMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IGroupManager;
use OCP\IUserSession;

class LinkService {
	private LinkMapper $mapper;
	private IUserSession $userSession;
	private IGroupManager $groupManager;

	public function __construct(
		LinkMapper $mapper,
		IUserSession $userSession,
		IGroupManager $groupManager
	) {
		$this->mapper = $mapper;
		$this->userSession = $userSession;
		$this->groupManager = $groupManager;
	}

	/**
	 * Get links for the current user (filtered by group membership)
	 *
	 * @return array
	 */
	public function getLinksForCurrentUser(): array {
		$user = $this->userSession->getUser();
		if ($user === null) {
			return [];
		}

		// Get user's groups
		$userGroups = $this->groupManager->getUserGroupIds($user);

		// Find links for user
		$links = $this->mapper->findForUser($userGroups);

		return array_map(fn(Link $link) => $link->jsonSerialize(), $links);
	}

	/**
	 * Get all links (admin only)
	 *
	 * @return array
	 */
	public function getAllLinks(): array {
		$links = $this->mapper->findAll();
		return array_map(fn(Link $link) => $link->jsonSerialize(), $links);
	}

	/**
	 * Get link by ID
	 *
	 * @throws DoesNotExistException
	 */
	public function getLink(int $id): Link {
		return $this->mapper->findById($id);
	}

	/**
	 * Create new link
	 */
	public function createLink(array $data): Link {
		$link = new Link();
		$link->setTitle($data['title'] ?? '');
		$link->setUrl($data['url'] ?? '');
		$link->setDescription($data['description'] ?? null);
		$link->setTarget($data['target'] ?? '_blank');
		$link->setGroups($data['groups'] ?? []);
		$link->setPosition($data['position'] ?? 0);
		$link->setEnabled($data['enabled'] ?? 1);
		$link->setCreatedAt(new \DateTime());
		$link->setUpdatedAt(new \DateTime());

		return $this->mapper->insert($link);
	}

	/**
	 * Update link
	 *
	 * @throws DoesNotExistException
	 */
	public function updateLink(int $id, array $data): Link {
		$link = $this->mapper->findById($id);

		if (isset($data['title'])) {
			$link->setTitle($data['title']);
		}
		if (isset($data['url'])) {
			$link->setUrl($data['url']);
		}
		if (array_key_exists('description', $data)) {
			$link->setDescription($data['description']);
		}
		if (isset($data['target'])) {
			$link->setTarget($data['target']);
		}
		if (isset($data['groups'])) {
			$link->setGroups($data['groups']);
		}
		if (isset($data['position'])) {
			$link->setPosition($data['position']);
		}
		if (isset($data['enabled'])) {
			$link->setEnabled($data['enabled']);
		}

		$link->setUpdatedAt(new \DateTime());

		return $this->mapper->update($link);
	}

	/**
	 * Delete link
	 */
	public function deleteLink(int $id): void {
		$this->mapper->deleteById($id);
	}

	/**
	 * Update link order
	 *
	 * @param array $linkIds Array of link IDs in desired order
	 */
	public function updateOrder(array $linkIds): void {
		$this->mapper->updatePositions($linkIds);
	}

	/**
	 * Import links from array
	 *
	 * @param array $linksData Array of link data to import
	 * @param IconService $iconService IconService for downloading icons
	 * @return array Import result with counts
	 */
	public function importLinks(array $linksData, IconService $iconService): array {
		$imported = 0;
		$skipped = 0;
		$errors = [];

		// Get maximum position to append new links after existing ones
		$existingLinks = $this->mapper->findAll();
		$maxPosition = 0;
		foreach ($existingLinks as $existingLink) {
			if ($existingLink->getPosition() > $maxPosition) {
				$maxPosition = $existingLink->getPosition();
			}
		}

		foreach ($linksData as $linkData) {
			try {
				// Check if link already exists by title or URL
				$exists = false;

				foreach ($existingLinks as $existingLink) {
					if ($existingLink->getTitle() === ($linkData['title'] ?? '') ||
						$existingLink->getUrl() === ($linkData['url'] ?? '')) {
						$exists = true;
						break;
					}
				}

				if ($exists) {
					$skipped++;
					continue;
				}

				// Increment position for new link (append after last existing)
				$maxPosition++;

				// Create new link
				$link = $this->createLink([
					'title' => $linkData['title'] ?? '',
					'url' => $linkData['url'] ?? '',
					'description' => $linkData['description'] ?? null,
					'target' => $linkData['target'] ?? '_blank',
					'groups' => $linkData['groups'] ?? [],
					'position' => $maxPosition,
					'enabled' => $linkData['enabled'] ?? 1,
				]);

				// Download and attach icon if iconUrl is provided
				if (!empty($linkData['iconUrl'])) {
					try {
						$iconService->downloadAndSaveIcon($link->getId(), $linkData['iconUrl']);
					} catch (\Exception $e) {
						// Icon download failed, but link was created
						$errors[] = "Link '{$linkData['title']}' created but icon download failed: {$e->getMessage()}";
					}
				}

				$imported++;
			} catch (\Exception $e) {
				$errors[] = "Failed to import link '{$linkData['title']}': {$e->getMessage()}";
			}
		}

		return [
			'imported' => $imported,
			'skipped' => $skipped,
			'errors' => $errors,
		];
	}
}
