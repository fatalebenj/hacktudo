<?php
require_once __DIR__ . '/../includes/DeviceDetector.php';
$detector = new DeviceDetector();
$device   = $detector->getDeviceType();
$pageTitle = 'Sobre - EduPlataforma';

include __DIR__ . '/../includes/header.php';
?>
<section>
    <h1>Sobre a EduPlataforma</h1>
    <p>Texto institucional aqui.</p>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
