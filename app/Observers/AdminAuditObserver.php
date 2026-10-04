<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

class AdminAuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = array_keys($model->getChanges());

        $this->record(
            $model,
            'updated',
            array_intersect_key($model->getRawOriginal(), array_flip($changes)),
            array_intersect_key($model->getAttributes(), array_flip($changes)),
            $changes,
        );
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $model->getRawOriginal(), null);
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     * @param  array<int, string>|null  $changedFields
     */
    private function record(
        Model $model,
        string $action,
        ?array $before,
        ?array $after,
        ?array $changedFields = null,
    ): void {
        /** @var Admin|null $admin */
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return;
        }

        /** @var array<int, string> $allowedAttributes */
        $allowedAttributes = config('audit.attributes.'.get_class($model), []);
        /** @var array<int, string> $sensitiveAttributes */
        $sensitiveAttributes = config('audit.sensitive_attributes.'.get_class($model), []);
        $trackableAttributes = [...$allowedAttributes, ...$sensitiveAttributes];
        $visibleBefore = $this->onlyAllowed($before, $allowedAttributes);
        $visibleAfter = $this->onlyAllowed($after, $allowedAttributes);
        $allChangedFields = $changedFields ?? array_keys($before ?? $after ?? []);

        AuditLog::query()->create([
            'admin_id' => $admin->getKey(),
            'action' => $action,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'changed_fields' => array_values(array_intersect($allChangedFields, $trackableAttributes)),
            'before' => $visibleBefore ?: null,
            'after' => $visibleAfter ?: null,
            'request_id' => Context::get('request_id'),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $values
     * @param  array<int, string>  $allowed
     * @return array<string, mixed>|null
     */
    private function onlyAllowed(?array $values, array $allowed): ?array
    {
        return $values === null ? null : array_intersect_key($values, array_flip($allowed));
    }
}
