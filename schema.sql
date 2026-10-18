-- StuDyIo public PostgreSQL schema reference
-- Source: database/migrations/*.php. Run Laravel migrations for the authoritative schema.
-- Supabase Storage bucket portfolio-assets is configured outside this SQL schema.

CREATE TABLE users (
    id bigserial PRIMARY KEY,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL UNIQUE,
    email_verified_at timestamp without time zone NULL,
    password varchar(255) NOT NULL,
    remember_token varchar(100) NULL,
    created_at timestamp without time zone NULL,
    updated_at timestamp without time zone NULL
);

CREATE TABLE password_reset_tokens (
    email varchar(255) PRIMARY KEY,
    token varchar(255) NOT NULL,
    created_at timestamp without time zone NULL
);

CREATE TABLE sessions (
    id varchar(255) PRIMARY KEY,
    user_id bigint NULL,
    ip_address varchar(45) NULL,
    user_agent text NULL,
    payload text NOT NULL,
    last_activity integer NOT NULL
);
CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE cache (
    key varchar(255) PRIMARY KEY,
    value text NOT NULL,
    expiration bigint NOT NULL
);
CREATE INDEX cache_expiration_index ON cache(expiration);

CREATE TABLE cache_locks (
    key varchar(255) PRIMARY KEY,
    owner varchar(255) NOT NULL,
    expiration bigint NOT NULL
);
CREATE INDEX cache_locks_expiration_index ON cache_locks(expiration);

CREATE TABLE jobs (
    id bigserial PRIMARY KEY,
    queue varchar(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer NULL,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);
CREATE INDEX jobs_queue_index ON jobs(queue);

CREATE TABLE job_batches (
    id varchar(255) PRIMARY KEY,
    name varchar(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text NULL,
    cancelled_at integer NULL,
    created_at integer NOT NULL,
    finished_at integer NULL
);

CREATE TABLE failed_jobs (
    id bigserial PRIMARY KEY,
    uuid varchar(255) NOT NULL UNIQUE,
    connection varchar(255) NOT NULL,
    queue varchar(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX failed_jobs_connection_queue_failed_at_index ON failed_jobs(connection, queue, failed_at);

CREATE TABLE migrations (
    id bigserial PRIMARY KEY,
    migration varchar(255) NOT NULL,
    batch integer NOT NULL
);

CREATE TABLE templates (
    id bigserial PRIMARY KEY,
    slug varchar(40) NOT NULL UNIQUE,
    name varchar(80) NOT NULL,
    description text NOT NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

CREATE TABLE portfolios (
    id bigserial PRIMARY KEY,
    user_id bigint NULL REFERENCES users(id) ON DELETE CASCADE,
    full_name varchar(160) NOT NULL,
    profile_picture_path varchar(1024) NULL,
    email varchar(255) NOT NULL,
    contact_number varchar(60) NULL,
    address text NULL,
    about_me text NULL,
    template_id bigint NULL REFERENCES templates(id) ON DELETE SET NULL,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

CREATE TABLE education (
    id bigserial PRIMARY KEY,
    portfolio_id bigint NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    institution varchar(180) NULL,
    qualification varchar(180) NULL,
    field_of_study varchar(180) NULL,
    start_date date NULL,
    end_date date NULL,
    description text NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

CREATE TABLE skills (
    id bigserial PRIMARY KEY,
    portfolio_id bigint NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    name varchar(120) NOT NULL,
    proficiency varchar(60) NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

CREATE TABLE projects (
    id bigserial PRIMARY KEY,
    portfolio_id bigint NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    title varchar(180) NOT NULL,
    description text NULL,
    project_url varchar(2048) NULL,
    screenshot_path varchar(1024) NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL,
    tags json NULL
);

CREATE TABLE work_experience (
    id bigserial PRIMARY KEY,
    portfolio_id bigint NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    company varchar(180) NULL,
    role varchar(180) NULL,
    start_date date NULL,
    end_date date NULL,
    description text NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

CREATE TABLE social_links (
    id bigserial PRIMARY KEY,
    portfolio_id bigint NOT NULL REFERENCES portfolios(id) ON DELETE CASCADE,
    platform varchar(80) NULL,
    label varchar(120) NULL,
    url varchar(2048) NOT NULL,
    sort_order smallint NOT NULL DEFAULT 0,
    created_at timestamptz NULL,
    updated_at timestamptz NULL
);

-- Enable RLS and leave all policies absent. Laravel uses a server-side
-- pooler database connection; browser API roles receive no policies.
ALTER TABLE portfolios ENABLE ROW LEVEL SECURITY;
ALTER TABLE education ENABLE ROW LEVEL SECURITY;
ALTER TABLE skills ENABLE ROW LEVEL SECURITY;
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;
ALTER TABLE work_experience ENABLE ROW LEVEL SECURITY;
ALTER TABLE social_links ENABLE ROW LEVEL SECURITY;
ALTER TABLE templates ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache ENABLE ROW LEVEL SECURITY;
ALTER TABLE cache_locks ENABLE ROW LEVEL SECURITY;
ALTER TABLE failed_jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE job_batches ENABLE ROW LEVEL SECURITY;
ALTER TABLE jobs ENABLE ROW LEVEL SECURITY;
ALTER TABLE migrations ENABLE ROW LEVEL SECURITY;
ALTER TABLE password_reset_tokens ENABLE ROW LEVEL SECURITY;
ALTER TABLE sessions ENABLE ROW LEVEL SECURITY;
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

-- Match TemplateSeeder by removing any template outside the three supported slugs.
DELETE FROM templates WHERE slug NOT IN ('simple', 'modern', 'creative');

-- TemplateSeeder is idempotent and maintains exactly these three templates.
INSERT INTO templates (slug, name, description, sort_order, created_at, updated_at)
VALUES
    ('simple', 'Simple', 'An editorial, professional portfolio.', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('modern', 'Modern', 'A card-based layout with clear sections and visual detail.', 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
    ('creative', 'Creative', 'An expressive split layout with a bold, editorial feel.', 3, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
ON CONFLICT (slug) DO UPDATE SET
    name = EXCLUDED.name,
    description = EXCLUDED.description,
    sort_order = EXCLUDED.sort_order,
    updated_at = CURRENT_TIMESTAMP;




