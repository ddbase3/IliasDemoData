<?php
$translations = is_array($this->_['translations'] ?? null) ? $this->_['translations'] : [];
$t = static function(string $key, string $fallback) use ($translations): string {
	$value = trim((string)($translations[$key] ?? ''));
	return $value !== '' ? $value : $fallback;
};
$status = is_array($this->_['status'] ?? null) ? $this->_['status'] : [];
$result = is_array($this->_['result'] ?? null) ? $this->_['result'] : null;
$root = is_array($status['root'] ?? null) ? $status['root'] : null;
$role = is_array($status['role'] ?? null) ? $status['role'] : null;
$users = is_array($status['users'] ?? null) ? $status['users'] : [];
$stats = is_array($status['content_stats'] ?? null) ? $status['content_stats'] : [];
$membership = is_array($status['membership_status'] ?? null) ? $status['membership_status'] : [];
$isAdmin = !empty($status['is_system_admin']);
$parentValid = !empty($status['parent_valid']);
$installed = !empty($status['complete']);
$hasManagedData = !empty($status['managed_roots'])
	|| !empty($status['managed_repository_object_ids'])
	|| $users !== []
	|| $role !== null;
$serverDisabled = !$isAdmin || !$parentValid;
$createDisabled = $serverDisabled || $hasManagedData;
$removeDisabled = $serverDisabled || !$hasManagedData;
?>
<h3><?php echo htmlspecialchars($t('page_title', 'ILIAS Demo Data')); ?></h3>
<p class="demo-note">
	<?php echo htmlspecialchars($t('intro', 'Creates a complete English demo dataset for RAG, course-management and reporting demonstrations. All repository content lives below one managed root category. Demo users and the global demo role carry explicit technical ownership markers and can be removed again.')); ?>
</p>

<?php if ((string)($this->_['error'] ?? '') !== ''): ?>
	<div class="demo-message demo-error" role="alert">
		<strong><?php echo htmlspecialchars($t('error', 'Error')); ?>:</strong>
		<?php echo htmlspecialchars((string)$this->_['error']); ?>
	</div>
<?php endif; ?>

