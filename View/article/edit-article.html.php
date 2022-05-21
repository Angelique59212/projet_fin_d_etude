<?php

$article = $data['article'];
?>

<h1 id="title-editArticle">Modifier l'article</h1>

<div id="form-editArticle">
    <form action="/index.php?c=article&a=edit-article&id=<?= $article->getId() ?>" method="post" id="editArticle"  enctype="multipart/form-data">
        <div>
            <label for="title">Titre de l'article</label>
            <input type="text" name="title" id="title" value="<?= $article->getTitle() ?>" required>
        </div>
        <div>
            <label for="image">Image de couverture</label>
            <input type="file" name="image" id="image">
        </div>
        <div>
            <label for="editor-summary">Résumé:</label>
            <textarea name="summary" id="editor-summary" cols="30" rows="20"><?= $article->getSummary() ?></textarea>
        </div>
        <div>
            <label for="editor-content">Contenu</label>
            <textarea name="content" id="editor-content" cols="30" rows="20"><?= $article->getContent() ?></textarea>
        </div>

        <input type="submit" name="save" value="Valider" class="save">
    </form>
</div>
