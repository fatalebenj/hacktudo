<?php
require_once __DIR__ . '/../includes/DeviceDetector.php';
$detector = new DeviceDetector();
$device   = $detector->getDeviceType();
$pageTitle = 'Cursos - EduPlataforma';

include __DIR__ . '/../includes/header.php';
?>
<section>
    <h1>Nossos Cursos</h1>
    <p>Lista de cursos disponíveis em breve.</p>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
