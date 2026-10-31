<?php

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Accueil';
$currentPage = 'index.php';
$events = array_slice(getEvents(), 0, 2);

require __DIR__ . '/includes/header.php';
?>
  <main>
    <section class="hero">
      <h1>Tous les événements des clubs, au même endroit</h1>
      <p>Ateliers, conférences, sorties, tournois : découvrez ce que font les clubs étudiants et réservez votre place en quelques secondes.</p>
      <a class="button" href="evenements.php">Voir les événements</a>
    </section>

    <section>
      <h2>Prochains événements</h2>
      <div class="event-list">
        <?php foreach ($events as $event): ?>
          <article class="event-card">
            <p class="club"><?= e($event['clubName']) ?></p>
            <h3><?= e($event['title']) ?></h3>
            <p class="meta"><time datetime="<?= e($event['date']) ?>"><?= e($event['dateLabel']) ?></time>, <?= e($event['time']) ?> · <?= e($event['location']) ?></p>
            <a href="evenement.php?id=<?= e($event['id']) ?>">Voir le détail</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
