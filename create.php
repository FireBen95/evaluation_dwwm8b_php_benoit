<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post_clean = [];
    $create_form_errors = [];

    // xss protection
    foreach ($_POST as $key => $value) {
        $post_clean[$key] = strip_tags(trim($value));
    }

    // les contraintes

    // nom livre
    if (isset($post_clean['name'])) {
        if (empty($post_clean['name'])) {
            $create_form_errors['name'] = "Le nom du livre est obligatoire.";
        } else if (mb_strlen($post_clean['name']) > 255) {
            $create_form_errors['name'] = "Le nom du livre doit contenir au maximum 255 caracatères.";
        }
    }

    // genres livre
    if (isset($post_clean['category'])) {
        if (empty($post_clean['category'])) {
            $create_form_errors['category'] = "Le genre est obligatoire.";
        } elseif (mb_strlen($post_clean['category']) > 255) {
            $create_form_errors['actors'] = "Le genres doit contenir au maximum 255 caractères.";
        }
    }


    // auteur livre
    if (isset($post_clean['author'])) {
        if (empty($post_clean['author'])) { //required
            $create_form_errors['author'] = "Le nom du ou des auteurs est obligatoire.";
        } elseif (mb_strlen($post_clean['author']) > 255) { // max:255
            $create_form_errors['author'] = "Le nom du ou des auteurs doit contenir au maximum 255 caractères.";
        }
    }


    // note livre
    if (isset($post_clean['review'])) {
        if (is_string($post_clean['review']) && ($post_clean['review'] == '')) {
            $create_form_errors['review'] = "La note est obligatoire.";
        } else if (empty($post_clean['review']) && ($post_clean['review'] != 0)) {
            $create_form_errors['review'] = "La note est obligatoire!!!!!!!!!!!!!.";
        } else if (!is_numeric($post_clean['review'])) {
            $create_form_errors['review'] = "La note doit être un nombre.";
        } else if (($post_clean['review'] < 0) || ($post_clean['review'] > 10)) {
            $create_form_errors['review'] = "La note doit être comprise entre 0 et 10.";
        }
    }



    if (count($create_form_errors) > 0) {
        // Stocker les messages d'erreurs en session
        $_SESSION['create_form_errors'] = $create_form_errors;

        // Stocker les données provenant du formulaire en session
        $_SESSION['old'] = $post_clean;

        // Rediriger l'utilisateur vers la page de laquelle proviennent les données
        // J'arrête l'exécution du script
        return header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    // Protéger XSS une seconde fois
    $final_post_clean = [];
    foreach ($post_clean as $key => $value) {
        $final_post_clean[$key] = htmlspecialchars($value);
    }


    $livre_name = $final_post_clean["name"];
    $livre_category = $final_post_clean["category"];
    $livre_author = $final_post_clean["author"];
    $livre_review = $final_post_clean["review"];

    // arrondir la note
    $livre_review_rounded = round($livre_review, 1);

    // connexion avec la base
    require __DIR__ . "/db/connexion.php";

    // Effectuer la requête d'insertion des données dans la table "film" de la base données
    $req = $db->prepare("INSERT INTO livre (name, category, author, review, created_at, updated_at) VALUES (:name, :category, :author, :review, now(), now() ) ");

    $req->bindValue(":name", $livre_name);
    $req->bindValue(":category", $livre_category);
    $req->bindValue(":author", $livre_author);
    $req->bindValue(":review", $livre_review_rounded);

    $req->execute();
    $req->closeCursor();


    // Arrêter l'execution du script.
    return header("Location: index.php");
}
?>




<?php include __DIR__ . "/partials/head.php"; ?>

<main>
    <h1>Ajouter un nouveau livre</h1>

    <?php if (isset($_SESSION['create_form_errors']) && !empty($_SESSION['create_form_errors'])) : ?>
        <div>
            <ul>
                <?php foreach ($_SESSION['create_form_errors'] as $error) : ?>
                    <li>- <?= $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
        <?php unset($_SESSION['create_form_errors']); ?>
    <?php endif ?>

    <div>
        <form method="POST">
            <div>
                <label for="name-livre" class="fw-semibold">Nom du livre</label>
                <input type="text" name="name" id="name_livre" class="form-control form-control-lg border-primary">
            </div>
            <div>
                <label for="category-livre" class="fw-semibold">Le genre</label>
                <input type="text" name="category" id="category_livre" class="form-control form-control-lg border-primary">
            </div>
            <div>
                <label for="author-livre" class="fw-semibold">L'auteur</label>
                <input type="text" name="author" id="author_livre" class="form-control form-control-lg border-primary">
            </div>
            <div>
                <label for="review-livre" class="fw-semibold">La note sur 10</label>
                <input type="text" name="review" id="review_livre" class="form-control form-control-lg border-primary">
            </div>
            <div class="d-grid gap-2">
                <input type="submit" type="button" class="btn btn-primary">
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . "/partials/foot.php"; ?>