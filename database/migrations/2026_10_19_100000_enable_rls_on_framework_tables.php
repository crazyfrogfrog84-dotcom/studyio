<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Laravel's unused scaffold tables also live in Supabase's public schema.
     * The application connects as the database owner; API roles get no policies.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE
                table_name text;
                policy_name text;
            BEGIN
                FOREACH table_name IN ARRAY ARRAY[
                    'cache',
                    'cache_locks',
                    'failed_jobs',
                    'job_batches',
                    'jobs',
                    'migrations',
                    'password_reset_tokens',
                    'sessions',
                    'users'
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
        DB::unprepared(<<<'SQL'
            ALTER TABLE public.cache DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.cache_locks DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.failed_jobs DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.job_batches DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.jobs DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.migrations DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.password_reset_tokens DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.sessions DISABLE ROW LEVEL SECURITY;
            ALTER TABLE public.users DISABLE ROW LEVEL SECURITY;
            SQL);
    }
};
