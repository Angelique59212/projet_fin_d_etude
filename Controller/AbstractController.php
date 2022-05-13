<?php

use App\Model\Entity\User;
use App\Model\Entity\Role;

abstract class AbstractController
{
    abstract public function index();

    /**
     * @param string $template
     * @param array $data
     * @return void
     */
    public function render(string $template, array $data = [])
    {
        ob_start();
        require __DIR__ . "/../View/" . $template . ".html.php";
        $html = ob_get_clean();
        require __DIR__ . "/../View/base.html.php";
    }

    /**
     * @param ...$inputNames
     * @return bool
     */
    public function formIsset(...$inputNames): bool
    {
        foreach ($inputNames as $name) {
            if (!isset($_POST[$name])) {
                return false;
            }
        }
        return true;
    }

    /**
     * check if the form is submitted
     * @return bool
     */
    public function verifyFormSubmit(): bool
    {
        return isset($_POST['save']);
    }

    /**
     * @return void
     */
    public function redirectIfConnected(): void
    {
        if (self::verifyUserConnect()) {
            $this->render('home/home');
        }
    }

    /**
     * @return void
     */
    public function redirectIfNotConnected(): void
    {
        if (!self::verifyUserConnect()) {
            $this->render('home/home');
        }
    }

    /**
     * check role
     * @return bool
     */
    public static function verifyRole(): bool
    {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            /* @var User $user */
            foreach ($user->getRole() as $role) {
                /* @var  Role $role */
                $currentRole = $role->getRoleName();
                if ($currentRole === 'admin') {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *Return a form field value or default
     * @param string $field
     * @param $default
     * @return mixed|string
     */
    public function getFormField(string $field, $default = null)
    {
        if (!isset($_POST[$field])) {
            return (null === $default) ? '' : $default;
        }

        return $_POST[$field];
    }

    /**
     * image management for articles
     * @param string $field
     * @param $default
     * @return mixed|string
     */
    public function getFormFieldImage(string $field)
    {
        if ($_FILES[$field]['error']) {
            $_SESSION['error'] = "Erreur lors de l'upload de l'image";
            return false;
        }

        $splitFileName =  explode('.', $_FILES[$field]['name']);
        $name = $splitFileName[0];
        $fileExtension = strtolower(end( $splitFileName));
        $authorizedType= ['jpg','jpeg', 'png'];

        if (!in_array($fileExtension, $authorizedType)) {
            $_SESSION['error'] = "Type de fichier non autorisé (uniquement images jpg, jpeg et png)";
            return false;
        }

        $unwanted_array = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y',
            ' ' => "-", ':' => "-", '&' => '-et-', '+' => 'et',
        ];

        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $newImageName = strtr($name, $unwanted_array);

        $newImageName = $newImageName . date('Y-m-d-H-i-s') . "." . $fileExtension;

        if (!move_uploaded_file($_FILES[$field]['tmp_name'], 'uploads/' .$newImageName)) {
            $_SESSION['error'] = "echec de l'enregistrement de l'image";
            return false;
        }

        return $newImageName;
    }

    /**
     * @return bool
     */
    public static function verifyUserConnect(): bool
    {
        return isset($_SESSION['user']) && null !== ($_SESSION['user'])->getId();
    }

    /**
     * @param string $password
     * @param string $password_repeat
     * @return bool
     */
    public function checkPassword(string $password, string $password_repeat): bool
    {
        if ($password === $password_repeat) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * sanitize data
     * @param $data
     * @return string
     */
    public function dataClean($data): string
    {
        $data = trim(strip_tags($data));
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }
}