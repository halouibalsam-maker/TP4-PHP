<?php

require_once __DIR__ . '/includes/functions.php';

$rawId = $_GET['id'] ?? '';
$id = (is_string($rawId) || is_int($rawId))
    ? filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
    : false;
$event = $id === false ? null : findEvent($id);

if ($event === null) {
    http_response_code(404);
    $pageTitle = 'Événement introuvable';
    $currentPage = 'evenement.php';
    require __DIR__ . '/includes/header.php';
    ?>
  <main>
    <h1>Événement introuvable</h1>
    <p>L’événement demandé n’existe pas. Consultez la liste pour découvrir les événements disponibles.</p>
    <p><a class="button" href="evenements.php">Voir les événements</a></p>
  </main>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$formData = [
    'name' => '',
    'email' => '',
    'level' => '',
    'experience' => 'no',
    'motivation' => '',
];
$errors = [];
$registrationSucceeded = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $postedName = $_POST['name'] ?? '';
    $postedEmail = $_POST['email'] ?? '';
    $postedLevel = $_POST['level'] ?? '';
    $postedExperience = $_POST['experience'] ?? 'no';
    $postedMotivation = $_POST['motivation'] ?? '';

    $formData['name'] = is_string($postedName) ? trim($postedName) : '';
    $formData['email'] = is_string($postedEmail) ? trim($postedEmail) : '';
    $formData['level'] = is_string($postedLevel) ? trim($postedLevel) : '';
    $formData['experience'] = is_string($postedExperience) && in_array($postedExperience, ['yes', 'no'], true)
        ? $postedExperience
        : 'no';
    $formData['motivation'] = is_string($postedMotivation) ? trim($postedMotivation) : '';

    if (characterLength($formData['name']) < 3) {
        $errors['name'] = 'Le nom doit contenir au moins 3 caractères.';
    }

    if (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Saisissez une adresse e-mail valide.';
    }

    if (!in_array($formData['level'], ['1', '2', '3'], true)) {
        $errors['level'] = 'Choisissez un niveau d’études valide.';
    }

    if (($_POST['rules'] ?? '') !== '1') {
        $errors['rules'] = 'Vous devez accepter le règlement du club.';
    }

    if (characterLength($formData['motivation']) > 300) {
        $errors['motivation'] = 'La motivation ne peut pas dépasser 300 caractères.';
    }

    $registrationSucceeded = $errors === [];
}

$pageTitle = $event['title'];
$currentPage = 'evenement.php';

require __DIR__ . '/includes/header.php';
?>
  <main class="event-page">
    <article class="event-detail">
      <p class="club"><?= e($event['clubName']) ?></p>
      <h1><?= e($event['title']) ?></h1>
      <ul class="facts">
        <li><strong>Date :</strong> <time datetime="<?= e($event['date']) ?>"><?= e($event['dateDetail']) ?></time>, de <?= e($event['time']) ?><?= $event['endTime'] !== '' ? ' à ' . e($event['endTime']) : '' ?></li>
        <li><strong>Lieu :</strong> <?= e($event['location']) ?></li>
        <li><strong>Places :</strong> <?= e($event['places']) ?></li>
      </ul>

      <p><?= e($event['description']) ?></p>

      <h2>Au programme</h2>
      <ul>
        <?php foreach ($event['program'] as $item): ?>
          <li><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>

      <h2>À apporter</h2>
      <ul>
        <?php foreach ($event['supplies'] as $item): ?>
          <li><?= e($item) ?></li>
        <?php endforeach; ?>
      </ul>
    </article>

    <section class="registration" aria-labelledby="registration-title">
      <h2 id="registration-title">S'inscrire</h2>
      <?php if ($registrationSucceeded): ?>
        <p class="form-success" role="status">Merci <?= e($formData['name']) ?> ! Votre inscription à <?= e($event['title']) ?> est bien reçue.</p>
      <?php else: ?>
        <form action="evenement.php?id=<?= e($event['id']) ?>" method="post" id="registration-form">
          <p>
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name" value="<?= e($formData['name']) ?>" required minlength="3" autocomplete="name" aria-describedby="name-error"<?= isset($errors['name']) ? ' aria-invalid="true" class="is-invalid"' : '' ?>>
            <small class="field-error" id="name-error"><?= e($errors['name'] ?? '') ?></small>
          </p>
          <p>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($formData['email']) ?>" required autocomplete="email" placeholder="prenom.nom@campus.example" aria-describedby="email-error"<?= isset($errors['email']) ? ' aria-invalid="true" class="is-invalid"' : '' ?>>
            <small class="field-error" id="email-error"><?= e($errors['email'] ?? '') ?></small>
          </p>
          <p>
            <label for="level">Niveau d'études</label>
            <select id="level" name="level" required aria-describedby="level-error"<?= isset($errors['level']) ? ' aria-invalid="true" class="is-invalid"' : '' ?>>
              <option value="">Choisir...</option>
              <option value="1"<?= $formData['level'] === '1' ? ' selected' : '' ?>>1re année</option>
              <option value="2"<?= $formData['level'] === '2' ? ' selected' : '' ?>>2e année</option>
              <option value="3"<?= $formData['level'] === '3' ? ' selected' : '' ?>>3e année</option>
            </select>
            <small class="field-error" id="level-error"><?= e($errors['level'] ?? '') ?></small>
          </p>
          <fieldset>
            <legend>Avez-vous déjà utilisé un Arduino ?</legend>
            <label><input type="radio" name="experience" value="yes"<?= $formData['experience'] === 'yes' ? ' checked' : '' ?>> Oui</label>
            <label><input type="radio" name="experience" value="no"<?= $formData['experience'] === 'no' ? ' checked' : '' ?>> Non</label>
          </fieldset>
          <p>
            <label for="motivation">Motivation (facultatif)</label>
            <textarea id="motivation" name="motivation" rows="4" maxlength="300" aria-describedby="motivation-error"<?= isset($errors['motivation']) ? ' aria-invalid="true" class="is-invalid"' : '' ?>><?= e($formData['motivation']) ?></textarea>
            <small class="field-error" id="motivation-error"><?= e($errors['motivation'] ?? '') ?></small>
            <small class="counter" id="motivation-count"><?= characterLength($formData['motivation']) ?> / 300</small>
          </p>
          <p>
            <label><input type="checkbox" id="rules" name="rules" value="1" required aria-describedby="rules-error"<?= isset($_POST['rules']) && $_POST['rules'] === '1' ? ' checked' : '' ?><?= isset($errors['rules']) ? ' aria-invalid="true" class="is-invalid"' : '' ?>> J'accepte le règlement du club</label>
            <small class="field-error" id="rules-error"><?= e($errors['rules'] ?? '') ?></small>
          </p>
          <p>
            <button type="submit">S'inscrire</button>
          </p>
        </form>
      <?php endif; ?>
    </section>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
