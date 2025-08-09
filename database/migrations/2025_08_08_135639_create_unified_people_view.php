<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private function indexNamedExists(string $table, string $name): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $name)
            ->exists();
    }

    private function anyIndexOnColumn(string $table, string $column): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->exists();
    }

    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS unified_people");
        DB::statement("
CREATE OR REPLACE VIEW unified_people AS
SELECT
  CONCAT('M-', id)        AS uid,
  first_name,
  last_name,
  gender_id,
  CONCAT('M-', father_id) AS father_uid,
  CONCAT('M-', mother_id) AS mother_uid,
  CONCAT('M-', spouse_id) AS spouse_uid,
  family_no,
  member_no,              -- from members
  'Member'                AS source
FROM members
UNION ALL
SELECT
  CONCAT('E-', id)        AS uid,
  first_name,
  last_name,
  gender_id,
  CONCAT('E-', father_id) AS father_uid,
  CONCAT('E-', mother_id) AS mother_uid,
  CONCAT('E-', spouse_id) AS spouse_uid,
  family_no,
  external_member_no AS member_no,      -- or use external_members.member_no if it exists
  'External'              AS source
FROM external_members
");
    }

    private function foreignExists(string $table, string $column): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('column_name', $column)
            ->whereNotNull('constraint_name')
            ->where('referenced_table_name', '!=', null)
            ->exists();
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS unified_people");
    }
};

