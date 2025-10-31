#!/bin/bash

DB_PATH="/var/www/data/users.db"

# Only create database if it doesn't exist
if [ ! -f "$DB_PATH" ]; then
    echo "Creating database..."
    sqlite3 "$DB_PATH" << EOF
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT NOT NULL,
    api_key TEXT NOT NULL
);

INSERT INTO users (username, password, email, role, api_key) VALUES
    ('admin', 'SuperSecret123!', 'admin@internal.local', 'administrator', 'sk_live_abc123xyz'),
    ('dbadmin', 'P@ssw0rd!', 'dbadmin@internal.local', 'database_admin', 'sk_live_def456uvw'),
    ('backup_service', 'BackupKey9000', 'backup@internal.local', 'service_account', 'sk_live_ghi789rst');

CREATE TABLE sensitive_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    data_type TEXT NOT NULL,
    value TEXT NOT NULL
);

INSERT INTO sensitive_data (data_type, value) VALUES
    ('aws_access_key', 'AKIAIOSFODNN7EXAMPLE'),
    ('aws_secret_key', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'),
    ('stripe_secret', 'sk_live_51AbCdEfGhIjKlMnOpQrStUvWxYz'),
    ('internal_api_token', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...');
EOF

    chown www-data:www-data "$DB_PATH"
    chmod 644 "$DB_PATH"
    echo "Database created successfully!"
else
    echo "Database already exists, skipping initialization."
fi