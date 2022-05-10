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
$article = $data['article']; ?>

<div id="show-article">
    <div id="title-article">
        <?= $article->getTitle() ?>
    </div>
    <div id="content">

        <?= html_entity_decode($article->getContent()) ?>
    </div>
    <div id="comment">
        <span id="comments">Commentaires:</span><?php
        foreach (CommentManager::getCommentByArticle($article) as $item) {
            /* @var Comment $item */ ?>
             <div>
                 <p id="author-comment"><?= $item->getAuthor()->getFirstname() ?></p>
                 <p><?= $item->getContent() ?></p>
             </div><?php

            if (AbstractController::verifyRole()) { ?>
               <div id="remove-comment">
                   <a href="/index.php?c=comment&a=delete-comment&id=<?= $item->getId() ?>">Supprimer le
                       commentaire</a>
               </div><?php
            }
        }
        ?>
    </div>
<a href="/index.php?c=comment&a=add-comment&id=<?= $article->getId() ?>">Ajouter un commentaire</a><?php


