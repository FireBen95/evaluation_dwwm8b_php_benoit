<?php
session_start();



if (!isset($_GET['livre_id']) || empty($_GET['livre_id'])) {

    return header("Location: index.php");
}

$livre_id = strip_tags($_GET['livre_id']);


$livre_id_converted = (int) $livre_id;


require __DIR__ . "/db/connexion.php";



$req = $db->prepare("SELECT * FROM livre WHERE id = :id");
$req->bindValue(":id", $livre_id_converted);
$req->execute();
$count = $req->rowCount();




if ($count != 1) {
    return header("Location: index.php");
}


$livre = $req->fetch();


$req->closeCursor();


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post_clean = [];
    $edit_form_errors = [];

    foreach ($_POST as $key => $value) {
        $post_clean[$key] = strip_tags(trim($value));
    }

    // nom livre
    if (isset($post_clean['name'])) {
        if (empty($post_clean['name'])) {
            $edit_form_errors['name'] = "Le nom du livre est obligatoire.";
        } else if (mb_strlen($post_clean['name']) > 255) {
            $edit_form_errors['name'] = "Le nom du livre doit contenir au maximum 255 caracatères.";
        }
    }

    // genre livre
    if (isset($post_clean['category'])) {
        if (empty($post_clean['category'])) {
            $edit_form_errors['category'] = "Le genre est obligatoire.";
        } else if (mb_strlen($post_clean['category']) > 255) {
            $edit_form_errors['category'] = "Le genre doit contenir au maximum 255 caractères.";
        }
    }


    // auteur
    if (isset($post_clean['author'])) {
        if (empty($post_clean['author'])) {
            $edit_form_errors['author'] = "L'auteur est obligatoire.";
        } else if (mb_strlen($post_clean['author']) > 255) {
            $edit_form_errors['author'] = "L'auteur doit contenir au maximum 255 caractères.";
        }
    }


    // note
    if (isset($post_clean['review'])) {
        if (is_string($post_clean['review']) && ($post_clean['review'] == '')) {
            $edit_form_errors['review'] = "La note est obligatoire.";
        } else if (empty($post_clean['review']) && ($post_clean['review'] != 0)) {
            $edit_form_errors['review'] = "La note est obligatoire.";
        } else if (!is_numeric($post_clean['review'])) {
            $edit_form_errors['review'] = "La note doit être un nombre.";
        } else if (($post_clean['review'] < 0) || ($post_clean['review'] > 10)) {
            $edit_form_errors['review'] = "La note doit être comprise entre 0 et 10.";
        }
    }




    if (count($edit_form_errors) > 0) {
        $_SESSION['edit_form_errors'] = $edit_form_errors;

        $_SESSION['old'] = $post_clean;

        return header("Location: " . $_SERVER['HTTP_REFERER']);
    }


    $final_post_clean = [];
    foreach ($post_clean as $key => $value) {
        $final_post_clean[$key] = htmlspecialchars($value);
    }

    $livre_name   = $final_post_clean["name"];
    $livre_category = $final_post_clean["category"];
    $livre_author = $final_post_clean["author"];
    $livre_review = $final_post_clean["review"];



    $livre_review_rounded = round($livre_review, 1);



    require __DIR__ . "/db/connexion.php";


    // Effectuer la requête de modification des données dans la table "livre" de la base données
    $req = $db->prepare("UPDATE livre SET name=:name, category=:category, author=:author, review=:review, updated_at=now() WHERE id=:id");

    $req->bindValue(":name",          $livre_name);
    $req->bindValue(":category",      $livre_category);
    $req->bindValue(":author",        $livre_author);
    $req->bindValue(":review",        $livre_review_rounded);
    $req->bindValue(":id",            $livre['id']);

    $req->execute();
    $req->closeCursor();

    

    // Rediriger l'utilisateur vers la page d'accueil
    // Arrêter l'execution du script.
    return header("Location: index.php");
}


?>

<?php include __DIR__ . "/partials/head.php"; ?>

<main>
    <h1>Modifier ce livre</h1>

    <?php if (isset($_SESSION['edit_form_errors']) && !empty($_SESSION['edit_form_errors'])) : ?>
        <div>
            <ul>
                <?php foreach ($_SESSION['edit_form_errors'] as $error) : ?>
                    <li>- <?= $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
        <?php unset($_SESSION['edit_form_errors']); ?>
    <?php endif ?>

    <div>
        <form method="POST">
            <div>
                <label for="name-livre" class="fw-semibold">Nom du livre</label>
                <input type="text" name="name" id="name_livre" class="form-control form-control-lg border-warning">
            </div>
            <div>
                <label for="category-livre" class="fw-semibold">Le genre</label>
                <input type="text" name="category" id="category_livre" class="form-control form-control-lg border-warning">
            </div>
            <div>
                <label for="author-livre" class="fw-semibold">L'auteur</label>
                <input type="text" name="author" id="author_livre" class="form-control form-control-lg border-warning">
            </div>
            <div>
                <label for="review-livre" class="fw-semibold">La note sur 10</label>
                <input type="text" name="review" id="review_livre" class="form-control form-control-lg border-warning">
            </div>
            <div class="d-grid gap-2">
                <input type="submit" type="button" class="btn btn-warning fw-semibold">
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . "/partials/foot.php"; ?> 