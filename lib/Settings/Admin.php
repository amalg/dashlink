<?php
declare(strict_types=1);

namespace OCA\DashLink\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class Admin implements ISettings {
	public function getForm(): TemplateResponse {
		return new TemplateResponse('dashlink', 'admin');
	}

	public function getSection(): string {
		return 'dashlink';
	}

	public function getPriority(): int {
		return 50;
	}
}
