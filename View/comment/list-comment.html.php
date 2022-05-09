<?php

use App\Model\Entity\Comment;

$comments = $data['comments'];

foreach ($comments as $comment) {
    /* @var Comment $comment */  ?>

    <p><?= $comment->getContent() ?></p>

    <a href="/index.php?c=comment&a=delete-comment&id=<?= $comment->getId() ?>">Supprimer</a><?php
}
