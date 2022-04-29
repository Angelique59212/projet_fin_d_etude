<p id="def-dys">
    Les troubles dys sont des troubles cognitifs qui entraînent des difficultés d'apprentissage.
    Ce sont des troubles persistant et durable, ils sont relatif au langage, à l'écriture, aux calculs, aux gestes et à
    l'attention.
    A travers ce site vous en apprendrez plus sur les différents troubles.<br>
    <span id="namely">A savoir:</span> Il n'est pas évident que les enfants est forcément tout les symptômes d'un trouble.
    Les troubles dys sont des troubles durables.
</p>

<?php

use App\Model\Entity\Article;

if (isset($data['articles']))
{
    $articles = $data['articles'];
    ?>
<div class="container"><?php
    foreach ($articles as $article) {
    /* @var Article $article */?>
    <div class="container-dys">
        <!-- Dyslexie -->
        <div class="card">
            <img class="card-img-top w-30" src="/assets/img/dyslexie/dyslexie.jpg" alt="image enfant dyslexique">
            <div class="card-body">
                <h2 class="card-title fw-bold"><?= $article->getTitle()?></h2>
                <p class="card-text">
                    <?= $article->getSummary() ?>
                </p>
                <a id="learn" href="/index.php?c=article&a=page&action=dyslexie" class="btn btn-primary"> En savoir plus</a>
            </div>
        </div>
    </div><?php
    }
}
?>
</div>
<!--<div id="image-trouble">-->
<!--    <img src="/assets/img/les_troubles_dys.jpg" alt="les troubles dys">-->
<!--</div>-->
