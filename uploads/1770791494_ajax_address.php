<?php
require_once 'db.php';

if (isset($_POST['action'])) {
    
    // Fetch Municipalities/Cities and their ZIP Code based on Province Code
    if ($_POST['action'] == 'get_mun' && !empty($_POST['prov_code'])) {
        $stmt = $pdo->prepare("SELECT mun_code, muncity_name, zip_code FROM muncity WHERE prov_code = ? ORDER BY muncity_name ASC");
        $stmt->execute([$_POST['prov_code']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
    
    // Fetch Barangays based on Municipality Code
    if ($_POST['action'] == 'get_brgy' && !empty($_POST['mun_code'])) {
        $stmt = $pdo->prepare("SELECT brgy_code, brgy_name FROM brgy WHERE mun_code = ? ORDER BY brgy_name ASC");
        $stmt->execute([$_POST['mun_code']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }
}
?>