# Database Schema Documentation

This document describes the database schema used by the PHP Authentication Library.

## Tables

### user_credentials

Stores user authentication credentials.

#### MySQL Schema

```sql
CREATE TABLE user_credentials (
    user_id VARCHAR(255) NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### PostgreSQL Schema

```sql
CREATE TABLE user_credentials (
    user_id VARCHAR(255) NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
);

-- Trigger to update updated_at column
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_user_credentials_updated_at
BEFORE UPDATE ON user_credentials
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();
```

### reset_tokens

Stores password reset tokens.

#### MySQL Schema

```sql
CREATE TABLE reset_tokens (
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
```

#### PostgreSQL Schema

```sql
CREATE TABLE reset_tokens (
    user_id VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_reset_tokens_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
);

CREATE INDEX idx_token ON reset_tokens (token);
CREATE INDEX idx_expires_at ON reset_tokens (expires_at);
```

### user_metadata

Stores additional user metadata.

#### MySQL Schema

```sql
CREATE TABLE user_metadata (
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
```

#### PostgreSQL Schema

```sql
CREATE TABLE user_metadata (
    user_id VARCHAR(255) NOT NULL,
    meta_key VARCHAR(255) NOT NULL,
    meta_value TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, meta_key),
    CONSTRAINT fk_user_metadata_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
);

CREATE INDEX idx_user_metadata_user_id ON user_metadata (user_id);

CREATE TRIGGER update_user_metadata_updated_at
BEFORE UPDATE ON user_metadata
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();
```

### sso_providers (Future Extension)

Stores SSO provider linkages for users.

#### MySQL Schema

```sql
CREATE TABLE sso_providers (
    user_id VARCHAR(255) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    provider_data TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, provider),
    UNIQUE INDEX idx_provider_user_id (provider, provider_user_id),
    CONSTRAINT fk_sso_providers_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### PostgreSQL Schema

```sql
CREATE TABLE sso_providers (
    user_id VARCHAR(255) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    provider_data TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, provider),
    CONSTRAINT fk_sso_providers_user_id FOREIGN KEY (user_id)
        REFERENCES user_credentials (user_id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX idx_provider_user_id ON sso_providers (provider, provider_user_id);

CREATE TRIGGER update_sso_providers_updated_at
BEFORE UPDATE ON sso_providers
FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();
```

## Indexes

The schema includes the following indexes for optimal performance:

- Primary key on `user_id` in `user_credentials`
- Primary key on `user_id` in `reset_tokens`
- Index on `token` in `reset_tokens`
- Index on `expires_at` in `reset_tokens`
- Composite primary key on `user_id` and `meta_key` in `user_metadata`
- Index on `user_id` in `user_metadata`
- Composite primary key on `user_id` and `provider` in `sso_providers`
- Unique index on `provider` and `provider_user_id` in `sso_providers`

## Foreign Keys

The schema includes the following foreign key constraints:

- `reset_tokens.user_id` references `user_credentials.user_id` (CASCADE on DELETE)
- `user_metadata.user_id` references `user_credentials.user_id` (CASCADE on DELETE)
- `sso_providers.user_id` references `user_credentials.user_id` (CASCADE on DELETE)

These constraints ensure referential integrity and automatically clean up related data when a user is deleted.
