
CREATE TABLE IF NOT EXISTS user_metadata (
    user_id VARCHAR(255) NOT NULL,
    meta_key VARCHAR(255) NOT NULL,
    meta_value TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, meta_key),
    CONSTRAINT fk_user_metadata_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_user_metadata_user_id ON user_metadata (user_id);

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_proc WHERE proname = 'update_updated_at_column') THEN
        CREATE FUNCTION update_updated_at_column()
        RETURNS TRIGGER AS $$
        BEGIN
            NEW.updated_at = NOW();
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
    END IF;
END
$$;

DROP TRIGGER IF EXISTS update_user_metadata_updated_at ON user_metadata;
CREATE TRIGGER update_user_metadata_updated_at
BEFORE UPDATE ON user_metadata
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();
