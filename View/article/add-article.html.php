<h1>Ajouter un article</h1>
<div id="form-addArticle">
    <form action="/index.php?c=article&a=add-article" method="post">
        <div>
            <label for="title">Titre de l'article</label>
            <input type="text" name="title" id="title">
        </div>
        <div>
            <label for="content"></label>
            <textarea name="content" id="content" cols="30" rows="10"></textarea>
        </div>

        <input type="submit" name="save" value="Enregistrer" class="save btn btn-secondary">
    </form>
</div>
