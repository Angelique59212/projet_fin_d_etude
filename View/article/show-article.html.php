<?php

use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Manager\CommentManager;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $stmt = "SELECT * FROM mdf58_article WHERE id =" . $_GET['id'];

} else {
    header('Location: home');
}

/* @var Article $article */
$article = $data['article'];

// Getting connected user.
$user = null;
if($_SESSION['user']) {
    $user = $_SESSION['user'];
}?>

<div id="show-article">
    <div id="title-article">
        <h1><?= $article->getTitle() ?></h1>
        <div class="article-actions"> <?php
            // Admin buttons.
            if (AbstractController::verifyRole()) { ?>
                <a class="btn btn-warning" href="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>">Modifier</a>
                <a class="btn btn-danger" href="/index.php?c=article&a=delete-article&id=<?= $article->getId() ?>">Supprimer</a>   <?php
            } ?>

        </div>
    </div>

    <div class="article-summary">
        <div class="article-image">
            <img src="/uploads/<?= $article->getImage() ?>" alt="Image article">
        </div>
        <div class="article-summary-content">
            <?= $article->getSummary() ?>
        </div>
    </div>

    <div id="content">
        <?= html_entity_decode($article->getContent()) ?>
    </div>

</div>

<div id="comment">
    <span id="comments">Commentaires:</span><?php
    foreach (CommentManager::getCommentByArticle($article) as $item) {
        /* @var Comment $item */ ?>
        <div>
            <p id="author-comment"><?= $item->getAuthor()->getFirstname() ?></p>
            <p><?= $item->getContent() ?></p>
        </div><?php

        if (AbstractController::verifyRole() || ($user !== null && $article->getAuthor()->getId() === $user->getId())) { ?>
            <div id="remove-comment">
                <a class="btn btn-danger" href="/index.php?c=comment&a=delete-comment&id=<?= $item->getId() ?>">Supprimer</a>
            </div><?php
        } ?>
        <hr> <?php
    }
    ?>
    <a class="btn btn-primary" href="/index.php?c=comment&a=add-comment&id=<?= $article->getId() ?>">Ajouter un commentaire</a>
</div>
<?php


