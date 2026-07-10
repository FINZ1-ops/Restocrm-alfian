<?php
$db = new mysqli('localhost', 'root', '', 'restocrm');

// Test the exact query that getAllWithDetails runs
$sql = "SELECT sp.*, r.name as restaurant_name, r.whatsapp as restaurant_whatsapp, sub.plan_id 
        FROM subscription_payments sp 
        LEFT JOIN restaurants r ON r.id = sp.restaurant_id 
        LEFT JOIN restaurant_subscriptions sub ON sub.id = sp.subscription_id 
        ORDER BY sp.created_at DESC";

$r = $db->query($sql);
if ($r === false) {
    echo "ERROR: " . $db->error . PHP_EOL;
} else {
    echo "SUCCESS! Rows: " . $r->num_rows . PHP_EOL;
    while ($row = $r->fetch_assoc()) {
        echo json_encode($row) . PHP_EOL;
    }
}

$db->close();
