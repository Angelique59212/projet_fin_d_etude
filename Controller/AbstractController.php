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
            $this->render('home/index');
        }
    }

    /**
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
     * @param string $field
     * @param $default
     * @return mixed|string
     */
    public function getFormFieldImage(string $field, $default = null)
    {
        $tmpName = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        if (!isset($_FILES[$field]['name'])) {
            return (null === $default) ? '' : $default;
        }
        move_uploaded_file($_FILES[$field]['tmp_name'], 'uploads/' .$_FILES[$field]['name']);
        return basename($_FILES[$field]['name']);

    }


    /**
     * @return bool
     */
    public static function verifyUserConnect(): bool
    {
        return isset($_SESSION['user']) && null !== ($_SESSION['user'])->getId();
    }

    /**
     * @param string $value
     * @param int $min
     * @param int $max
     * @param string $redirect
     * @return void
     */
    public function checkRange(string $value, int $min, int $max, string $redirect): void
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
    public function checkPassword(string $password, string $password_repeat): bool
    {
        if ($password !== $password_repeat) {
            return true;
        } else {
            return false;
        }
    }

    /**
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