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

     /**
      * @param string $value
      * @param int $min
      * @param int $max
      * @param string $redirect
      * @return void
      */
     public function checkRange (string $value, int $min, int $max, string $redirect): void
     {
         if (strlen($value) < $min || strlen($value) > $max) {
             header("Location: " . $redirect);
             exit();
         }
     }

     /**
      * @param string $password
      * @param string $password_repeat
      * @return bool
      */
     public function checkPassword(string $password, string $password_repeat):bool
     {
         if ($password !== $password_repeat) {
             return true;
         }
         else {
             return false;
         }
     }
}