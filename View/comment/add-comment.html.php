<?php
$article = $data['article'];
?>
<div id="title-comment">
    <h1>Ajouter un commentaire</h1>
</div>


<div id="form-addComment">
    <form action="/index.php?c=comment&a=add-comment&id=<?= $article->getId() ?>" method="post" id="addComment">
        <div class="style-comment">
            <label for="content"></label>
            <textarea name="content" id="content" required></textarea>
        </div>

        <div id="register">
            <input type="submit" name="save" value="Enregistrer">
        </div>
    </form>
</div>