<?php if ($result !== null): ?>
	<div class="demo-message demo-success">
		<strong><?php echo htmlspecialchars((string)($result['message'] ?? '')); ?></strong>
		<?php if ((string)($result['generated_password'] ?? '') !== ''): ?>
			<div class="demo-password">
				<?php echo htmlspecialchars($t('generated_password', 'Generated shared demo user password')); ?>:
				<code><?php echo htmlspecialchars((string)$result['generated_password']); ?></code>
			</div>
		<?php endif; ?>
		<?php if (!empty($result['steps'])): ?>
			<table class="demo-table">
				<thead>
					<tr>
						<th><?php echo htmlspecialchars($t('step', 'Step')); ?></th>
						<th>ID / Count</th>
						<th>Ref ID</th>
						<th><?php echo htmlspecialchars($t('detail', 'Detail')); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ((array)$result['steps'] as $step): ?>
						<tr>
							<td><?php echo htmlspecialchars((string)($step['name'] ?? '')); ?></td>
							<td><code><?php echo htmlspecialchars((string)($step['id'] ?? '')); ?></code></td>
							<td><code><?php echo htmlspecialchars((string)($step['ref_id'] ?? '')); ?></code></td>
							<td><?php echo htmlspecialchars((string)($step['detail'] ?? '')); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div class="demo-grid">
	<section class="demo-card demo-status-card">
		<h4><?php echo htmlspecialchars($t('status', 'Status')); ?></h4>
		<table class="demo-table">
			<tbody>
				<tr><th>System administrator</th><td><?php echo $isAdmin ? 'yes' : 'no'; ?></td></tr>
				<tr><th>Parent ref_id</th><td><code><?php echo (int)($status['parent_ref_id'] ?? $this->_['parentRefId']); ?></code> <?php echo $parentValid ? '(valid)' : '(invalid)'; ?></td></tr>
				<tr><th>Demo dataset</th><td><strong><?php echo $installed ? 'installed and verified' : ($hasManagedData ? 'partial or incomplete' : 'not installed'); ?></strong></td></tr>
				<tr><th>Managed root</th><td><?php echo $root !== null ? '<code>' . (int)$root['ref_id'] . '</code>' : 'not found'; ?></td></tr>
				<tr><th>Repository objects</th><td><?php echo count((array)($status['managed_repository_object_ids'] ?? [])); ?> / <?php echo (int)($status['expected_repository_object_count'] ?? 0); ?></td></tr>
				<tr><th>Demo users</th><td><?php echo count($users); ?> / <?php echo (int)($status['expected_user_count'] ?? 0); ?></td></tr>
				<tr><th>Global demo role</th><td><?php echo $role !== null ? '<code>' . htmlspecialchars((string)$role['title']) . '</code>' : 'not found'; ?></td></tr>
				<tr><th>Role assignments</th><td><?php echo !empty($status['role_assignments_complete']) ? 'verified' : 'not complete'; ?></td></tr>
				<tr><th>Course assignments</th><td><?php echo (int)($membership['assigned'] ?? 0); ?> / <?php echo (int)($membership['expected'] ?? 0); ?></td></tr>
				<tr><th>Learning progress</th><td><?php echo !empty($status['learning_progress_enabled']) ? 'enabled, seeded and verified with memberships' : 'ILIAS learning progress disabled, course passed flags only'; ?></td></tr>
			</tbody>
		</table>

		<h5>Seed corpus</h5>
		<table class="demo-table">
			<tbody>
				<tr><th>Courses</th><td><?php echo (int)($stats['courses'] ?? 0); ?></td></tr>
				<tr><th>Wiki pages</th><td><?php echo (int)($stats['wiki_pages'] ?? 0); ?></td></tr>
				<tr><th>Blog posts</th><td><?php echo (int)($stats['blog_posts'] ?? 0); ?></td></tr>
				<tr><th>Forum threads</th><td><?php echo (int)($stats['forum_threads'] ?? 0); ?></td></tr>
				<tr><th>Learning-module pages</th><td><?php echo (int)($stats['learning_module_pages'] ?? 0); ?></td></tr>
				<tr><th>Reference files</th><td><?php echo (int)($stats['files'] ?? 0); ?></td></tr>
				<tr><th>Approximate corpus size</th><td><?php echo number_format((int)($stats['approx_words'] ?? 0)); ?> words</td></tr>
			</tbody>
		</table>

		<?php if ($users !== []): ?>
			<h5>Managed demo users</h5>
			<div class="demo-user-list">
				<?php foreach ($users as $user): ?>
					<code><?php echo htmlspecialchars((string)$user['login']); ?></code>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<section class="demo-card">
		<h4><?php echo htmlspecialchars($t('create_title', 'Create full demo dataset')); ?></h4>
		<p class="demo-small">Creates the complete managed repository subtree, 14 English demo users, course memberships with mixed progress, and the RAG corpus shown in the status table.</p>
		<form data-ilias-demo-ajax-form data-action="create">
			<label>
				Parent ref_id
				<input type="number" name="parent_ref_id" min="1" value="<?php echo (int)$this->_['parentRefId']; ?>" required>
			</label>
			<label>
				Shared demo user password
				<input type="password" name="password" autocomplete="new-password">
			</label>
			<p class="demo-small"><?php echo htmlspecialchars($t('password_note', 'Leave the password empty to generate one shared password for all managed demo users.')); ?></p>
			<button type="submit" data-server-disabled="<?php echo $createDisabled ? '1' : '0'; ?>"<?php echo $createDisabled ? ' disabled' : ''; ?>><?php echo htmlspecialchars($t('create_button', 'Create full demo dataset')); ?></button>
		</form>
	</section>

	<section class="demo-card demo-danger-card">
		<h4><?php echo htmlspecialchars($t('remove_title', 'Remove full demo dataset')); ?></h4>
		<p class="demo-small"><?php echo htmlspecialchars($t('remove_note', 'Removal uses the tested ILIAS repository lifecycle and only deletes the exact managed subtree, exact demo user markers, and exact demo role marker.')); ?></p>
		<form data-ilias-demo-ajax-form data-action="remove">
			<label>
				Parent ref_id
				<input type="number" name="parent_ref_id" min="1" value="<?php echo (int)$this->_['parentRefId']; ?>" required>
			</label>
			<label class="demo-check">
				<input type="checkbox" name="confirm_remove" value="yes">
				<?php echo htmlspecialchars($t('confirm_remove', 'I confirm removal of the complete managed BASE3 demo dataset.')); ?>
			</label>
			<button class="danger" type="submit" data-server-disabled="<?php echo $removeDisabled ? '1' : '0'; ?>"<?php echo $removeDisabled ? ' disabled' : ''; ?>><?php echo htmlspecialchars($t('remove_button', 'Remove full demo dataset')); ?></button>
		</form>
	</section>
</div>
