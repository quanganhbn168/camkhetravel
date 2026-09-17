import { defineConfig, devices } from '@playwright/test';
export default defineConfig({
    testDir: './tests/Browser',
    outputDir: 'test-results/browser-artifacts',
    testMatch: '**/*.spec.js',
    timeout: 45000,
    workers: 1,
    reporter: [['list'], ['json', { outputFile: 'test-results/browser-results.json' }]],
    use: { baseURL: 'http://127.0.0.1:8000', screenshot: 'only-on-failure', trace: 'retain-on-failure' },
    projects: [
        { name: 'desktop', use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 1000 } } },
        { name: 'mobile', use: { ...devices['iPhone 13'], defaultBrowserType: 'chromium' } },
    ],
});
