<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of IliasDemoData for BASE3 Framework.
 *
 * IliasDemoData manages ILIAS demonstration content lifecycles.
 * It owns controlled seed content and related administration views.
 * Native ILIAS repository and authorization boundaries are preserved.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0-or-later
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/


namespace Base3\IliasDemoData\Test\Display;

use Base3\IliasDemoData\Display\IliasDemoDataLifecycleTestDisplay;
use Base3\IliasDemoData\Test\Fixture\DemoIliasAuthorization;
use Base3\IliasDemoData\Test\Fixture\DemoIliasLifecycle;
use Base3\IliasDemoData\Test\Fixture\DemoIliasLink;
use Base3\IliasDemoData\Test\Fixture\DemoIliasRequest;
use Base3\IliasDemoData\Test\Fixture\DemoIliasSession;
use Base3\IliasDemoData\Test\Fixture\DemoIliasView;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class IliasDemoDataLifecycleTestDisplayTest extends TestCase {

	protected function setUp(): void {
		foreach ([
			'DemoIliasAuthorization', 'DemoIliasLifecycle', 'DemoIliasLink',
			'DemoIliasRequest', 'DemoIliasSession', 'DemoIliasView'
		] as $fixtureName) {
			require_once dirname(__DIR__) . '/Fixture/' . $fixtureName . '.php';
		}
		if (!defined('DIR_COMPONENTS')) define('DIR_COMPONENTS', dirname(__DIR__, 3) . '/');
		if (!defined('ROOT_FOLDER_ID')) define('ROOT_FOLDER_ID', 1);
		http_response_code(200);
	}

	protected function tearDown(): void {
		http_response_code(200);
	}

	public function testTechnicalIdentityHelpAndIgnoredSetData(): void {
		$display = $this->display(new DemoIliasRequest());
		self::assertSame('iliasdemodatalifecycletestdisplay', IliasDemoDataLifecycleTestDisplay::getName());
		self::assertStringContainsString('managed BASE3 ILIAS demo dataset', $display->getHelp());
		self::assertNull($display->setData(['unused' => 'value']));
	}

	public function testHtmlUsesManagedTemplateLinkAndSessionToken(): void {
		$request = new DemoIliasRequest(['base3_demo_parent_ref_id' => '12']);
		$session = new DemoIliasSession();
		$view = new DemoIliasView();
		$lifecycle = new DemoIliasLifecycle();
		$link = new DemoIliasLink();
		$html = $this->display($request, $lifecycle, $session, new DemoIliasAuthorization(), $view, $link)->getOutput();
		self::assertStringContainsString('ILIAS Demo Data', $html);
		self::assertStringContainsString('data-ilias-demo-data-seed-ui', $html);
		self::assertStringContainsString('data-csrf=', $html);
		self::assertStringContainsString('value="12"', $html);
		self::assertSame([['status', 12]], $lifecycle->actions);
		self::assertCount(1, $link->targets);
		self::assertSame(['name' => IliasDemoDataLifecycleTestDisplay::getName(), 'out' => 'json'], $link->targets[0][0]);
		self::assertSame([], $link->targets[0][1]);
		self::assertSame([
			'bricks:Display', 'Display/IliasDemoDataLifecycleTestBody.php', 'Display/IliasDemoDataLifecycleTestDisplay.php'
		], $view->loads);
		self::assertSame(1, $session->writes);
		self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $session->get('iliasdemodata.lifecycle.csrf'));
	}

	public function testHtmlKeepsExistingSessionTokenWithoutWriting(): void {
		$session = new DemoIliasSession();
		$session->values['iliasdemodata.lifecycle.csrf'] = str_repeat('b', 64);
		$html = $this->display(new DemoIliasRequest(), session: $session)->getOutput('HTML');
		self::assertSame(0, $session->writes);
		self::assertStringContainsString(str_repeat('b', 64), $html);
	}

	public function testHtmlReplacesMalformedSessionTokens(): void {
		foreach ([123, '', 'tiny', str_repeat('0', 63)] as $invalid) {
			$session = new DemoIliasSession();
			$session->values['iliasdemodata.lifecycle.csrf'] = $invalid;
			$this->display(new DemoIliasRequest(), session: $session)->getOutput('html');
			self::assertSame(1, $session->writes);
			self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $session->get('iliasdemodata.lifecycle.csrf'));
		}
	}

	public function testJsonStatusIsReadOnlyAndWorksWithoutCsrf(): void {
		$request = new DemoIliasRequest([], ['action' => 'status', 'parent_ref_id' => 8], 'GET');
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display($request, $lifecycle));
		self::assertTrue($response['ok']);
		self::assertSame('', $response['error']);
		self::assertSame([['status', 8]], $lifecycle->actions);
		self::assertSame(8, $response['status']['parent_ref_id']);
		self::assertStringContainsString('ILIAS Demo Data', $response['html']);
	}

	public function testCreateRequiresValidCsrfAndPreservesPasswordAndReference(): void {
		$request = new DemoIliasRequest([], [
			'action' => 'create', 'parent_ref_id' => '17', 'password' => ' password ',
			'csrf' => str_repeat('e', 64)
		]);
		$session = $this->validSession('e');
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->createResult = [
			'message' => 'created', 'generated_password' => '<generated>',
			'steps' => [['name' => 'root', 'id' => 7, 'ref_id' => 11, 'detail' => 'seeded']]
		];
		$response = $this->json($this->display($request, $lifecycle, $session));
		self::assertTrue($response['ok']);
		self::assertSame([['create', 17, ' password '], ['status', 17]], $lifecycle->actions);
		self::assertStringContainsString('&lt;generated&gt;', $response['html']);
		self::assertStringNotContainsString('<generated>', $response['html']);
		self::assertStringContainsString('seeded', $response['html']);
	}

	public function testConfirmedRemoveCallsOnlyManagedLifecycleService(): void {
		$request = new DemoIliasRequest([], [
			'action' => 'remove', 'parent_ref_id' => 11, 'confirm_remove' => true,
			'csrf' => str_repeat('c', 64)
		]);
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display($request, $lifecycle, $this->validSession('c')));
		self::assertTrue($response['ok']);
		self::assertSame([['remove', 11], ['status', 11]], $lifecycle->actions);
		self::assertStringContainsString('removed', $response['html']);
	}

	public static function invalidConfirmations(): array {
		return [
			'missing' => [null], 'false' => [false], 'integer one' => [1], 'string yes' => ['yes'],
		];
	}

	#[DataProvider('invalidConfirmations')]
	public function testRemovalRequiresLiteralTrueConfirmation(mixed $confirmation): void {
		$payload = ['action' => 'remove', 'csrf' => str_repeat('a', 64)];
		if ($confirmation !== null) $payload['confirm_remove'] = $confirmation;
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display(new DemoIliasRequest([], $payload), $lifecycle, $this->validSession()));
		self::assertFalse($response['ok']);
		self::assertStringContainsString('Removal was not confirmed.', $response['error']);
		self::assertSame([['status', 1]], $lifecycle->actions);
	}

	public static function invalidMutations(): array {
		return [
			'get method' => ['GET', str_repeat('a', 64)],
			'wrong token' => ['POST', str_repeat('b', 64)],
			'missing token' => ['POST', null],
			'short token' => ['POST', 'short'],
			'non-string token' => ['POST', 123]
		];
	}

	#[DataProvider('invalidMutations')]
	public function testCreateRejectsInvalidCsrfWithoutMutations(string $method, mixed $token): void {
		$payload = ['action' => 'create', 'parent_ref_id' => 23, 'csrf' => $token];
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display(
			new DemoIliasRequest([], $payload, $method), $lifecycle, $this->validSession()
		));
		self::assertFalse($response['ok']);
		self::assertNotEmpty($response['error']);
		self::assertSame([['status', 23]], $lifecycle->actions);
		self::assertSame(403, http_response_code());
	}

	public function testRemovalWithInvalidCsrfDoesNotExecute(): void {
		$payload = ['action' => 'remove', 'confirm_remove' => true, 'csrf' => 'incorrect'];
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display(new DemoIliasRequest([], $payload), $lifecycle, $this->validSession()));
		self::assertFalse($response['ok']);
		self::assertSame([['status', 1]], $lifecycle->actions);
		self::assertSame(403, http_response_code());
	}

	public function testUnknownActionIsRejectedWithoutMutating(): void {
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display(new DemoIliasRequest([], ['action' => 'reset']), $lifecycle));
		self::assertFalse($response['ok']);
		self::assertStringContainsString('Unknown demo data action', $response['error']);
		self::assertSame([['status', 1]], $lifecycle->actions);
	}

	public function testWhitespaceAroundStatusIsIgnored(): void {
		$lifecycle = new DemoIliasLifecycle();
		$response = $this->json($this->display(new DemoIliasRequest([], ['action' => ' status ']), $lifecycle));
		self::assertTrue($response['ok']);
		self::assertSame([['status', 1]], $lifecycle->actions);
	}

	public function testMutationErrorsReturnStructuredJsonAndUpdatedStatus(): void {
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->throwOn = 'create';
		$response = $this->json($this->display(new DemoIliasRequest([], [
			'action' => 'create', 'csrf' => str_repeat('a', 64)
		]), $lifecycle, $this->validSession()));
		self::assertFalse($response['ok']);
		self::assertSame('create failure', $response['error']);
		self::assertSame([['create', 1, ''], ['status', 1]], $lifecycle->actions);
		self::assertStringContainsString('create failure', $response['html']);
	}

	public function testStatusFailureAfterActionIsReportedAsJsonError(): void {
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->throwOn = 'status';
		$response = $this->json($this->display(new DemoIliasRequest([], ['action' => 'status']), $lifecycle));
		self::assertFalse($response['ok']);
		self::assertSame('status failure', $response['error']);
		self::assertSame([], $response['status']);
		self::assertSame([['status', 1]], $lifecycle->actions);
		self::assertStringContainsString('status failure', $response['html']);
	}

	public static function parentReferences(): array {
		return [
			'positive integer' => ['9', 9], 'positive numeric' => [3.5, 3],
			'zero' => ['0', 1], 'negative' => [-2, 1],
			'invalid string' => ['unknown', 1], 'non-numeric array' => [[1], 1]
		];
	}

	#[DataProvider('parentReferences')]
	public function testJsonParentReferencesAreValidated(mixed $value, int $expected): void {
		$lifecycle = new DemoIliasLifecycle();
		$this->json($this->display(new DemoIliasRequest([], ['action' => 'status', 'parent_ref_id' => $value]), $lifecycle));
		self::assertSame([['status', $expected]], $lifecycle->actions);
	}

	#[DataProvider('parentReferences')]
	public function testHtmlParentReferencesAreValidated(mixed $value, int $expected): void {
		$lifecycle = new DemoIliasLifecycle();
		$this->display(new DemoIliasRequest(['base3_demo_parent_ref_id' => $value]), $lifecycle)->getOutput();
		self::assertSame([['status', $expected]], $lifecycle->actions);
	}

	public function testAccessDeniedByNativeSystemAuthorization(): void {
		$this->assertDenied(new DemoIliasAuthorization(false, true));
	}

	public function testAccessDeniedByFeatureAuthorization(): void {
		$this->assertDenied(new DemoIliasAuthorization(true, false));
	}

	public function testActionErrorTextIsHtmlEscaped(): void {
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->createResult = [
			'message' => '<script>alert(1)</script>', 'generated_password' => '<pass>',
			'steps' => [['name' => '<img src=x>', 'id' => 9, 'ref_id' => 3, 'detail' => '<b>']] 
		];
		$response = $this->json($this->display(new DemoIliasRequest([], [
			'action' => 'create', 'csrf' => str_repeat('a', 64)
		]), $lifecycle, $this->validSession()));
		self::assertStringNotContainsString('<script>alert(1)</script>', $response['html']);
		self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $response['html']);
		self::assertStringContainsString('&lt;img src=x&gt;', $response['html']);
		self::assertStringContainsString('&lt;pass&gt;', $response['html']);
	}

	public function testStatusBodyDisablesCreationForExistingManagedData(): void {
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->status['managed_roots'] = [['ref_id' => 9]];
		$html = $this->display(new DemoIliasRequest(), $lifecycle)->getOutput();
		self::assertStringContainsString('partial or incomplete', $html);
		self::assertStringContainsString('data-server-disabled="1" disabled', $html);
	}

	public function testStatusBodyDisablesActionsForUnauthorizedHostUser(): void {
		$lifecycle = new DemoIliasLifecycle();
		$lifecycle->status['is_system_admin'] = false;
		$html = $this->display(new DemoIliasRequest(), $lifecycle)->getOutput();
		self::assertStringContainsString('System administrator', $html);
		self::assertStringContainsString('data-server-disabled="1" disabled', $html);
	}

	public function testJsonFinalModeStillReturnsJson(): void {
		$response = $this->json($this->display(new DemoIliasRequest([], ['action' => 'status'])), true);
		self::assertTrue($response['ok']);
		self::assertArrayHasKey('html', $response);
	}

	private function assertDenied(DemoIliasAuthorization $auth): void {
		$lifecycle = new DemoIliasLifecycle();
		$output = $this->display(new DemoIliasRequest(), $lifecycle, authorization: $auth)->getOutput();
		self::assertSame('', $output);
		self::assertSame(403, http_response_code());
		self::assertSame([], $lifecycle->actions);
	}

	private function validSession(string $letter = 'a'): DemoIliasSession {
		$session = new DemoIliasSession();
		$session->set('iliasdemodata.lifecycle.csrf', str_repeat($letter, 64));
		return $session;
	}

	private function display(
		DemoIliasRequest $request,
		?DemoIliasLifecycle $lifecycle = null,
		?DemoIliasSession $session = null,
		?DemoIliasAuthorization $authorization = null,
		?DemoIliasView $view = null,
		?DemoIliasLink $link = null
	): IliasDemoDataLifecycleTestDisplay {
		return new IliasDemoDataLifecycleTestDisplay(
			$request,
			$view ?? new DemoIliasView(),
			$link ?? new DemoIliasLink(),
			$lifecycle ?? new DemoIliasLifecycle(),
			$authorization ?? new DemoIliasAuthorization(),
			$session ?? new DemoIliasSession()
		);
	}

	private function json(IliasDemoDataLifecycleTestDisplay $display, bool $final = false): array {
		return json_decode($display->getOutput('json', $final), true, 512, JSON_THROW_ON_ERROR);
	}
}
