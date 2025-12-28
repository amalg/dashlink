<?php
declare(strict_types=1);

namespace OCA\DashLink\Service;

use OCA\DashLink\Db\Link;
use OCA\DashLink\Db\LinkMapper;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\SimpleFS\ISimpleFile;
use OCP\Files\SimpleFS\ISimpleFolder;

class IconService {
	private const FOLDER_NAME = 'icons';
	private const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
	private const ALLOWED_MIME_TYPES = [
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/svg+xml',
		'image/webp',
	];

	private IAppData $appData;
	private LinkMapper $mapper;

	public function __construct(
		IAppData $appData,
		LinkMapper $mapper
	) {
		$this->appData = $appData;
		$this->mapper = $mapper;
	}

	/**
	 * Get icons folder (create if not exists)
	 */
	private function getIconsFolder(): ISimpleFolder {
		try {
			return $this->appData->getFolder(self::FOLDER_NAME);
		} catch (NotFoundException $e) {
			return $this->appData->newFolder(self::FOLDER_NAME);
		}
	}

	/**
	 * Upload icon for link
	 *
	 * @param int $linkId
	 * @param string $tmpPath Temporary file path
	 * @param string $mimeType
	 * @throws \InvalidArgumentException
	 * @throws NotFoundException
	 * @throws NotPermittedException
	 */
	public function uploadIcon(int $linkId, string $tmpPath, string $mimeType): Link {
		// Validate mime type
		if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
			throw new \InvalidArgumentException('Invalid file type. Only images are allowed.');
		}

		// Validate file size
		$fileSize = filesize($tmpPath);
		if ($fileSize === false || $fileSize > self::MAX_FILE_SIZE) {
			throw new \InvalidArgumentException('File too large. Maximum size is 2MB.');
		}

		// Get link
		$link = $this->mapper->findById($linkId);

		// Delete old icon if exists
		if ($link->getIconPath() !== null) {
			$this->deleteIconFile($link->getIconPath());
		}

		// Generate filename
		$extension = $this->getExtensionFromMimeType($mimeType);
		$filename = 'icon_' . $linkId . '_' . time() . '.' . $extension;

		// Save file
		$folder = $this->getIconsFolder();
		$file = $folder->newFile($filename);
		$content = file_get_contents($tmpPath);
		if ($content === false) {
			throw new \RuntimeException('Failed to read uploaded file');
		}
		$file->putContent($content);

		// Update link
		$link->setIconPath($filename);
		$link->setIconMimeType($mimeType);
		$link->setUpdatedAt(new \DateTime());

		return $this->mapper->update($link);
	}

	/**
	 * Get icon file
	 *
	 * @throws NotFoundException
	 */
	public function getIconFile(string $filename): ISimpleFile {
		$folder = $this->getIconsFolder();
		return $folder->getFile($filename);
	}

	/**
	 * Delete icon for link
	 *
	 * @throws NotFoundException
	 */
	public function deleteIcon(int $linkId): Link {
		$link = $this->mapper->findById($linkId);

		if ($link->getIconPath() !== null) {
			$this->deleteIconFile($link->getIconPath());

			$link->setIconPath(null);
			$link->setIconMimeType(null);
			$link->setUpdatedAt(new \DateTime());

			return $this->mapper->update($link);
		}

		return $link;
	}

	/**
	 * Delete icon file from storage
	 */
	private function deleteIconFile(string $filename): void {
		try {
			$folder = $this->getIconsFolder();
			$file = $folder->getFile($filename);
			$file->delete();
		} catch (NotFoundException $e) {
			// File doesn't exist, nothing to delete
		}
	}

	/**
	 * Get file extension from mime type
	 */
	private function getExtensionFromMimeType(string $mimeType): string {
		$extensions = [
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif',
			'image/svg+xml' => 'svg',
			'image/webp' => 'webp',
		];

		return $extensions[$mimeType] ?? 'bin';
	}

	/**
	 * Download icon from URL and save it
	 *
	 * @param int $linkId
	 * @param string $iconUrl URL to download icon from
	 * @throws \InvalidArgumentException
	 * @throws NotFoundException
	 * @throws NotPermittedException
	 */
	public function downloadAndSaveIcon(int $linkId, string $iconUrl): Link {
		// Create a temporary file
		$tmpFile = tempnam(sys_get_temp_dir(), 'dashlink_icon_');
		if ($tmpFile === false) {
			throw new \RuntimeException('Failed to create temporary file');
		}

		try {
			// Download the file
			$context = stream_context_create([
				'http' => [
					'timeout' => 10, // 10 second timeout
					'user_agent' => 'DashLink/1.0',
				],
			]);

			$content = @file_get_contents($iconUrl, false, $context);
			if ($content === false) {
				throw new \RuntimeException('Failed to download icon from URL');
			}

			file_put_contents($tmpFile, $content);

			// Detect mime type
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($tmpFile);

			if ($mimeType === false || !in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
				throw new \InvalidArgumentException('Downloaded file is not a valid image');
			}

			// Upload the icon
			return $this->uploadIcon($linkId, $tmpFile, $mimeType);
		} finally {
			// Clean up temporary file
			if (file_exists($tmpFile)) {
				unlink($tmpFile);
			}
		}
	}
}
