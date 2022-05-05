<?php
$article = $data['article'];
?>
<h1>Ajouter un commentaire</h1>
<div id="form-addComment">
    <form action="/index.php?c=comment&a=add-comment&id=<?= $article->getId() ?>" method="post" id="addComment">
        <div>
            <label for="content"></label>
            <textarea name="content" id="content" cols="30" rows="20" required></textarea>
        </div>

        <input type="submit" name="save" value="Enregistrer">
    </form>
</div>
