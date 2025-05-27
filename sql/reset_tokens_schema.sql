
CREATE TABLE IF NOT EXISTS reset_tokens (
    user_id VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at),
    CONSTRAINT fk_reset_tokens_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
