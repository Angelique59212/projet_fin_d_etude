<?php

use App\Model\Entity\Article;

$articles = $data['article'];


?>

<div class="container-home"><?php
    foreach ($articles as $article) {
        /* @var Article $article */?>
        <div class="container-dys">
            <div class="card">
                <img class="card-img-top w-30" src="uploads/<?= $article->getImage() ?>" alt="image enfant">

                <div class="card-body">
                    <h2 class="card-title fw-bold"><?= $article->getTitle()?></h2>
                    <p class="card-text">
                        <?= $article->getSummary() ?>
                    </p>
                    <a href="/index.php?c=article&a=show-article&id=<?= $article->getId()?>" class="btn btn-primary"> En savoir plus</a> <?php
                    // Admin buttons.
                    if (AbstractController::verifyRole()) { ?>
                        <a class="btn btn-warning" href="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>">Modifier</a>
                        <a class="btn btn-danger" href="/index.php?c=article&a=delete-article&id=<?= $article->getId() ?>">Supprimer</a>   <?php
                    } ?>
                </div>
            </div>
        </div><?php
    } ?>
</div>