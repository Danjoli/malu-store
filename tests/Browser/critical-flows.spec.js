import { expect, test } from '@playwright/test';

test('visitante alterna o tema e a preferência permanece salva', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('button', { name: 'Ativar tema escuro' }).click();

    await expect(page.locator('html')).toHaveClass(/dark/);

    await page.reload();
    await expect(page.locator('html')).toHaveClass(/dark/);
    await expect(page.getByRole('button', { name: 'Ativar tema claro' })).toBeVisible();
});

test('textos principais e secundários mantêm contraste nos dois temas', async ({ page }) => {
    await page.goto('/');

    for (const dark of [false, true]) {
        await page.evaluate((nextDark) => window.storeTheme.set(nextDark), dark);

        const ratios = await page.evaluate(() => {
            const styles = getComputedStyle(document.documentElement);
            const parse = (value) => {
                if (value.startsWith('#')) {
                    const hex = value.slice(1);
                    return [0, 2, 4].map((offset) => Number.parseInt(hex.slice(offset, offset + 2), 16));
                }

                const match = value.match(/\d+/g).map(Number);
                return match.slice(0, 3);
            };
            const luminance = (rgb) => {
                const channels = rgb.map((channel) => {
                    const value = channel / 255;
                    return value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
                });
                return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
            };
            const contrast = (foreground, background) => {
                const light = Math.max(luminance(parse(foreground)), luminance(parse(background)));
                const dark = Math.min(luminance(parse(foreground)), luminance(parse(background)));
                return (light + 0.05) / (dark + 0.05);
            };

            const text = styles.getPropertyValue('--store-text').trim();
            const soft = styles.getPropertyValue('--store-text-soft').trim();
            const background = styles.getPropertyValue('--store-bg').trim();
            const surface = styles.getPropertyValue('--store-surface').trim();

            return [
                contrast(text, background),
                contrast(text, surface),
                contrast(soft, background),
                contrast(soft, surface),
            ];
        });

        for (const ratio of ratios) {
            expect(ratio).toBeGreaterThanOrEqual(4.5);
        }
    }
});

test('cabeçalho da autenticação permanece legível nos dois temas', async ({ page }) => {
    await page.goto('/login');

    for (const dark of [false, true]) {
        await page.evaluate((nextDark) => window.storeTheme.set(nextDark), dark);

        const heading = page.getByRole('heading', { name: 'Que bom te ver' });
        const subtitle = page.getByText('Entre para acompanhar seus pedidos e favoritos.');

        await expect(heading).toBeVisible();
        await expect(subtitle).toBeVisible();

        const ratios = await page.locator('.auth-card-header').evaluate((header) => {
            const parse = (value) => value.match(/\d+(?:\.\d+)?/g).slice(0, 3).map(Number);
            const luminance = (value) => {
                const channels = parse(value).map((channel) => {
                    const normalized = channel / 255;
                    return normalized <= 0.04045 ? normalized / 12.92 : ((normalized + 0.055) / 1.055) ** 2.4;
                });
                return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
            };
            const contrast = (foreground, background) => {
                const values = [luminance(foreground), luminance(background)].sort((a, b) => b - a);
                return (values[0] + 0.05) / (values[1] + 0.05);
            };
            const background = getComputedStyle(header).backgroundColor;

            return [...header.querySelectorAll('h1, p')].map((element) =>
                contrast(getComputedStyle(element).color, background),
            );
        });

        for (const ratio of ratios) {
            expect(ratio).toBeGreaterThanOrEqual(4.5);
        }
    }
});

