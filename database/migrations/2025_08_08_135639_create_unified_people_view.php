<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW unified_people AS

            -- Members
            SELECT 
                CONCAT('M-', m.id) AS uid,
                m.id AS original_id,
                m.first_name,
                m.last_name,
                m.family_no,
                m.gender_id,
                'Member' AS source,

                CASE
                    WHEN m.father_id IS NOT NULL AND m.father_source = 'Member' THEN CONCAT('M-', m.father_id)
                    WHEN m.father_id IS NOT NULL AND m.father_source = 'External' THEN CONCAT('E-', m.father_id)
                    ELSE NULL
                END AS father_uid,

                CASE
                    WHEN m.mother_id IS NOT NULL AND m.mother_source = 'Member' THEN CONCAT('M-', m.mother_id)
                    WHEN m.mother_id IS NOT NULL AND m.mother_source = 'External' THEN CONCAT('E-', m.mother_id)
                    ELSE NULL
                END AS mother_uid,

                CASE
                    WHEN m.spouse_id IS NOT NULL AND m.spouse_source = 'Member' THEN CONCAT('M-', m.spouse_id)
                    WHEN m.spouse_id IS NOT NULL AND m.spouse_source = 'External' THEN CONCAT('E-', m.spouse_id)
                    ELSE NULL
                END AS spouse_uid

            FROM members m

            UNION ALL

            -- External Members
            SELECT 
                CONCAT('E-', e.id) AS uid,
                e.id AS original_id,
                e.first_name,
                e.last_name,
                e.family_no,
                e.gender_id,
                'External' AS source,

                CASE
                    WHEN e.father_id IS NOT NULL AND e.father_source = 'Member' THEN CONCAT('M-', e.father_id)
                    WHEN e.father_id IS NOT NULL AND e.father_source = 'External' THEN CONCAT('E-', e.father_id)
                    ELSE NULL
                END AS father_uid,

                CASE
                    WHEN e.mother_id IS NOT NULL AND e.mother_source = 'Member' THEN CONCAT('M-', e.mother_id)
                    WHEN e.mother_id IS NOT NULL AND e.mother_source = 'External' THEN CONCAT('E-', e.mother_id)
                    ELSE NULL
                END AS mother_uid,

                CASE
                    WHEN e.spouse_id IS NOT NULL AND e.spouse_source = 'Member' THEN CONCAT('M-', e.spouse_id)
                    WHEN e.spouse_id IS NOT NULL AND e.spouse_source = 'External' THEN CONCAT('E-', e.spouse_id)
                    ELSE NULL
                END AS spouse_uid

            FROM external_members e
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS unified_people");
    }
};

