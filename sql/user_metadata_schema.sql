
CREATE TABLE IF NOT EXISTS user_metadata (
    user_id VARCHAR(255) NOT NULL,
    meta_key VARCHAR(255) NOT NULL,
    meta_value TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, meta_key),
    INDEX idx_user_id (user_id),
    CONSTRAINT fk_user_metadata_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
