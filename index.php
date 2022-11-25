<?php
session_start();

require __DIR__ . "/db/connexion.php";

$req = $db->prepare("SELECT * FROM livre ORDER BY created_at DESC");
$req->execute();
$livres = $req->fetchAll();
?>




<?php include __DIR__ . "/partials/head.php"; ?>

<main>
    <h1>Liste des Livres</h1>

    <?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])) : ?>
        <div>
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif ?>

    <div class="d-grid gap-2">
        <a href="create.php" class="btn btn-dark fw-bold" type="button">Ajouter un livre</a>
    </div>

    <div>
        <?php if (isset($livres) && !empty($livres)) : ?>
            <?php foreach ($livres as $livre) : ?>
                <div class="container border border-dark border-5 rounded-5">
                    <h2>Livre numéro : <?= htmlspecialchars($livre['id']) ?></h2>
                    <h2>Nom : <?= htmlspecialchars($livre['name']) ?></h2>
                    <p class="fw-semibold">Genres : <?= htmlspecialchars($livre['category']) ?></p>
                    <p class="fw-semibold">Auteur : <?= htmlspecialchars($livre['author']) ?></p>
                    <p class="fw-semibold">Note : <?= htmlspecialchars($livre['review']) ?></p>
                    <a href="edit.php?livre_id=<?= htmlspecialchars($livre['id']) ?>" type="button" class="btn btn-warning fw-semibold">Modifier</a>
                    <a href="delete.php?livre_id=<?= htmlspecialchars($livre['id']) ?>" onclick="return confirm('Confirmer la suppression ?')" type="button" class="btn btn-danger fw-semibold">Supprimer</a>
                </div>
            <?php endforeach ?>
        <?php else : ?>
            <p class="livre-none">Aucun livre ajouté à la liste</p>
        <?php endif ?>
    </div>
</main>

<?php include __DIR__ . "/partials/foot.php"; ?>