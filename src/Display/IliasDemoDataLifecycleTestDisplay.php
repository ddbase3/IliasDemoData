<?php declare(strict_types=1);

namespace IliasDemoData\Display;

use Base3\Api\IDisplay;
use Base3\Authorization\Api\IAuthorizationService;
use UiFoundation\Settings\SettingsAdministrationAccess;
use Base3\Api\IMvcView;
use Base3\Api\IRequest;
use Base3\Session\Api\ISession;
use Base3\LinkTarget\Api\ILinkTargetService;
use IliasDemoData\Api\IDemoDataLifecycleService;
use RuntimeException;
use Throwable;

final class IliasDemoDataLifecycleTestDisplay implements IDisplay {

	private array $translations = [];
	private const CSRF_KEY = 'iliasdemodata.lifecycle.csrf';

	public function __construct(
		private readonly IRequest $request,
		private readonly IMvcView $view,
		private readonly ILinkTargetService $linkTargetService,
		private readonly IDemoDataLifecycleService $lifecycleService,
		private readonly IAuthorizationService $settingsAuthorization,
		private readonly ISession $session
	) {}

	public static function getName(): string {
		return 'iliasdemodatalifecycletestdisplay';
	}

	public function setData($data): void {
	}

	public function getHelp(): string {
		return 'Creates and removes the complete managed BASE3 ILIAS demo dataset for RAG, course management and reporting demonstrations.';
	}

	public function getOutput(string $out = 'html', bool $final = false): string {
		if (!SettingsAdministrationAccess::isAllowed(
			$this->settingsAuthorization, \IliasDemoData\SettingsTab\IliasdemodatalifecycleSettingsTab::getName()
		)) {
			http_response_code(403);
			return '';
		}

		$this->loadTranslations();

		return strtolower($out) === 'json'
			? $this->getJsonOutput($final)
			: $this->getHtmlOutput();
	}

	private function getHtmlOutput(): string {
		$parentRefId = $this->getRequestParentRefId();
		$body = $this->renderBody(
			$parentRefId,
			$this->lifecycleService->getStatus($parentRefId),
			null,
			''
		);

		$this->view->setPath(\DIR_COMPONENTS . 'Base3/IliasDemoData');
		$this->view->setTemplate('Display/IliasDemoDataLifecycleTestDisplay.php');
		$this->view->assign('endpoint', $this->buildEndpoint());
		$this->view->assign('csrfToken', $this->csrfToken());
		$this->view->assign('body', $body);

		return $this->view->loadTemplate();
	}

	private function getJsonOutput(bool $final): string {
		$payload = $this->request->getJsonBody();
		$payload = is_array($payload) ? $payload : [];
		$parentRefId = $this->getPayloadParentRefId($payload);
		$result = null;
		$error = '';
		$ok = true;

		try {
			$action = trim((string)($payload['action'] ?? ''));
			if (in_array($action, ['create', 'remove'], true) && !$this->validCsrf($payload)) {
				http_response_code(403);
				throw new RuntimeException($this->translations['invalid_request']);
			}
			if ($action === 'create') {
				$result = $this->lifecycleService->create(
					$parentRefId,
					(string)($payload['password'] ?? '')
				);
			} elseif ($action === 'remove') {
				if (($payload['confirm_remove'] ?? false) !== true) {
					throw new RuntimeException('Removal was not confirmed.');
				}
				$result = $this->lifecycleService->remove($parentRefId);
			} elseif ($action !== 'status') {
				throw new RuntimeException('Unknown demo data action.');
			}
		} catch (Throwable $exception) {
			$ok = false;
			$error = $exception->getMessage();
		}

		try {
			$status = $this->lifecycleService->getStatus($parentRefId);
			$html = $this->renderBody($parentRefId, $status, $result, $error);
		} catch (Throwable $exception) {
			$ok = false;
			if ($error === '') {
				$error = $exception->getMessage();
			}
			$status = [];
			$html = $this->renderBody($parentRefId, $status, $result, $error);
		}

		$response = [
			'ok' => $ok,
			'error' => $error,
			'html' => $html,
			'status' => $status,
		];

		if ($final && !headers_sent()) {
			header('Content-Type: application/json; charset=utf-8');
		}

		return (string)json_encode(
			$response,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	}

	private function csrfToken(): string {
		$token = $this->session->get(self::CSRF_KEY, '');
		if (!is_string($token) || strlen($token) !== 64) {
			$token = bin2hex(random_bytes(32));
			$this->session->set(self::CSRF_KEY, $token);
		}
		return $token;
	}

	private function validCsrf(array $payload): bool {
		$token = $this->session->get(self::CSRF_KEY, '');
		return strtoupper((string)$this->request->server('REQUEST_METHOD', '')) === 'POST'
			&& is_string($token) && strlen($token) === 64
			&& is_string($payload['csrf'] ?? null)
			&& hash_equals($token, $payload['csrf']);
	}

	private function renderBody(int $parentRefId, array $status, ?array $result, string $error): string {
		$this->view->setPath(\DIR_COMPONENTS . 'Base3/IliasDemoData');
		$this->view->setTemplate('Display/IliasDemoDataLifecycleTestBody.php');
		$this->view->assign('parentRefId', $parentRefId);
		$this->view->assign('status', $status);
		$this->view->assign('result', $result);
		$this->view->assign('error', $error);
		$this->view->assign('translations', $this->translations);

		return $this->view->loadTemplate();
	}

	private function getRequestParentRefId(): int {
		$default = defined('ROOT_FOLDER_ID') ? ROOT_FOLDER_ID : 1;
		$value = $this->request->get('base3_demo_parent_ref_id', $default);

		return is_numeric($value) && (int)$value > 0 ? (int)$value : $default;
	}

	private function getPayloadParentRefId(array $payload): int {
		$default = defined('ROOT_FOLDER_ID') ? ROOT_FOLDER_ID : 1;
		$value = $payload['parent_ref_id'] ?? $default;

		return is_numeric($value) && (int)$value > 0 ? (int)$value : $default;
	}

	private function buildEndpoint(): string {
		return $this->linkTargetService->getLink([
			'name' => self::getName(),
			'out' => 'json',
		]);
	}

	private function loadTranslations(): void {
		$this->view->setPath(\DIR_COMPONENTS . 'Base3/IliasDemoData');
		$this->view->loadBricks('Display');

		$translations = $this->view->getBricks('ilias_demo_data_lifecycle_test_display');
		$this->translations = is_array($translations) ? $translations : [];
	}
}
