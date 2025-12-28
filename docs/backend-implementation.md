# Backend Implementation - Code Examples

This document contains detailed PHP backend implementation examples for DashLink v1.0.0.

## Overview

The backend is built with PHP 8.1+ using the Nextcloud App Framework, featuring:
- **15 RESTful API endpoints** (2 public, 11 admin link management, 2 admin settings)
- **Group-based access control** for link visibility
- **Export/Import system** with JSON and icon URL support
- **Icon management** with upload, delete, and download from URLs
- **Modular services** with dependency injection

## SettingsService.php

```php
<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCP\IConfig;

class SettingsService {
    private IConfig $config;
    private string $appName = 'dashlink';

    // Available effects - must match frontend registry
    private const AVAILABLE_EFFECTS = [
        'blur' => [
            'id' => 'blur',
            'name' => 'Blur Overlay',
            'description' => 'Description appears over a blurred logo background'
        ],
        'flip' => [
            'id' => 'flip',
            'name' => '3D Card Flip',
            'description' => 'Card flips to reveal description on the back'
        ],
        'slide' => [
            'id' => 'slide',
            'name' => 'Slide Panel',
            'description' => 'Description panel slides up from the bottom'
        ],
    ];

    private const DEFAULT_EFFECT = 'blur';

    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    /**
     * Get current hover effect
     */
    public function getHoverEffect(): string {
        $effect = $this->config->getAppValue($this->appName, 'hover_effect', self::DEFAULT_EFFECT);

        // Validate effect exists
        if (!isset(self::AVAILABLE_EFFECTS[$effect])) {
            return self::DEFAULT_EFFECT;
        }

        return $effect;
    }

    /**
     * Set hover effect
     */
    public function setHoverEffect(string $effect): void {
        if (!isset(self::AVAILABLE_EFFECTS[$effect])) {
            throw new \InvalidArgumentException('Invalid effect: ' . $effect);
        }

        $this->config->setAppValue($this->appName, 'hover_effect', $effect);
    }

    /**
     * Get all available effects
     */
    public function getAvailableEffects(): array {
        return array_values(self::AVAILABLE_EFFECTS);
    }

    /**
     * Get all settings
     */
    public function getSettings(): array {
        return [
            'hoverEffect' => $this->getHoverEffect(),
            'availableEffects' => $this->getAvailableEffects(),
        ];
    }
}
```

## SettingsController.php

```php
<?php
declare(strict_types=1);

namespace OCA\DashLink\Controller;

use OCA\DashLink\Service\SettingsService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class SettingsController extends Controller {
    private SettingsService $settingsService;

    public function __construct(
        string $appName,
        IRequest $request,
        SettingsService $settingsService
    ) {
        parent::__construct($appName, $request);
        $this->settingsService = $settingsService;
    }

    /**
     * @AdminRequired
     */
    public function index(): JSONResponse {
        return new JSONResponse($this->settingsService->getSettings());
    }

    /**
     * @AdminRequired
     */
    public function update(string $hoverEffect): JSONResponse {
        try {
            $this->settingsService->setHoverEffect($hoverEffect);
            return new JSONResponse(['status' => 'ok']);
        } catch (\InvalidArgumentException $e) {
            return new JSONResponse(['error' => $e->getMessage()], 400);
        }
    }
}
```

## Updated DashLinkWidget.php

```php
<?php
declare(strict_types=1);

namespace OCA\DashLink\Dashboard;

use OCP\Dashboard\IWidget;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IInitialStateService;
use OCA\DashLink\Service\LinkService;
use OCA\DashLink\Service\SettingsService;

class DashLinkWidget implements IWidget {
    private IL10N $l10n;
    private IURLGenerator $urlGenerator;
    private IInitialStateService $initialStateService;
    private LinkService $linkService;
    private SettingsService $settingsService;

    public function __construct(
        IL10N $l10n,
        IURLGenerator $urlGenerator,
        IInitialStateService $initialStateService,
        LinkService $linkService,
        SettingsService $settingsService
    ) {
        $this->l10n = $l10n;
        $this->urlGenerator = $urlGenerator;
        $this->initialStateService = $initialStateService;
        $this->linkService = $linkService;
        $this->settingsService = $settingsService;
    }

    public function getId(): string {
        return 'dashlink';
    }

    public function getTitle(): string {
        return $this->l10n->t('DashLink');
    }

    public function getOrder(): int {
        return 10;
    }

    public function getIconClass(): string {
        return 'icon-external';
    }

    public function getUrl(): ?string {
        return null;
    }

    public function load(): void {
        // Provide links for current user
        $links = $this->linkService->getLinksForCurrentUser();
        $this->initialStateService->provideInitialState(
            'dashlink',
            'links',
            $links
        );

        // Provide active hover effect
        $this->initialStateService->provideInitialState(
            'dashlink',
            'hoverEffect',
            $this->settingsService->getHoverEffect()
        );

        \OCP\Util::addScript('dashlink', 'dashlink-dashboard');
        \OCP\Util::addStyle('dashlink', 'dashboard');
    }
}
```

