<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_catalog_change_records_before_after_and_request_id(): void
    {
        $admin = $this->admin('actor@example.com');
        $product = Product::factory()->create(['price' => 100]);
        $requestId = (string) Str::uuid();

        $this->actingAs($admin, 'admin');
        Context::add('request_id', $requestId);

        $product->update(['price' => 125]);

        $audit = AuditLog::query()->sole();
        $this->assertSame($admin->id, $audit->admin_id);
        $this->assertSame('updated', $audit->action);
        $this->assertSame(Product::class, $audit->auditable_type);
        $this->assertSame($product->id, $audit->auditable_id);
        $this->assertSame(['price'], $audit->changed_fields);
        $this->assertSame(['price' => 100], $audit->before);
        $this->assertSame(['price' => 125], $audit->after);
        $this->assertSame($requestId, $audit->request_id);
    }

    public function test_sensitive_admin_values_are_never_stored(): void
    {
        $actor = $this->admin('actor@example.com');
        $target = $this->admin('target@example.com');
        $this->actingAs($actor, 'admin');

        $target->update([
            'name' => 'Novo nome',
            'password' => 'new-password-hash',
        ]);

        $audit = AuditLog::query()->sole();
        $payload = json_encode([$audit->before, $audit->after, $audit->changed_fields]);

        $this->assertSame(['name', 'password'], $audit->changed_fields);
        $this->assertStringNotContainsString('new-password-hash', (string) $payload);
    }

    public function test_background_changes_without_an_admin_are_not_audited(): void
    {
        Product::factory()->create();

        $this->assertDatabaseCount('audit_logs', 0);
    }

    private function admin(string $email): Admin
    {
        return Admin::query()->create([
            'name' => 'Admin',
            'email' => $email,
            'password' => 'password-hash',
            'is_active' => true,
            'role' => 'admin',
        ]);
    }
}
