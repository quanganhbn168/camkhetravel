let alerts;
const getAlerts = () => alerts ??= import('./notifications').then((module) => module.notifications);

export function initialiseLeadForms() {
    document.querySelectorAll('form[data-lead-form]').forEach((form) => {
        if (form.dataset.leadFormReady === 'true') return;
        form.dataset.leadFormReady = 'true';
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (form.dataset.submitting === 'true') return;
            if (!form.reportValidity()) return;
            const button = form.querySelector('[type="submit"]');
            const status = form.querySelector('[data-form-success]');
            if (status) { status.hidden = true; status.textContent = ''; }
            form.dataset.submitting = 'true';
            if (button) { button.disabled = true; button.setAttribute('aria-busy', 'true'); }
            let notify;
            try {
                // Failure to fetch the optional notification library must not
                // prevent a contact request from being submitted.
                notify = await getAlerts().catch(() => null);
                notify?.loading();
                const response = await fetch(form.action, {
                    method: (form.method || 'POST').toUpperCase(),
                    body: new FormData(form), credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const json = (response.headers.get('content-type') || '').includes('application/json');
                const payload = json ? await response.json().catch(() => ({})) : {};
                if (!response.ok || !json) {
                    const firstField = Object.keys(payload.errors || {})[0];
                    if (firstField) Array.from(form.elements).find((element) => element.name === firstField)?.focus();
                    const messages = Object.values(payload.errors || {}).flat().filter(Boolean).join('\n');
                    throw new Error(messages || (response.status === 419
                        ? 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang rồi gửi lại.'
                        : payload.message || 'Chưa gửi được yêu cầu. Vui lòng thử lại.'));
                }
                const hidden = [...form.querySelectorAll('input[type="hidden"]')].map((input) => [input, input.value]);
                form.reset();
                hidden.forEach(([input, value]) => { input.value = value; });
                const message = payload.message || 'DVTEC đã nhận yêu cầu và sẽ liên hệ tư vấn.';
                if (status) { status.textContent = message; status.hidden = false; status.setAttribute('role', 'status'); }
                notify?.success(message);
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Chưa gửi được yêu cầu. Vui lòng thử lại.';
                if (notify) notify.error(message);
                else {
                    let feedback = status;
                    if (!feedback) { feedback = document.createElement('p'); form.prepend(feedback); }
                    feedback.textContent = message; feedback.hidden = false; feedback.setAttribute('role', 'alert');
                }
            } finally {
                delete form.dataset.submitting;
                if (button) { button.disabled = false; button.removeAttribute('aria-busy'); }
            }
        });
    });
}
