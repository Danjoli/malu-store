import { expect, test } from '@playwright/test';

test('visitante alterna o tema e a preferência permanece salva', async ({ page }) => {
    await page.goto('/');
    await page.getByRole('button', { name: 'Ativar tema escuro' }).click();

    await expect(page.locator('html')).toHaveClass(/dark/);

    await page.reload();
    await expect(page.locator('html')).toHaveClass(/dark/);
    await expect(page.getByRole('button', { name: 'Ativar tema claro' })).toBeVisible();
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
