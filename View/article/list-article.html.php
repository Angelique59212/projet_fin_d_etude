<?php

use App\Model\Entity\Article;

$articles = $data['article'];


?>
<div id="container-article"><?php
    if (isset($data['article'])) { ?>
        <?php
        foreach ($articles as $article) {
            /* @var Article $article */ ?>
                <div id="article-show">
                    <p id="title"><?= $article->getTitle() ?></p>
                    <p id="author"><?= $article->getAuthor()->getFirstname() ?></p>
                    <a href="/index.php?c=article&a=show-article&id=<?= $article->getId() ?>">Voir plus</a>

            <?php

            if (AbstractController::verifyRole()) { ?>
                <a href="/index.php?c=article&a=delete-article&id=<?= $article->getId() ?>">Supprimer</a>
                <a href="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>">Modifier</a>
                <?php
            }?>
                </div><?php
        }
    }?>
</div>

