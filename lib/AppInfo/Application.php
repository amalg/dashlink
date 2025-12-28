<?php
declare(strict_types=1);

namespace OCA\DashLink\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\DashLink\Dashboard\DashLinkWidget;

class Application extends App implements IBootstrap {
	public const APP_ID = 'dashlink';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		// Register dashboard widget
		$context->registerDashboardWidget(DashLinkWidget::class);
	}

	public function boot(IBootContext $context): void {
		// Boot logic if needed
	}
}
