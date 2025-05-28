
CREATE TABLE IF NOT EXISTS reset_tokens (
    user_id VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_reset_tokens_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_token ON reset_tokens (token);
CREATE INDEX IF NOT EXISTS idx_expires_at ON reset_tokens (expires_at);
