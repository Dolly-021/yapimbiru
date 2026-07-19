<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=db_simak_smk', 'root', '');
    
    echo "=== All Tables ===\n";
    $stmt = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'db_simak_smk' ORDER BY TABLE_NAME");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['TABLE_NAME'] . "\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
