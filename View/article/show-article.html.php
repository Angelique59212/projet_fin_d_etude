<?php

use App\Model\Entity\Article;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $stmt = "SELECT * FROM mdf58_article WHERE id =" . $_GET['id'];

}
else {
    header('Location: home');
}

/* @var Article $article */
$article = $data['article']; ?>

<div id="show-article">
    <div id="title-article">
        <?= $article->getTitle() ?>
    </div>
    <div id="content">
        <?= $article->getContent() ?>
    </div>
</div>


