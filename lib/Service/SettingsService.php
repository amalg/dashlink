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
	private const DEFAULT_WIDGET_TITLE = 'DashLink';

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
	 * Get widget title
	 */
	public function getWidgetTitle(): string {
		return $this->config->getAppValue($this->appName, 'widget_title', self::DEFAULT_WIDGET_TITLE);
	}

	/**
	 * Set widget title
	 */
	public function setWidgetTitle(string $title): void {
		// Trim and validate
		$title = trim($title);
		if (empty($title)) {
			$title = self::DEFAULT_WIDGET_TITLE;
		}

		$this->config->setAppValue($this->appName, 'widget_title', $title);
	}

	/**
	 * Get all settings
	 */
	public function getSettings(): array {
		return [
			'hoverEffect' => $this->getHoverEffect(),
			'availableEffects' => $this->getAvailableEffects(),
			'widgetTitle' => $this->getWidgetTitle(),
		];
	}
}
