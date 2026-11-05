<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Unique indexes that must be scoped per tenant because all tenants share one database.
     *
     * @var array<string, array{index: string, columns: array<int, string>}>
     */
    private array $indexes = [
        'payroll_periods' => [
            'index' => 'payroll_periods_month_year_unique',
            'columns' => ['month', 'year'],
        ],
        'payroll_components' => [
            'index' => 'payroll_components_code_unique',
            'columns' => ['code'],
        ],
        'qr_codes' => [
            'index' => 'qr_codes_code_unique',
            'columns' => ['code'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $tableName => $config) {
            if (! $this->canBeRescoped($tableName, $config['index'])) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $config) {
                $table->dropUnique($config['index']);
                $table->unique(array_merge($config['columns'], ['tenant_id']), $this->tenantIndexName($tableName, $config['columns']));
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $tableName => $config) {
            $tenantIndex = $this->tenantIndexName($tableName, $config['columns']);

            if (! Schema::hasTable($tableName) || ! Schema::hasIndex($tableName, $tenantIndex)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($config, $tenantIndex) {
                $table->dropUnique($tenantIndex);
                $table->unique($config['columns'], $config['index']);
            });
        }
    }

    /**
     * The index can only be rescoped when the table, the tenant_id column and the original index exist.
     *
     * @param  array{index: string, columns: array<int, string>}  $config
     */
    private function canBeRescoped(string $tableName, string $index): bool
    {
        return Schema::hasTable($tableName)
            && Schema::hasColumn($tableName, 'tenant_id')
            && Schema::hasIndex($tableName, $index);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function tenantIndexName(string $tableName, array $columns): string
    {
        return $tableName.'_'.implode('_', $columns).'_tenant_id_unique';
    }
};
