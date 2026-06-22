<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

trait HandlesAdminForms
{
    protected function normalizeNullableIds(string ...$fields): void
    {
        foreach ($fields as $field) {
            if (! property_exists($this, $field)) {
                continue;
            }

            $value = $this->{$field};

            if ($value === '' || $value === '0' || $value === 0) {
                $this->{$field} = null;
            }
        }
    }

    protected function handleSaveException(QueryException $e): void
    {
        if ($e->getCode() === '23000') {
            throw ValidationException::withMessages([
                'form' => 'This record already exists or cannot be saved. Check duplicate names or flat numbers.',
            ]);
        }

        throw $e;
    }

    protected function ensureSocietyContext(): void
    {
        if (! auth()->user()?->society_id) {
            throw ValidationException::withMessages([
                'form' => 'No society linked to your account. Contact Super Admin.',
            ]);
        }
    }
}