## LinkController.php - Additional Features

Beyond the basic CRUD operations, LinkController provides advanced features:

### Export Links to JSON

```php
/**
 * Export all links to JSON
 * @AdminRequired
 */
public function export(): JSONResponse {
    try {
        $links = $this->linkService->getAllLinks();
        $exportData = [];

        foreach ($links as $link) {
            $exportData[] = [
                'title' => $link->getTitle(),
                'url' => $link->getUrl(),
                'description' => $link->getDescription(),
                'iconUrl' => $link->getIconPath()
                    ? $this->urlGenerator->getAbsoluteURL(
                        $this->urlGenerator->linkToRoute('dashlink.link.getIcon', ['id' => $link->getId()])
                    )
                    : null,
                'target' => $link->getTarget(),
                'groups' => json_decode($link->getGroupsJson(), true) ?? [],
                'enabled' => (bool)$link->getEnabled(),
            ];
        }

        return new JSONResponse($exportData);
    } catch (\Exception $e) {
        return new JSONResponse(['error' => $e->getMessage()], 500);
    }
}
```

### Import Links from JSON

```php
/**
 * Import links from JSON
 * @AdminRequired
 */
public function import(array $links): JSONResponse {
    try {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($links as $linkData) {
            // Duplicate detection by title or URL
            if ($this->linkService->isDuplicate($linkData['title'], $linkData['url'])) {
                $skipped++;
                continue;
            }

            // Create link
            $link = $this->linkService->createLink($linkData);

            // Download icon from external URL if provided
            if (!empty($linkData['iconUrl'])) {
                try {
                    $this->iconService->downloadIcon($link->getId(), $linkData['iconUrl']);
                } catch (\Exception $e) {
                    $errors[] = "Failed to download icon for '{$linkData['title']}': {$e->getMessage()}";
                }
            }

            $imported++;
        }

        return new JSONResponse([
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    } catch (\Exception $e) {
        return new JSONResponse(['error' => $e->getMessage()], 500);
    }
}
```

### Download Icon from URL

```php
/**
 * Download and save icon from URL
 * @AdminRequired
 */
public function downloadIcon(int $id, string $iconUrl): JSONResponse {
    try {
        $link = $this->linkService->getLink($id);
        $this->iconService->downloadIcon($id, $iconUrl);

        return new JSONResponse([
            'id' => $link->getId(),
            'iconUrl' => $this->urlGenerator->linkToRoute('dashlink.link.getIcon', ['id' => $id])
        ]);
    } catch (\Exception $e) {
        return new JSONResponse(['error' => $e->getMessage()], 400);
    }
}
```

## LinkService.php - Core Business Logic

```php
<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCA\DashLink\Db\Link;
use OCA\DashLink\Db\LinkMapper;
use OCP\IGroupManager;
use OCP\IUserSession;

class LinkService {
    private LinkMapper $linkMapper;
    private IGroupManager $groupManager;
    private IUserSession $userSession;

    public function __construct(
        LinkMapper $linkMapper,
        IGroupManager $groupManager,
        IUserSession $userSession
    ) {
        $this->linkMapper = $linkMapper;
        $this->groupManager = $groupManager;
        $this->userSession = $userSession;
    }

    /**
     * Get links filtered for current user based on group membership
     */
    public function getLinksForCurrentUser(): array {
        $user = $this->userSession->getUser();
        if (!$user) {
            return [];
        }

        $userGroups = $this->groupManager->getUserGroupIds($user);
        return $this->linkMapper->findForUser($userGroups);
    }

    /**
     * Get all links (admin only)
     */
    public function getAllLinks(): array {
        return $this->linkMapper->findAll();
    }

    /**
     * Create new link
     */
    public function createLink(array $data): Link {
        $link = new Link();
        $link->setTitle($data['title']);
        $link->setUrl($data['url']);
        $link->setDescription($data['description'] ?? '');
        $link->setTarget($data['target'] ?? '_blank');
        $link->setGroupsJson(json_encode($data['groups'] ?? []));
        $link->setEnabled($data['enabled'] ?? true);

        // Get next position
        $maxPosition = $this->linkMapper->getMaxPosition();
        $link->setPosition($maxPosition + 1);

        return $this->linkMapper->insert($link);
    }

    /**
     * Update link order
     */
    public function updateOrder(array $linkIds): void {
        foreach ($linkIds as $position => $linkId) {
            $link = $this->linkMapper->find($linkId);
            $link->setPosition($position);
            $this->linkMapper->update($link);
        }
    }

    /**
     * Check if link is duplicate by title or URL
     */
    public function isDuplicate(string $title, string $url): bool {
        return $this->linkMapper->findByTitleOrUrl($title, $url) !== null;
    }
}
```