test('produto mantém frete legível e tamanho selecionado destacado nos dois temas', async ({ page }) => {
    await page.goto('/produtos/vestido-midi-floral');

    for (const dark of [false, true]) {
        await page.evaluate((nextDark) => window.storeTheme.set(nextDark), dark);

        const selectedSize = page.getByRole('button', { name: 'Selecionar tamanho M' });
        await selectedSize.click();
        await expect(selectedSize).toHaveAttribute('aria-pressed', 'true');

        const audit = await page.locator('[data-product-shipping]').evaluate((shipping) => {
            const parse = (value) => value.match(/\d+(?:\.\d+)?/g).slice(0, 3).map(Number);
            const luminance = (value) => {
                const channels = parse(value).map((channel) => {
                    const normalized = channel / 255;
                    return normalized <= 0.04045 ? normalized / 12.92 : ((normalized + 0.055) / 1.055) ** 2.4;
                });
                return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
            };
            const contrast = (foreground, background) => {
                const values = [luminance(foreground), luminance(background)].sort((a, b) => b - a);
                return (values[0] + 0.05) / (values[1] + 0.05);
            };
            const background = getComputedStyle(shipping).backgroundColor;

            return [...shipping.querySelectorAll('h2, p')].map((element) =>
                contrast(getComputedStyle(element).color, background),
            );
        });

        for (const ratio of audit) {
            expect(ratio).toBeGreaterThanOrEqual(4.5);
        }

        const sizeContrast = await selectedSize.evaluate((button) => {
            const parse = (value) => value.match(/\d+(?:\.\d+)?/g).slice(0, 3).map(Number);
            const luminance = (value) => {
                const channels = parse(value).map((channel) => {
                    const normalized = channel / 255;
                    return normalized <= 0.04045 ? normalized / 12.92 : ((normalized + 0.055) / 1.055) ** 2.4;
                });
                return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
            };
            const styles = getComputedStyle(button);
            const values = [luminance(styles.color), luminance(styles.backgroundColor)].sort((a, b) => b - a);

            return (values[0] + 0.05) / (values[1] + 0.05);
        });

        expect(sizeContrast).toBeGreaterThanOrEqual(4.5);
    }
});

async function loginAsCustomer(page) {
    await page.goto('/login');
    await page.getByLabel('E-mail').fill('test@gmail.com');
    await page.getByLabel('Senha').fill('Senha@2026');
    await page.getByRole('button', { name: 'Entrar' }).click();
    await expect(page).toHaveURL(/\/$/);
}

test('cliente pode se cadastrar e entrar com dados fictícios', async ({ page }) => {
    await page.goto('/register');
    await page.getByLabel('Nome completo').fill('Cliente Teste Navegador');
    await page.getByLabel('E-mail').fill('cliente.browser@example.test');
    await page.getByLabel('Telefone').fill('(11) 98888-7777');
    await page.getByLabel('Senha').fill('Teste@2026');
    await page.getByRole('button', { name: 'Criar conta' }).click();

    await expect(page).toHaveURL(/\/$/);
    await expect(page.getByText('Cliente Teste Navegador')).toBeVisible();
});

test('cliente inclui produto na sacola e chega à escolha do pagamento', async ({ page }) => {
    await loginAsCustomer(page);
    await page.goto('/produtos/vestido-midi-floral');
    await page.getByRole('button', { name: /Adicionar à sacola/ }).click();

    await expect(page.getByRole('link', { name: 'Sacola' })).toContainText('2');
    await page.goto('/cart');
    await expect(page.getByText('Vestido Midi Floral')).toBeVisible();
    await page.getByRole('link', { name: /Finalizar compra/i }).click();

    await expect(page).toHaveURL(/\/checkout/);
    await expect(page.getByText('Forma de pagamento')).toBeVisible();
    await expect(page.locator('input[name="payment_method"]')).toHaveCount(3);
});

test('administrador entra e consulta pedidos fictícios', async ({ page }) => {
    await page.goto('/admin/login');
    await page.getByLabel('E-mail').fill('admin@malustore.test');
    await page.getByLabel('Senha').fill('Senha@2026');
    await page.getByRole('button', { name: 'Entrar no painel' }).click();

    await expect(page).toHaveURL(/\/admin/);
    await page.goto('/admin/orders');
    await expect(page.getByRole('heading', { name: /Pedidos/ })).toBeVisible();
    await expect(page.getByRole('cell', { name: 'test', exact: true }).first()).toBeVisible();
});
