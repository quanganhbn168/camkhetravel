import { test, expect } from '@playwright/test';
import fs from 'node:fs';
const routes = JSON.parse(fs.readFileSync('test-results/browser-routes.json', 'utf8'));
for (const route of routes) {
    test(`renders ${route} without frontend errors`, async ({ page }, testInfo) => {
        const errors = [];
        const broken = [];
        page.on('pageerror', (error) => errors.push(error.message));
        page.on('response', (response) => {
            if (response.url().includes('127.0.0.1') && response.status() >= 400) broken.push(`${response.status()} ${response.url()}`);
        });
        const response = await page.goto(route, { waitUntil: 'networkidle' });
        expect(response.status()).toBe(200);
        await expect(page.locator('body')).toBeVisible();
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth + 2)).toBe(true);
        expect(errors).toEqual([]);
        expect(broken).toEqual([]);
        if (route === '/') {
            await expect(page.locator('h1')).toHaveCount(1);
            for (const selector of ['.tab-pane.active .dv-service-image', '.dv-about-main', '.dv-about-side:not(.dv-about-quote)']) {
                const frame = page.locator(selector).first();
                const image = frame.locator('img');
                await frame.scrollIntoViewIfNeeded();
                await expect(image).toBeVisible();
                const bounds = await frame.boundingBox();
                const imageBounds = await image.boundingBox();
                expect(Math.abs(bounds.height - imageBounds.height)).toBeLessThanOrEqual(2);
                expect(Math.abs(bounds.width - imageBounds.width)).toBeLessThanOrEqual(2);
            }
            await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
            await page.screenshot({ path: `test-results/home-${testInfo.project.name}.png`, fullPage: true });
            const assets = await page.evaluate(() => performance.getEntriesByType('resource').filter((entry) => /\.(css|js|woff2)(\?|$)/.test(entry.name)).map((entry) => entry.name));
            expect(assets.some((asset) => asset.includes('/theme-'))).toBe(false);
            fs.writeFileSync(`test-results/network-${testInfo.project.name}.json`, JSON.stringify(assets, null, 2));
        }
    });
}

test('header search opens, focuses and closes with Escape', async ({ page }) => {
    await page.goto('/');
    await page.locator('[data-bs-target="#header-search-modal"]').click();
    await expect(page.locator('#header-search-query')).toBeFocused();
    await page.keyboard.press('Escape');
    await expect(page.locator('#header-search-modal')).not.toBeVisible();
});

test('navigation supports desktop dropdown or mobile offcanvas', async ({ page }, testInfo) => {
    await page.goto('/');
    if (testInfo.project.name === 'mobile') {
        await page.locator('[data-bs-target="#mobile-drawer"]').click();
        await expect(page.locator('#mobile-drawer')).toBeVisible();
        const toggle = page.locator('#mobile-drawer [data-bs-toggle="collapse"]').first();
        await toggle.click();
        await expect(page.locator('#mobile-drawer a', { hasText: 'Hạng mục kiểm thử' })).toBeVisible();
        await page.keyboard.press('Escape');
        await expect(page.locator('#mobile-drawer')).not.toBeVisible();
    } else {
        await page.locator('.site-nav__toggle').first().click();
        await expect(page.locator('.site-nav .dropdown-menu.show')).toBeVisible();
        await page.keyboard.press('Escape');
        await expect(page.locator('.site-nav .dropdown-menu.show')).toHaveCount(0);
    }
});

test('service tabs and FAQ keep their state and visible content', async ({ page }) => {
    await page.goto('/');
    const secondTab = page.locator('.dv-tabs [data-bs-toggle="tab"]').nth(1);
    await secondTab.click();
    await expect(secondTab).toHaveAttribute('aria-selected', 'true');
    await expect(page.locator('.dv-tabs + .tab-content .tab-pane.show')).toHaveCount(1);
    const question = page.locator('#homeFaq .accordion-button').nth(1);
    await question.click();
    await expect(question).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('#faq-1')).toBeVisible();
});

test('homepage form submits a real JSON request with CSRF', async ({ page }) => {
    await page.goto('/');
    await page.locator('#home-name').fill('Khách kiểm thử giao diện');
    await page.locator('#home-phone').fill('0900000000');
    await page.locator('#home-message').fill('Đây là yêu cầu kiểm thử tự động.');
    const result = page.waitForResponse((response) => response.url().endsWith('/lien-he') && response.request().method() === 'POST');
    await page.locator('.dv-contact-form button[type="submit"]').click();
    expect((await result).status()).toBe(200);
    await expect(page.locator('.dv-contact-form [data-form-success]')).toBeVisible();
});
