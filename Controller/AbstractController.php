<?php

 abstract class AbstractController
{
    abstract public function index();

     /**
      * @param string $template
      * @param array $data
      * @return void
      */
    public function render (string $template, array $data = []) {
        ob_start();
        require __DIR__ . "/../View/" . $template . ".html.php";
        $html = ob_get_clean();
        require __DIR__ . "/../View/base.html.php";
    }

     public function formIsset (...$inputNames): bool
     {
         foreach ($inputNames as $name) {
             if (!isset($_POST[$name])) {
                 return false;
             }
         }
         return true;
     }
}