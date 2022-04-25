<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\User;
use UserManager;

class UserController extends AbstractController
{

    public function index()
    {
        $this->render('user/register');
    }

    public function register()
    {
        if (isset($_POST['submit'])) {
            if (!$this->formIsset
            ('email', 'firstname', 'lastname', 'password', 'password-repeat','age')) {
                header("Location: /?c=user&f=1");
            }

            $mail = trim(filter_var($_POST['mail'], FILTER_SANITIZE_STRING));
            $firstname = trim(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
            $lastname = trim(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
            $password = password_hash($_POST['password'], PASSWORD_ARGON2I);
            $age = trim(filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT));


            $user = (new User())
                ->setEmail($mail)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setPassword($password)
                ->setAge($age)
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
            if (!$this->formIsset('mail', 'password')) {
                header("Location: /?home&f=1");
            }

            $mail = filter_var($_POST['mail'], FILTER_SANITIZE_STRING);
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
        if (isset($_POST['mail'])) {
            $name = trim(strip_tags($_POST['name']));
            $message = trim(strip_tags($_POST['message']));
            $userMail = trim(strip_tags($_POST['mail']));

            $to = 'dehainaut.angelique@orange.fr';
            $subject = "Vous avez un message";
            $headers = array(
                'Reply-to' => $userMail,
                'X-Mailer' => 'PHP/' . phpversion()
            );
            if (filter_var($userMail, FILTER_VALIDATE_EMAIL)) {
                if (strlen($message) >=20 && strlen($message) <= 250) {
                    if (mail($to, $subject, $message, $headers, $userMail)) {
                        $_SESSION['mail'] = "mail-success";
                    } else {
                        $_SESSION['mail'] = "mail-error";
                    }
                    header('Location: /index.php?c=user&a=save-form');
                }
            }
        }
        else {
            $this->render('form/contact');
        }
    }

}