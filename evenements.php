<?php

require_once __DIR__ . '/includes/functions.php';

$events = getEvents();
$clubs = [];
foreach ($events as $event) {
    $clubs[$event['club']] = $event['clubName'];
}

$query = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$selectedClub = isset($_GET['club']) && is_string($_GET['club']) ? $_GET['club'] : '';
if (!isset($clubs[$selectedClub])) {
    $selectedClub = '';
}

$sortBy = isset($_GET['sort']) && is_string($_GET['sort']) ? $_GET['sort'] : 'date';
if (!in_array($sortBy, ['date', 'places'], true)) {
    $sortBy = 'date';
}

$normalizedQuery = normalizeText($query);
$filteredEvents = [];
foreach ($events as $event) {
    $searchableText = normalizeText(
        $event['clubName'] . ' ' . $event['title'] . ' ' . $event['dateLabel'] . ' ' . $event['location']
    );
    if (
        ($normalizedQuery === '' || strpos($searchableText, $normalizedQuery) !== false)
        && ($selectedClub === '' || $event['club'] === $selectedClub)
    ) {
        $filteredEvents[] = $event;
    }
}

$visibleEventIds = [];
foreach ($filteredEvents as $event) {
    $visibleEventIds[$event['id']] = true;
}

$orderedEvents = $events;
usort($orderedEvents, static function (array $first, array $second) use ($sortBy): int {
    if ($sortBy === 'places') {
        return $second['places'] <=> $first['places'];
    }

    return strcmp($first['date'], $second['date']);
});

$pageTitle = 'Événements';
$currentPage = 'evenements.php';

require __DIR__ . '/includes/header.php';
?>
  <main>
    <h1>Tous les événements</h1>
    <p><span id="upcoming-count"><?= count($filteredEvents) ?></span> événement<?= count($filteredEvents) === 1 ? '' : 's' ?> à venir ce mois-ci.</p>

    <form class="filters" role="search" aria-label="Filtrer les événements" action="evenements.php" method="get">
      <p>
        <label for="search">Rechercher</label>
        <input type="search" id="search" name="q" value="<?= e($query) ?>" placeholder="Arduino, photo, IA...">
      </p>
      <p>
        <label for="club-filter">Club</label>
        <select id="club-filter" name="club">
          <option value="">Tous les clubs</option>
          <?php foreach ($clubs as $clubId => $clubName): ?>
            <option value="<?= e($clubId) ?>"<?= $selectedClub === $clubId ? ' selected' : '' ?>><?= e($clubName) ?></option>
          <?php endforeach; ?>
        </select>
      </p>
      <p>
        <label for="sort">Trier par</label>
        <select id="sort" name="sort">
          <option value="date"<?= $sortBy === 'date' ? ' selected' : '' ?>>Date</option>
          <option value="places"<?= $sortBy === 'places' ? ' selected' : '' ?>>Places (plus grand d'abord)</option>
        </select>
      </p>
      <p>
        <label class="favorite-filter" for="favorites-only">
          <input type="checkbox" id="favorites-only" name="favorites-only" value="1">
          Seulement mes favoris
        </label>
      </p>
    </form>

    <p class="results-count" aria-live="polite"><?= count($filteredEvents) ?> événement<?= count($filteredEvents) === 1 ? '' : 's' ?> affiché<?= count($filteredEvents) === 1 ? '' : 's' ?>.</p>
    <p class="empty-message" aria-live="polite"<?= $filteredEvents === [] ? '' : ' hidden' ?>>Aucun événement ne correspond à votre recherche.</p>

    <div class="event-list" id="event-list">
      <?php foreach ($orderedEvents as $event): ?>
        <article class="event-card" data-club="<?= e($event['club']) ?>" data-date="<?= e($event['date']) ?>" data-places="<?= e($event['places']) ?>"<?= isset($visibleEventIds[$event['id']]) ? '' : ' hidden' ?>>
          <p class="club"><?= e($event['clubName']) ?></p>
          <h2><?= e($event['title']) ?></h2>
          <p class="meta"><time datetime="<?= e($event['date']) ?>"><?= e($event['dateLabel']) ?></time>, <?= e($event['time']) ?></p>
          <p class="meta"><?= e($event['location']) ?></p>
          <p class="places"><strong><?= e($event['places']) ?></strong> places</p>
          <button type="button" class="favorite-toggle" data-favorite-id="<?= e($event['id']) ?>" data-event-title="<?= e($event['title']) ?>" aria-pressed="false">☆</button>
          <a href="evenement.php?id=<?= e($event['id']) ?>">Voir le détail</a>
        </article>
      <?php endforeach; ?>
    </div>

    <section>
      <h2>Récapitulatif</h2>
      <table>
        <thead>
          <tr><th scope="col">Événement</th><th scope="col">Date</th><th scope="col">Places</th></tr>
        </thead>
        <tbody>
          <?php foreach (getEvents() as $event): ?>
            <tr>
              <td><?= e($event['title']) ?></td>
              <td><time datetime="<?= e($event['date']) ?>"><?= e($event['dateLabel']) ?></time></td>
              <td><?= e($event['places']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
