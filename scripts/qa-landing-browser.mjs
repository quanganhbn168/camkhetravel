import { spawn } from 'node:child_process';
import { mkdir, mkdtemp, rm, writeFile } from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';

const chromePath = process.env.THT_QA_CHROME || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const baseUrl = process.env.THT_QA_BASE_URL || 'http://127.0.0.1:8017';
const debugPort = Number(process.env.THT_QA_DEBUG_PORT || (9200 + (process.pid % 500)));
const stamp = new Date().toISOString().replace(/[:.]/g, '-');
const outputDir = path.resolve(process.argv[2] || `storage/app/qa/landing/${stamp}`);
const profileDir = await mkdtemp(path.join(os.tmpdir(), 'tht-landing-qa-'));

const pageCatalog = [
    ['ads', '/dich-vu-quang-cao-truc-tuyen-cho-doanh-nghiep'],
    ['wedding', '/dich-vu-quay-chup-phong-su-cuoi-chat-luong-cao'],
    ['corporate-film', '/dich-vu-san-xuat-phim-doanh-nghiep'],
    ['communications', '/giai-phap-truyen-thong-doanh-nghiep'],
    ['academy', '/hoc-vien-nhiep-anh-va-sang-tao-noi-dung'],
    ['academy-v2', '/khoa-hoc-nhiep-anh'],
    ['outsourced-marketing', '/tht-media-phong-marketing-thue-ngoai-gia-re'],
    ['event-media', '/quay-phim-chup-anh-su-kien-tai-bac-ninh'],
    ['profile', '/thiet-ke-profile-doanh-nghiep-ho-so-nang-luc'],
    ['event-organization', '/to-chuc-su-kien-tron-goi-chuyen-nghiep'],
];
const requestedPages = new Set((process.argv[3] || process.env.THT_QA_PAGES || '').split(',').map(value => value.trim()).filter(Boolean));
const pages = requestedPages.size > 0
    ? pageCatalog.filter(([name]) => requestedPages.has(name))
    : pageCatalog;

if (pages.length === 0) {
    throw new Error(`No landing page matched: ${[...requestedPages].join(', ')}`);
}

const viewports = [
    { name: 'desktop', width: 1536, height: 900, mobile: false, deviceScaleFactor: 1 },
    { name: 'mobile', width: 390, height: 844, mobile: true, deviceScaleFactor: 1 },
];

await mkdir(outputDir, { recursive: true });

const chrome = spawn(chromePath, [
    '--headless=new',
    '--disable-gpu',
    '--disable-background-networking',
    '--disable-component-update',
    '--disable-default-apps',
    '--disable-extensions',
    '--ignore-certificate-errors',
    '--no-default-browser-check',
    '--no-first-run',
    `--remote-debugging-port=${debugPort}`,
    `--user-data-dir=${profileDir}`,
    '--window-size=1536,900',
    'about:blank',
], { stdio: 'ignore', windowsHide: true });

const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds));

async function fetchJson(url, options = undefined) {
    const response = await fetch(url, options);

    if (! response.ok) {
        throw new Error(`${response.status} ${response.statusText}: ${url}`);
    }

    return response.json();
}

async function waitForDebugger() {
    let lastError;

    for (let attempt = 0; attempt < 80; attempt += 1) {
        try {
            return await fetchJson(`http://127.0.0.1:${debugPort}/json/list`);
        } catch (error) {
            lastError = error;
            await wait(100);
        }
    }

    throw lastError || new Error('Chrome DevTools endpoint did not start.');
}

let socket;

