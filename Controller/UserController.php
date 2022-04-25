<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\User;
use App\Model\Manager\UserManager;

class UserController extends AbstractController
{

    public function index()
    {
        $this->render('user/register');
    }

    public function register()
    {
        self::redirectIfConnected();
        if (isset($_POST['submit'])) {
            if (!$this->formIsset
            ('email', 'firstname', 'lastname', 'password', 'password-repeat','age')) {
                header("Location: /?c=user&f=1");
            }

            $mail = $this->dataClean(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
            $firstname = $this->dataClean(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
            $lastname = $this->dataClean(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
            $password = password_hash($_POST['password'], PASSWORD_ARGON2I);
            $passwordRepeat = $_POST['password-repeat'];
            $age = trim(filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT));


            $user = (new User())
                ->setEmail($mail)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setPassword($password)
                ->setAge($age)
                ->setRole(1)
                ;

            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                header("Location: /?c=user&f=2");
            }

            if (UserManager::mailExists($mail)) {
                header("Location: /?c=home&f=3");
            }

            if (UserManager::addUser($user)) {
                self::login();
            }

        }
        else {
            $this->render('user/register');
        }

    }


    /**
     * login
     * @return void
     */
    public function login () {
        if (isset($_POST['submit'])) {
            if (!$this->formIsset('email', 'password')) {
                header("Location: /?home&f=1");
            }

            $mail = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
            $password = $_POST['password'];

            UserManager::login($mail, $password);
        }

        $this->render('user/login');

    }

    /**
     * @param int|null $id
     * @return void
     */
    public function showUser (int $id = null)
    {
        if (null === $id) {
            header('Location: /index.php?c=home');
        }

        if ($_SESSION['user']->getId() !== $id) {
            header("Location: /?c=home");
            exit();
        }

        $this->render('user/profile', [
            'profile'=>UserManager::getUserById($id)
        ]);
    }

    /**
     * @return void
     */
    public function disconnect():void
    {
        $_SESSION['user'] = null;
        session_unset();
        session_destroy();
        $this->render('home/home');
    }

    /**
     * @return void
     */
    public function saveForm() {
        if (isset($_POST['email'])) {
            $name = trim(strip_tags($_POST['name']));
            $message = trim(strip_tags($_POST['message']));
            $userMail = trim(strip_tags($_POST['email']));

            $to = 'dehainaut.angelique@orange.fr';
            $subject = "Vous avez un message";
            $headers = array(
                'Reply-to' => $userMail,
                'X-Mailer' => 'PHP/' . phpversion()
            );
            if (filter_var($userMail, FILTER_VALIDATE_EMAIL)) {
                if (strlen($message) >=20 && strlen($message) <= 250) {
                    if (mail($to, $subject, $message, $headers, $userMail)) {
                        $_SESSION['email'] = "mail-success";
                    } else {
                        $_SESSION['email'] = "mail-error";
                    }
                    header('Location: /index.php?c=user&a=save-form');
                }
            }
        }
        else {
            $this->render('form/contact');
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function editUser(int $id)
    {
        if (isset($_POST['submit'])) {
            $user = $_SESSION['user'];
            /* @var User $user */
            $id = $user->getId();
            $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);


            UserManager::editUser($id, $firstname, $lastname, $email);
            $this->render('user/profile', [
                'profile' => UserManager::getUserById($id)
            ]);
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id)
    {
        if (UserManager::userExists($id)) {
            $user = UserManager::getUserById($id);
            $deleted = UserManager::deleteUser($user);
        }
        self::disconnect();
        $this->index();

    }

}