<div id="title-addArticle">
    <h1>Ajouter un article</h1>
</div>

<div id="form-addArticle">
    <form action="/index.php?c=article&a=add-article" method="post" enctype="multipart/form-data">
        <div>
            <label for="title">Titre de l'article</label>
            <input type="text" name="title" id="title">
        </div>
        <div>
            <label for="image">Chemin d'accès à l'image</label>
            <input type="file" name="image" id="image">
        </div>
        <div>
            <label for="summary">Résumé:</label>
            <input type="text" name="summary" id="summary" cols="30" row="10">
        </div>
        <div>
            <label for="editor"></label>
            <textarea name="content" id="editor" cols="30" rows="10"></textarea>
        </div>

        <input id="btn-addArticle" type="submit" name="save" value="Enregistrer" class="btn btn-secondary">
    </form>

</div>
