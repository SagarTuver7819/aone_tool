<?php
require_once 'config.php';
$sql = "CREATE TABLE IF NOT EXISTS amazon_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    report_date DATE NOT NULL,
    sku VARCHAR(100),
    asin VARCHAR(50),
    fnsku VARCHAR(50),
    product_name TEXT,
    condition_type VARCHAR(50),
    your_price DECIMAL(15,2),
    mfn_listing_exists VARCHAR(10),
    mfn_fulfillable_quantity INT,
    afn_listing_exists VARCHAR(10),
    afn_warehouse_quantity INT,
    afn_fulfillable_quantity INT,
    afn_unsellable_quantity INT,
    afn_reserved_quantity INT,
    afn_total_quantity INT,
    afn_inbound_working_quantity INT,
    afn_inbound_shipped_quantity INT,
    afn_inbound_receiving_quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (customer_id),
    INDEX (report_date),
    INDEX (sku),
    UNIQUE KEY (customer_id, report_date, sku)
)";
if ($conn->query($sql)) {
    echo "Table amazon_inventory created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
