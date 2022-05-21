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
        exit;
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
            $this->render('user/login');
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
            return $user->getRole()->getRoleName() === 'admin';
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
     * image management
     * @param string $field
     * @return false|string
     */
    public function getFormFieldImage(string $field)
    {
        // Return false if asked image does not exists.
        if(!$_FILES[$field]) {
            return false;
        }

        if ($_FILES[$field]['error']) {
            $_SESSION['error'] = "Erreur lors de l'upload de l'image";
            return false;
        }

        $authorizedMimeTypes = ['image/jpeg', 'image/jpg', 'image.png'];
        if (!in_array($_FILES[$field]['type'], $authorizedMimeTypes)) {
            $_SESSION['error'] = "Type de fichier non autorisé (uniquement images jpg, jpeg et png)";
            return false;
        }

        $oldName = $_FILES[$field]['name'];
        $newName = (new DateTime())->format('ymdhis') . '-' . uniqid();
        $newName .= substr($oldName, strripos($oldName, '.'));
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], 'uploads/' . $newName)) {
            $_SESSION['error'] = "Echec de l'enregistrement de l'image";
            return false;
        }

        return $newName;
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
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number    = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[^\w]/', $password);
        $same = $password === $password_repeat;
        $lenght = strlen($password) >= 7 && strlen($password) <= 150;

        return $uppercase && $lowercase && $number && $specialChars && $same && $lenght;
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


    /**
     * sanitize data for html database inserts.
     * @param $data
     * @return string
     */
    public function dataCleanHtmlContent($data): string
    {
        $allowedTags = [
            'p', 'div', 'small', 'ul', 'ol', 'li', 'table', 'th', 'td', 'tr', 'tbody', 'thead', 'span', 'strong', 'em',
            'pre', 'blockquote', 'i', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'a'
        ];

        $data = html_entity_decode($data, ENT_QUOTES, 'utf8');
        $data = strip_tags($data, $allowedTags);

        // Replace JavaScript events on attributes (onclick, onClick, onkeyup, ......).
        preg_replace('/(<.+?)(?<=\s)on[a-z]+\s*=\s*(?:([\'"])(?!\2).+?\2|(?:\S+?\(.*?\)(?=[\s>])))(.*?>)/i', "$1 $3", $data);
        return htmlentities($data);
    }
}

