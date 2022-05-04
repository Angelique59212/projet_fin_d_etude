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

<?= $article->getTitle() ?>
<?= $article->getContent() ?>

