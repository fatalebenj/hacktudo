<?php
require_once __DIR__ . '/../includes/DeviceDetector.php';
$detector = new DeviceDetector();
$device   = $detector->getDeviceType();
$pageTitle = 'Contato - EduPlataforma';

include __DIR__ . '/../includes/header.php';
?>
<section>
    <h1>Fale Conosco</h1>
    <p>Formulário de contato em breve.</p>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
