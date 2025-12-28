<?php
declare(strict_types=1);

namespace OCA\DashLink\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class AdminController extends Controller {
	public function __construct(
		string $appName,
		IRequest $request
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Render admin page
	 *
	 * @AdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
		\OCP\Util::addScript('dashlink', 'dashlink-admin');
		\OCP\Util::addStyle('dashlink', 'admin');

		return new TemplateResponse('dashlink', 'admin');
	}
}
