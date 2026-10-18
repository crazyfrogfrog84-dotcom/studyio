<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The application connects with the database pooler, while browser clients
     * must not access portfolio data through the Supabase anon key.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE
                table_name text;
                policy_name text;
            BEGIN
                FOREACH table_name IN ARRAY ARRAY[
                    'portfolios',
                    'education',
                    'skills',
                    'projects',
                    'work_experience',
                    'social_links',
                    'templates'
                ]
                LOOP
                    FOR policy_name IN
                        SELECT policyname
                        FROM pg_policies
                        WHERE schemaname = 'public'
                          AND tablename = table_name
                    LOOP
                        EXECUTE format(
                            'DROP POLICY IF EXISTS %I ON public.%I',
                            policy_name,
                            table_name
                        );
                    END LOOP;

                    EXECUTE format(
                        'ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY',
                        table_name
                    );
                END LOOP;
            END
            $$;
            SQL);
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            ALTER TABLE public.portfolios DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.education DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.skills DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.projects DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.work_experience DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.social_links DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.templates DISABLE ROW LEVEL SECURITY;
            SQL);
    }
};
