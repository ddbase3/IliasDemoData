<div id="ilias-demo-data-seed" class="ilias-demo-data-seed-ui" data-ilias-demo-data-seed-ui data-endpoint="<?php echo htmlspecialchars((string)$this->_['endpoint'], ENT_QUOTES); ?>" data-csrf="<?php echo htmlspecialchars((string)$this->_['csrfToken'], ENT_QUOTES); ?>">
	<div data-ilias-demo-data-content>
		<?php echo (string)$this->_['body']; ?>
	</div>
	<div class="demo-ajax-error" data-ilias-demo-data-ajax-error role="alert" hidden></div>
</div>

<style>
.ilias-demo-data-seed-ui {
	background: #fff;
	border: 1px solid #d6d6d6;
	border-radius: 4px;
	padding: 16px;
	color: #333;
	font-family: Arial, sans-serif;
}
.ilias-demo-data-seed-ui h3 { margin-top: 0; }
.demo-note { max-width: 980px; color: #555; }
.demo-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr); gap: 16px; align-items: start; }
.demo-card { border: 1px solid #ddd; border-radius: 4px; padding: 14px; background: #fafafa; }
.demo-danger-card { grid-column: 2; }
.demo-card h4 { margin-top: 0; }
.demo-card h5 { margin-bottom: 8px; }
.demo-card form { display: flex; flex-direction: column; gap: 10px; }
.demo-card label { display: flex; flex-direction: column; gap: 5px; font-size: 13px; }
.demo-card input[type="number"], .demo-card input[type="password"] { border: 1px solid #bbb; border-radius: 3px; padding: 7px 9px; background: #fff; }
.demo-card button { align-self: flex-start; padding: 8px 14px; border: 1px solid #aaa; border-radius: 3px; background: #eee; cursor: pointer; }
.demo-card button.danger { border-color: #9a4b4b; }
.demo-card button:disabled { opacity: .5; cursor: not-allowed; }
.demo-check { flex-direction: row !important; align-items: flex-start; }
.demo-small { margin: 0; color: #666; font-size: 12px; line-height: 1.4; }
.demo-message { margin: 12px 0; padding: 10px 12px; border-radius: 3px; }
.demo-error { background: #f9e7e7; border: 1px solid #d28d8d; }
.demo-success { background: #eef7ec; border: 1px solid #9dbf95; }
.demo-password { margin-top: 8px; }
.demo-table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
.demo-table th, .demo-table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; font-size: 12px; }
.demo-table th { background: #f2f2f2; }
.demo-table code { font-family: Consolas, monospace; }
.demo-ajax-error { margin-top: 12px; padding: 10px 12px; border: 1px solid #d28d8d; background: #f9e7e7; color: #8b0000; }
@media (max-width: 900px) {
	.demo-grid { grid-template-columns: 1fr; }
	.demo-danger-card { grid-column: auto; }
}
</style>

<script>
(function() {
	'use strict';

	const root = document.getElementById('ilias-demo-data-seed');
	if (!root || root.dataset.ajaxInitialized === '1') {
		return;
	}
	root.dataset.ajaxInitialized = '1';

	const endpoint = root.dataset.endpoint || '';
	const content = root.querySelector('[data-ilias-demo-data-content]');
	const ajaxError = root.querySelector('[data-ilias-demo-data-ajax-error]');
	let submitting = false;

	function setBusy(busy) {
		root.querySelectorAll('button[type="submit"]').forEach(function(button) {
			button.disabled = busy || button.dataset.serverDisabled === '1';
		});
	}

	function showTransportError(message) {
		if (!ajaxError) {
			return;
		}
		ajaxError.textContent = message;
		ajaxError.hidden = false;
	}

	root.addEventListener('submit', async function(event) {
		const form = event.target.closest('form[data-ilias-demo-ajax-form]');
		if (!form || !root.contains(form)) {
			return;
		}

		event.preventDefault();
		if (submitting || endpoint === '') {
			return;
		}

		const parentInput = form.querySelector('[name="parent_ref_id"]');
		const passwordInput = form.querySelector('[name="password"]');
		const confirmInput = form.querySelector('[name="confirm_remove"]');
		const payload = {
			action: form.dataset.action || '',
			parent_ref_id: parentInput ? Number(parentInput.value) : 0,
			password: passwordInput ? passwordInput.value : '',
			confirm_remove: confirmInput ? confirmInput.checked : false,
			csrf: root.dataset.csrf || ''
		};

		submitting = true;
		setBusy(true);
		if (ajaxError) {
			ajaxError.hidden = true;
			ajaxError.textContent = '';
		}

		try {
			const response = await fetch(endpoint, {
				method: 'POST',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json; charset=UTF-8'
				},
				body: JSON.stringify(payload)
			});
			const text = await response.text();
			let json;
			try {
				json = JSON.parse(text);
			} catch (error) {
				throw new Error('The server did not return valid JSON.');
			}

			if (typeof json.html !== 'string') {
				throw new Error(json.error || 'The server returned no demo data view.');
			}

			content.innerHTML = json.html;
		} catch (error) {
			showTransportError(error instanceof Error ? error.message : String(error));
		} finally {
			submitting = false;
			setBusy(false);
		}
	});
})();
</script>
