<?php

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $stmt = "SELECT * FROM mdf58_article WHERE id =" . $_GET['id'];

}
else {
    header('Location: home');
}?>

