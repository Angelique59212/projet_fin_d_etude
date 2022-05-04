<?php

use App\Model\Entity\Article;

$articles = $data['article'];


?>
<div id="container-article"><?php
    if (isset($data['article'])) { ?>
    <div>
        <div id="article-show"><?php
        foreach ($articles as $article) {
            /* @var Article $article */ ?>
            <p id="title"><?= $article->getTitle() ?></p>
            <p><?= $article->getContent() ?></p>
            <p id="author"><?= $article->getAuthor()->getFirstname() ?></p><?php

            if (AbstractController::verifyRole()) { ?>
                <a href="/index.php?c=article&a=show-article&id=<?= $article->getId() ?>">Voir plus</a>
                <a href="/index.php?c=article&a=delete-article&id=<?= $article->getId() ?>">Supprimer</a>
                <a href="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>">Modifier</a>
                }
        </div>
    </div><?php
            }
        }
}?>
</div>
