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

    /**
     * @return void
     */
    public function register()
    {
        self::redirectIfConnected();

        /**
         * verification of information
         */
        if (!isset($_POST['submit'])) {
            header("Location: /?c=user");
            die();
        }

        if (!$this->formIsset('email', 'firstname', 'lastname', 'password', 'password-repeat')) {
            $_SESSION['error'] = "Un champ est manquant";
            header("Location: /?c=user");
            die();
        }

        $mail = $this->dataClean(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
        $firstname = $this->dataClean(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING));
        $lastname = $this->dataClean(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING));
        $password = password_hash($_POST['password'], PASSWORD_ARGON2I);

        if (!$this->checkPassword($_POST['password'], $_POST['password-repeat'])) {
            $_SESSION['error'] = "Les password ne correspondent pas";
            header("Location: /?c=user");
            die();
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "l'email n'est pas valide";
            header("Location: /?c=user");
            die();
        }

        if (UserManager::mailExists($mail)) {
            $_SESSION['error'] = "l'email existe déjà";
            header("Location: /?c=user");
            die();
        }

        /**
         * generate a random key for sending the validation email
         */
        $validationKey = self::generateRandomString();

        /**
         * registration of the user and the validation key in the database
         */
        $user = (new User())
            ->setEmail($mail)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setPassword($password)
            ->setValidationKey($validationKey);

        if (!UserManager::addUser($user)) {
            $_SESSION['error'] = "Enregistrement impossible, re-essayez plus tard";
            header("Location: /?c=user&a=register");
            die();
        }

        /**
         * retrieval of user id and validation key
         */
        $userID = UserManager::getUserByMail($mail)->getId();

        /**
         * send mail
         */
        $to = $mail;
        $subject = 'validation email';
        $headers = array(
            'From' => 'dehainaut.angelique@orange.fr',
            'Reply-To' => 'dehainaut.angelique@orange.fr',
            'X-Mailer' => 'PHP/' . phpversion(),
            'Mime-Version' => '1.0',
            'Content-Type' => 'text/html; charset=utf-8'
        );
        $message = "
            <a href=\"http://troubles-dys.angeliquedehai.fr/?c=user&a=email-validation&key=" . $validationKey . "&id=" . $userID . "\"> Valider mon adresse e-mail</a>
        ";


        //TODO :: Décommenter a la mise en production
        //if(!mail($to, $subject, $message, $headers)) {
        //    $_SESSION['error'] = "Echec de l'envoi du mail.";
        //    header("Location: /?c=home");
        //    die();
        //}

        $_SESSION['error'] = "Un mail de validation vous a été envoyé (Pensez à vérifier vos spams)";
        header("Location: /?c=user&a=login");
    }

    /**
     * @param string $key
     * @param string $id
     * @return void
     */
    public function emailValidation(string $key, string $id)
    {
        $id = intval($id);

        if (!$user = UserManager::getUserById($id)) {
            $_SESSION['error'] = "L'utilisateur n'existe pas.";
            header("Location: /?c=home");
            die();
        }

        $validationKeyFromDB = $user->getValidationKey();

        if ($key !== $validationKeyFromDB) {
            $_SESSION['error'] = "Clé invalide.";
            header("Location: /?c=home");
            die();
        }

        UserManager::validUser($id);

        $_SESSION['error'] = "Felicitations, vous avez bien validé votre adresse e-mail.";
        header("Location: /?c=user&a=login");
        die();
    }


    /**
     * login
     * @return void
     */
    public function login()
    {
        if (isset($_POST['submit'])) {
            if (!$this->formIsset('email', 'password')) {
                $_SESSION['error'] = "Un champ est manquant";
                header("Location: /?user&a=login");
                die();
            }

            $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            if (!UserManager::login($mail, $password)) {
                header('Location: /?user&a=login');
                die();
            }

            header('Location: /?home');
            die();
        }

        $this->render('user/login');

    }

    /**
     * @param int|null $id
     * @return void
     */
    public function showUser(int $id = null)
    {
        if (null === $id) {
            header('Location: /index.php?c=home');
        }

        if ($_SESSION['user']->getId() !== $id) {
            header("Location: /?c=home");
            exit();
        }

        $this->render('user/profile', [
            'profile' => UserManager::getUserById($id)
        ]);
    }

    /**
     * @return void
     */
    public function disconnect(): void
    {
        $_SESSION['user'] = null;
        session_unset();
        session_destroy();
        $this->render('home/home');
    }

    /**
     * @return void
     */
    public function saveForm()
    {
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
                if (strlen($message) >= 20 && strlen($message) <= 250) {
                    if (mail($to, $subject, $message, $headers, $userMail)) {
                        $_SESSION['mail'] = "mail-success";
                    } else {
                        $_SESSION['mail'] = "mail-error";
                    }
                    $this->render('form/contact');
                }
            }
        } else {
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
            $_SESSION['error'] = 'Votre profil a bien été modifié';
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

    /**
     * method used to create a random string
     * @param int $length
     * @return false|string
     */
    private static function generateRandomString(int $length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

}