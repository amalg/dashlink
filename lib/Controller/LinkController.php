<?php
declare(strict_types=1);

namespace OCA\DashLink\Controller;

use OCA\DashLink\Service\IconService;
use OCA\DashLink\Service\LinkService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IURLGenerator;

class LinkController extends Controller {
	private LinkService $linkService;
	private IconService $iconService;
	private IGroupManager $groupManager;
	private IURLGenerator $urlGenerator;

	public function __construct(
		string $appName,
		IRequest $request,
		LinkService $linkService,
		IconService $iconService,
		IGroupManager $groupManager,
		IURLGenerator $urlGenerator
	) {
		parent::__construct($appName, $request);
		$this->linkService = $linkService;
		$this->iconService = $iconService;
		$this->groupManager = $groupManager;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * Get links for current user (filtered by groups)
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse {
		$links = $this->linkService->getLinksForCurrentUser();
		return new JSONResponse($links);
	}

	/**
	 * Get icon for link
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function getIcon(int $id): DataDisplayResponse|JSONResponse {
		try {
			$link = $this->linkService->getLink($id);
			$iconPath = $link->getIconPath();

			if ($iconPath === null) {
				return new JSONResponse(['error' => 'No icon found'], Http::STATUS_NOT_FOUND);
			}

			$file = $this->iconService->getIconFile($iconPath);

			$response = new DataDisplayResponse(
				$file->getContent(),
				Http::STATUS_OK,
				['Content-Type' => $link->getIconMimeType() ?? 'application/octet-stream']
			);

			// Cache for 1 day
			$response->cacheFor(86400);

			return $response;
		} catch (DoesNotExistException $e) {
			return new JSONResponse(['error' => 'Link not found'], Http::STATUS_NOT_FOUND);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Get all links (admin only)
	 *
	 * @AdminRequired
	 */
	public function adminIndex(): JSONResponse {
		$links = $this->linkService->getAllLinks();
		return new JSONResponse($links);
	}

	/**
	 * Create link
	 *
	 * @AdminRequired
	 */
	public function create(
		string $title,
		string $url,
		?string $description = null,
		string $target = '_blank',
		array $groups = [],
		int $position = 0,
		int $enabled = 1
	): JSONResponse {
		try {
			$link = $this->linkService->createLink([
				'title' => $title,
				'url' => $url,
				'description' => $description,
				'target' => $target,
				'groups' => $groups,
				'position' => $position,
				'enabled' => $enabled,
			]);

			return new JSONResponse($link->jsonSerialize(), Http::STATUS_CREATED);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * Update link
	 *
	 * @AdminRequired
	 */
	public function update(
		int $id,
		?string $title = null,
		?string $url = null,
		?string $description = null,
		?string $target = null,
		?array $groups = null,
		?int $position = null,
		?int $enabled = null
	): JSONResponse {
		try {
			$data = [];
			if ($title !== null) $data['title'] = $title;
			if ($url !== null) $data['url'] = $url;
			if ($description !== null) $data['description'] = $description;
			if ($target !== null) $data['target'] = $target;
			if ($groups !== null) $data['groups'] = $groups;
			if ($position !== null) $data['position'] = $position;
			if ($enabled !== null) $data['enabled'] = $enabled;

			$link = $this->linkService->updateLink($id, $data);

			return new JSONResponse($link->jsonSerialize());
		} catch (DoesNotExistException $e) {
			return new JSONResponse(['error' => 'Link not found'], Http::STATUS_NOT_FOUND);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * Delete link
	 *
	 * @AdminRequired
	 */
	public function delete(int $id): JSONResponse {
		try {
			// Delete icon first if exists
			try {
				$this->iconService->deleteIcon($id);
			} catch (\Exception $e) {
				// Ignore icon deletion errors
			}

			$this->linkService->deleteLink($id);

			return new JSONResponse(['status' => 'ok']);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * Upload icon for link
	 *
	 * @AdminRequired
	 */
	public function uploadIcon(int $id): JSONResponse {
		try {
			$file = $this->request->getUploadedFile('icon');

			if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
				return new JSONResponse(['error' => 'No file uploaded'], Http::STATUS_BAD_REQUEST);
			}

			$link = $this->iconService->uploadIcon(
				$id,
				$file['tmp_name'],
				$file['type']
			);

			return new JSONResponse($link->jsonSerialize());
		} catch (DoesNotExistException $e) {
			return new JSONResponse(['error' => 'Link not found'], Http::STATUS_NOT_FOUND);
		} catch (\InvalidArgumentException $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Delete icon for link
	 *
	 * @AdminRequired
	 */
	public function deleteIcon(int $id): JSONResponse {
		try {
			$link = $this->iconService->deleteIcon($id);
			return new JSONResponse($link->jsonSerialize());
		} catch (DoesNotExistException $e) {
			return new JSONResponse(['error' => 'Link not found'], Http::STATUS_NOT_FOUND);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Update link order
	 *
	 * @AdminRequired
	 */
	public function updateOrder(array $linkIds): JSONResponse {
		try {
			$this->linkService->updateOrder($linkIds);
			return new JSONResponse(['status' => 'ok']);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * Get available groups
	 *
	 * @AdminRequired
	 */
	public function getGroups(): JSONResponse {
		$groups = $this->groupManager->search('');

		$groupData = array_map(function ($group) {
			return [
				'id' => $group->getGID(),
				'displayName' => $group->getDisplayName(),
			];
		}, $groups);

		return new JSONResponse(array_values($groupData));
	}

	/**
	 * Export all links as JSON
	 *
	 * @AdminRequired
	 */
	public function exportLinks(): JSONResponse {
		try {
			$links = $this->linkService->getAllLinks();

			// Add full icon URLs for each link
			$exportData = array_map(function ($link) {
				if (!empty($link['iconPath'])) {
					$link['iconUrl'] = $this->urlGenerator->getAbsoluteURL(
						$this->urlGenerator->linkToRoute('dashlink.link.getIcon', ['id' => $link['id']])
					);
				}
				return $link;
			}, $links);

			return new JSONResponse($exportData);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}

	/**
	 * Import links from JSON
	 *
	 * @AdminRequired
	 */
	public function importLinks(): JSONResponse {
		try {
			$file = $this->request->getUploadedFile('file');

			if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
				return new JSONResponse(['error' => 'No file uploaded'], Http::STATUS_BAD_REQUEST);
			}

			$content = file_get_contents($file['tmp_name']);
			$linksData = json_decode($content, true);

			if (!is_array($linksData)) {
				return new JSONResponse(['error' => 'Invalid JSON format'], Http::STATUS_BAD_REQUEST);
			}

			$result = $this->linkService->importLinks($linksData, $this->iconService);

			return new JSONResponse($result);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], Http::STATUS_INTERNAL_SERVER_ERROR);
		}
	}
}