try {
    const targets = await waitForDebugger();
    const target = targets.find(candidate => candidate.type === 'page');

    if (! target?.webSocketDebuggerUrl) {
        throw new Error('No inspectable Chrome page target was found.');
    }

    socket = new WebSocket(target.webSocketDebuggerUrl);
    await new Promise((resolve, reject) => {
        socket.addEventListener('open', resolve, { once: true });
        socket.addEventListener('error', reject, { once: true });
    });

    let commandId = 0;
    const pending = new Map();
    let activeNetworkFailures = [];

    socket.addEventListener('message', event => {
        const message = JSON.parse(event.data);

        if (message.id && pending.has(message.id)) {
            const { resolve, reject } = pending.get(message.id);
            pending.delete(message.id);
            if (message.error) reject(new Error(message.error.message));
            else resolve(message.result || {});
            return;
        }

        if (message.method === 'Network.responseReceived') {
            const { response, type } = message.params;
            if (response.status >= 400 && ['Document', 'Image', 'Media', 'Script', 'Stylesheet', 'Font'].includes(type)) {
                activeNetworkFailures.push({ status: response.status, type, url: response.url });
            }
        }
    });

    function call(method, params = {}) {
        return new Promise((resolve, reject) => {
            const id = ++commandId;
            pending.set(id, { resolve, reject });
            socket.send(JSON.stringify({ id, method, params }));
        });
    }

    async function evaluate(expression) {
        const result = await call('Runtime.evaluate', {
            expression,
            awaitPromise: true,
            returnByValue: true,
        });

        if (result.exceptionDetails) {
            throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
        }

        return result.result?.value;
    }

    async function waitForPage(expectedPath) {
        for (let attempt = 0; attempt < 120; attempt += 1) {
            const ready = await evaluate(`({ readyState: document.readyState, path: location.pathname })`).catch(() => null);
            if (ready?.readyState === 'complete' && ready.path === expectedPath) return;
            await wait(100);
        }

        throw new Error(`Timed out waiting for ${expectedPath}`);
    }

    await call('Page.enable');
    await call('Runtime.enable');
    await call('Network.enable');

    const report = [];

    for (const viewport of viewports) {
        await call('Emulation.setDeviceMetricsOverride', {
            width: viewport.width,
            height: viewport.height,
            mobile: viewport.mobile,
            deviceScaleFactor: viewport.deviceScaleFactor,
            screenWidth: viewport.width,
            screenHeight: viewport.height,
        });

        for (const [name, pathname] of pages) {
            activeNetworkFailures = [];
            await call('Page.navigate', { url: `${baseUrl}${pathname}` });
            await waitForPage(pathname);
            await evaluate(`(async () => { if (document.fonts?.ready) await document.fonts.ready; await new Promise(resolve => setTimeout(resolve, 300)); return true; })()`);
            await evaluate('window.scrollTo(0, 0)');

            const screenshot = await call('Page.captureScreenshot', {
                format: 'png',
                captureBeyondViewport: false,
            });
            await writeFile(path.join(outputDir, `${viewport.name}-${name}.png`), Buffer.from(screenshot.data, 'base64'));

            const top = await evaluate(`(() => {
                const h1 = document.querySelector('h1');
                const h2 = document.querySelector('h2');
                const h3 = document.querySelector('h3');
                const bodyStyle = getComputedStyle(document.body);
                const metric = element => element ? ({ font: getComputedStyle(element).fontFamily, size: getComputedStyle(element).fontSize }) : null;
                const visible = element => {
                    if (! element) return false;
                    const style = getComputedStyle(element);
                    const rect = element.getBoundingClientRect();
                    return style.display !== 'none'
                        && style.visibility !== 'hidden'
                        && Number.parseFloat(style.opacity || '1') > 0
                        && rect.width > 0
                        && rect.height > 0
                        && rect.right > 0
                        && rect.left < innerWidth;
                };
                return {
                    title: document.title,
                    path: location.pathname,
                    bodyFont: bodyStyle.fontFamily,
                    bodyOverflowY: bodyStyle.overflowY,
                    h1: metric(h1),
                    h2: metric(h2),
                    h3: metric(h3),
                    viewportSize: { width: innerWidth, height: innerHeight },
                    documentSize: { width: document.documentElement.scrollWidth, height: document.documentElement.scrollHeight },
                    horizontalOverflow: document.documentElement.scrollWidth > document.documentElement.clientWidth + 1,
                    scrollable: document.documentElement.scrollHeight > document.documentElement.clientHeight,
                    sharedHeader: Boolean(document.querySelector('.tht-landing-header')),
                    sharedFooter: Boolean(document.querySelector('.tht-landing-footer')),
                    headerCtaVisible: visible(document.querySelector('.tht-landing-header__cta')),
                    menuToggleVisible: visible(document.querySelector('.tht-landing-menu-toggle')),
                    legacyChrome: Boolean(document.querySelector('.academy-header, .academy-footer, .academy-v2-header, .academy-v2-footer, .communications-nav, .communications-footer, .landing-plain-header, .landing-plain-footer')),
                    modalTriggers: document.querySelectorAll('[data-landing-modal-open]').length,
                    modals: document.querySelectorAll('[data-landing-modal], .tht-landing-contact-modal, .academy-modal').length,
                    mobileInputsBelow16: innerWidth <= 767
                        ? [...document.querySelectorAll('input:not([type="hidden"]), select, textarea')]
                            .filter(element => Number.parseFloat(getComputedStyle(element).fontSize) < 16)
                            .map(element => ({ tag: element.tagName, name: element.getAttribute('name'), size: getComputedStyle(element).fontSize }))
                        : [],
                };
            })()`);

            const step = Math.max(480, Math.floor(viewport.height * 0.72));
            for (let y = 0; y <= top.documentSize.height; y += step) {
                await evaluate(`window.scrollTo(0, ${y})`);
                await wait(35);
            }
            await evaluate('window.scrollTo(0, document.documentElement.scrollHeight)');
            await wait(350);

            const footerScreenshot = await call('Page.captureScreenshot', {
                format: 'png',
                captureBeyondViewport: false,
            });
            await writeFile(path.join(outputDir, `${viewport.name}-${name}-footer.png`), Buffer.from(footerScreenshot.data, 'base64'));

            const end = await evaluate(`(() => ({
                scrollTop: document.scrollingElement?.scrollTop || 0,
                brokenImages: [...document.images].filter(image => image.complete && image.naturalWidth === 0).map(image => image.currentSrc || image.src),
                footerPresent: Boolean(document.querySelector('.tht-landing-footer')),
            }))()`);

            const interaction = await evaluate(`(() => {
                const trigger = document.querySelector('[data-landing-modal-open]');
                if (! trigger) return { modalTested: false };
                const modalSelector = trigger.dataset.landingModalOpen;
                trigger.click();
                return { modalTested: true, modalSelector };
            })()`);

            if (interaction.modalTested) {
                await wait(150);
                const modalSelector = JSON.stringify(interaction.modalSelector);
                interaction.modalOpened = await evaluate(`(() => {
                    const modal = document.querySelector(${modalSelector});
                    if (! modal) return false;
                    const style = getComputedStyle(modal);
                    return modal.classList.contains('is-open') || modal.classList.contains('show') || modal.getAttribute('aria-hidden') === 'false' || (style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0');
                })()`);
                await evaluate(`document.querySelector(${modalSelector})?.querySelector('[data-landing-modal-close], .tht-landing-modal-close, .academy-modal-close')?.click()`);
                await wait(100);
                interaction.bodyUnlockedAfterClose = await evaluate(`getComputedStyle(document.body).overflowY !== 'hidden' && !document.body.classList.contains('modal-open')`);
            }

            if (viewport.mobile) {
                interaction.mobileMenuOpened = await evaluate(`(() => {
                    const button = document.querySelector('.tht-landing-menu-toggle');
                    if (! button) return false;
                    button.click();
                    return true;
                })()`);
                await wait(100);
                interaction.mobileMenuVisible = await evaluate(`(() => {
                    const menu = document.querySelector('#tht-landing-mobile-menu');
                    if (! menu) return false;
                    const style = getComputedStyle(menu);
                    return style.display !== 'none' && style.visibility !== 'hidden';
                })()`);
                await evaluate(`document.querySelector('.tht-landing-menu-toggle')?.click()`);
            }

            report.push({
                page: name,
                pathname,
                viewport: viewport.name,
                ...top,
                ...end,
                ...interaction,
                networkFailures: [...activeNetworkFailures],
            });
        }
    }

    const summary = {
        generatedAt: new Date().toISOString(),
        baseUrl,
        outputDir,
        pages: pages.length,
        viewports: viewports.map(viewport => viewport.name),
        checks: report.length,
        failures: report.filter(item =>
            item.path !== item.pathname
            || ! item.scrollable
            || item.horizontalOverflow
            || ! item.sharedHeader
            || ! item.sharedFooter
            || (item.viewport === 'mobile' && (item.headerCtaVisible || ! item.menuToggleVisible))
            || (item.viewport === 'desktop' && (! item.headerCtaVisible || item.menuToggleVisible))
            || item.legacyChrome
            || item.brokenImages.length > 0
            || item.networkFailures.length > 0
            || item.bodyOverflowY === 'hidden'
            || ! item.bodyFont.includes('Be Vietnam Pro')
            || ! item.h1?.font.includes('Roboto Condensed Variable')
            || ! item.h2?.font.includes('Roboto Condensed Variable')
            || ! item.h3?.font.includes('Roboto Condensed Variable')
            || item.h1?.size !== (item.viewport === 'mobile' ? '36px' : '64px')
            || item.h2?.size !== (item.viewport === 'mobile' ? '28px' : '44px')
            || item.h3?.size !== '20px'
            || item.mobileInputsBelow16.length > 0
            || (item.modalTested && (! item.modalOpened || ! item.bodyUnlockedAfterClose))
            || (item.viewport === 'mobile' && (! item.mobileMenuOpened || ! item.mobileMenuVisible))
        ),
        report,
    };

    await writeFile(path.join(outputDir, 'report.json'), `${JSON.stringify(summary, null, 2)}\n`);
    process.stdout.write(`${JSON.stringify({
        generatedAt: summary.generatedAt,
        baseUrl: summary.baseUrl,
        outputDir: summary.outputDir,
        pages: summary.pages,
        viewports: summary.viewports,
        checks: summary.checks,
        failureCount: summary.failures.length,
        failures: summary.failures,
    }, null, 2)}\n`);
} finally {
    if (socket?.readyState === WebSocket.OPEN) socket.close();
    chrome.kill();
    await wait(200);
    await rm(profileDir, { recursive: true, force: true });
}
