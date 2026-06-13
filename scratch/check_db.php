<?php
require_once __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS file_upload_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    zip_filename VARCHAR(255) NULL,
    filename VARCHAR(255) NOT NULL,
    report_type VARCHAR(100) NOT NULL,
    report_date DATE NOT NULL,
    rows_processed INT NOT NULL DEFAULT 0,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "Table 'file_upload_log' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
