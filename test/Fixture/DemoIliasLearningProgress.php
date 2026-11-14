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


namespace Base3\IliasDemoData\Test\Fixture;

final class DemoIliasLearningProgress {

	public const LP_STATUS_NOT_ATTEMPTED_NUM = 0;
	public const LP_STATUS_IN_PROGRESS_NUM = 1;
	public const LP_STATUS_COMPLETED_NUM = 2;
	public const LP_STATUS_FAILED_NUM = 3;

	public static function writeStatus(int $courseId, int $userId, int $status, int $percentage, bool $flag): void {
		if (DemoIliasState::$switches['invalid_progress'] ?? false) return;
		DemoIliasState::$progress[$courseId][$userId] = ['status' => $status, 'percentage' => $percentage];
	}

	public static function _lookupStatus(int $courseId, int $userId, bool $flag): ?int {
		return DemoIliasState::$progress[$courseId][$userId]['status'] ?? null;
	}

	public static function _lookupPercentage(int $courseId, int $userId): ?int {
		return DemoIliasState::$progress[$courseId][$userId]['percentage'] ?? null;
	}
}
