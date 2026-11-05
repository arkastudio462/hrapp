<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tenant::settings and Tenant::limits are virtual columns serialized into
     * the tenants.data JSON column. They were also declared as 'array' casts,
     * so every save round-trip JSON encoded them one extra time until the
     * value nested dozens of layers deep (and eventually exhausted memory).
     *
     * This unwraps the corrupted values back to plain arrays.
     */
    public function up(): void
    {
        DB::table('tenants')->orderBy('id')->chunkById(50, function ($tenants) {
            foreach ($tenants as $tenant) {
                $data = json_decode((string) $tenant->data, true);

                if (! is_array($data)) {
                    continue;
                }

                $changed = false;

                foreach (['settings', 'limits'] as $key) {
                    if (! isset($data[$key]) || ! is_string($data[$key])) {
                        continue;
                    }

                    $value = $this->unwrap($data[$key]);

                    if (is_array($value)) {
                        $data[$key] = $value;
                        $changed = true;
                    }
                }

                if ($changed) {
                    DB::table('tenants')->where('id', $tenant->id)->update([
                        'data' => json_encode($data),
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data repair is not reversible.
    }

    /**
     * Decode a JSON string repeatedly until a non-string value is reached.
     */
    private function unwrap(mixed $value, int $maxDepth = 50): mixed
    {
        for ($depth = 0; $depth < $maxDepth && is_string($value); $depth++) {
            $decoded = json_decode($value, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // Not JSON (anymore) - leave the original value untouched.
                return null;
            }

            $value = $decoded;
        }

        return $value;
    }
};
