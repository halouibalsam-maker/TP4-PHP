<?php

$pageTitle = 'Bonjour';
$currentPage = '';

require __DIR__ . '/includes/header.php';
?>
  <main>
    <h1>Bonjour !</h1>
    <p>PHP fonctionne : version <?= e(PHP_VERSION) ?>.</p>
    <p>Il est <?= e(date('H:i:s')) ?>.</p>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