## IconService.php - Icon Management

```php
<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Http\Client\IClientService;

class IconService {
    private IAppData $appData;
    private IClientService $clientService;

    private const ALLOWED_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/svg+xml',
        'image/webp',
    ];

    private const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

    public function __construct(
        IAppData $appData,
        IClientService $clientService
    ) {
        $this->appData = $appData;
        $this->clientService = $clientService;
    }

    /**
     * Save uploaded icon
     */
    public function uploadIcon(int $linkId, array $file): string {
        // Validate file type
        if (!in_array($file['type'], self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('Invalid file type');
        }

        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException('File too large (max 2MB)');
        }

        // Get or create icons folder
        try {
            $folder = $this->appData->getFolder('icons');
        } catch (NotFoundException $e) {
            $folder = $this->appData->newFolder('icons');
        }

        // Delete old icon if exists
        $this->deleteIcon($linkId);

        // Save new icon
        $extension = $this->getExtensionFromMimeType($file['type']);
        $filename = $linkId . '.' . $extension;
        $iconFile = $folder->newFile($filename);
        $iconFile->putContent(file_get_contents($file['tmp_name']));

        return $filename;
    }

    /**
     * Download icon from external URL
     */
    public function downloadIcon(int $linkId, string $url): string {
        $client = $this->clientService->newClient();

        try {
            $response = $client->get($url, ['timeout' => 10]);
            $content = $response->getBody();
            $contentType = $response->getHeader('Content-Type');

            // Validate content type
            if (!in_array($contentType, self::ALLOWED_MIME_TYPES)) {
                throw new \InvalidArgumentException('Invalid image type from URL');
            }

            // Validate size
            if (strlen($content) > self::MAX_FILE_SIZE) {
                throw new \InvalidArgumentException('Downloaded image too large (max 2MB)');
            }

            // Get or create icons folder
            try {
                $folder = $this->appData->getFolder('icons');
            } catch (NotFoundException $e) {
                $folder = $this->appData->newFolder('icons');
            }

            // Delete old icon if exists
            $this->deleteIcon($linkId);

            // Save downloaded icon
            $extension = $this->getExtensionFromMimeType($contentType);
            $filename = $linkId . '.' . $extension;
            $iconFile = $folder->newFile($filename);
            $iconFile->putContent($content);

            return $filename;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to download icon: ' . $e->getMessage());
        }
    }

    /**
     * Delete icon
     */
    public function deleteIcon(int $linkId): void {
        try {
            $folder = $this->appData->getFolder('icons');

            // Try to find icon with any extension
            foreach (['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'] as $ext) {
                try {
                    $file = $folder->getFile($linkId . '.' . $ext);
                    $file->delete();
                    return;
                } catch (NotFoundException $e) {
                    continue;
                }
            }
        } catch (NotFoundException $e) {
            // Icons folder doesn't exist yet
        }
    }

    private function getExtensionFromMimeType(string $mimeType): string {
        $map = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
            'image/webp' => 'webp',
        ];

        return $map[$mimeType] ?? 'png';
    }
}
```

## Key Implementation Features

1. **Group-Based Filtering**: LinkService filters links based on user's group membership
2. **Export with Absolute URLs**: Export generates absolute Nextcloud URLs for icons
3. **Import with Duplicate Detection**: Prevents duplicate links by checking title and URL
4. **Icon Download**: Automatically downloads and validates icons from external URLs
5. **File Validation**: Strict validation for file types (PNG, JPG, GIF, SVG, WebP) and size (2MB max)
6. **App Data Storage**: Icons stored in Nextcloud app data directory for isolation
7. **Dependency Injection**: All services use constructor injection for testability
