<?php

$article = $data['article'];
?>

<h1 id="title-editArticle">Modifier l'article</h1>

<div id="form-editArticle">
    <form action="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>" method="post" id="editArticle">
        <div>
            <label for="title">Titre de l'article</label>
            <input type="text" name="title" id="title" value="<?= $article->getTitle() ?>" required>
        </div>
        <div>
            <label for="image">Chemin d'accès à l'image</label>
            <input type="file" name="image" id="image">
        </div>
        <div>
            <label for="editor">Résumé:</label>
            <textarea name="summary" id="editor" cols="30" rows="20"></textarea>
        </div>
        <div>
            <label for="editor">Contenu</label>
            <textarea name="content" id="editor" cols="30" rows="20"><?= $article->getContent() ?></textarea>
        </div>

        <input type="submit" name="save" value="Valider" class="save">
    </form>
</div>
