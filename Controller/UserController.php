<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Model\Manager\RoleManager;
use App\Model\Manager\UserManager;

class UserController extends AbstractController
{

    /**
     * Default method if no action provided in the URL.
     * @return void
     */
    public function index()
    {
        $this->render('user/register');
    }

    /**
     * Manage user registration.
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
            $_SESSION['error'] = "Les password ne correspondent pas, ou il ne respecte pas les critères de sécurité (minuscule, majuscule, nombre, caractère spécial)";
            header("Location: /?c=user");
            die();
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "L'email n'est pas valide";
            header("Location: /?c=user");
            die();
        }

        if (UserManager::mailExists($mail)) {
            $_SESSION['error'] = "L'email existe déjà";
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
            ->setValidationKey($validationKey)
            ->setRole(RoleManager::getDefaultRole())
        ;

        if (!UserManager::addUser($user)) {
            $_SESSION['error'] = "Enregistrement impossible, réessayez plus tard";
            header("Location: /?c=user&a=register");
            die();
        }

        /**
         * send user validation mail.
         */
        $subject = 'validation email';
        $headers = array(
            'From' => 'dehainaut.angelique@orange.fr',
            'Reply-To' => 'dehainaut.angelique@orange.fr',
            'X-Mailer' => 'PHP/' . phpversion(),
            'Mime-Version' => '1.0',
            'Content-Type' => 'text/html; charset=utf-8'
        );
        $message = "
            <a href=\"http://troubles-dys.angeliquedehai.fr/?c=user&a=email-validation&key=" . $validationKey . "&id=" . $user->getId() . "\"> 
            Valider mon adresse e-mail, afin de valider mon inscription sur les troubles-dys</a>
        ";

        if(!mail($mail, $subject, $message, $headers)) {
            $_SESSION['error'] = "Echec de l'envoi du mail.";
            header("Location: /?c=home");
            die();
        }

        $_SESSION['success'] = "Un mail de validation vous a été envoyé (Pensez à vérifier vos spams)";
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
        $user = UserManager::getUserById($id);

        if (!$user) {
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

        $_SESSION['success'] = "Felicitations, vous avez bien validé votre adresse e-mail.";
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
                header("Location: /?c=user&a=login");
                die();
            }

            $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $user = UserManager::getUserByMail($mail);

            if (password_verify($password, $user['password'])) {
                $userSession = (new User())
                    ->setId($user['id'])
                    ->setEmail($user['email'])
                    ->setFirstname($user['firstname'])
                    ->setLastname($user['lastname'])
                    ->setValid($user['valid']);

                $userSession->setRole(RoleManager::getRoleByUser($userSession));

                // Account not validated.
                if (!$userSession->isValid()) {
                    $_SESSION['error'] = "Votre mail n'a pas été validé";
                }
                // Account validated, storing user in session.
                else {
                    $_SESSION['user'] = $userSession;
                }
            }
            else {
                $_SESSION['error'] = 'Mot de passe incorrect';
            }
            header('Location: /?c=home');
            die();
        }

        $this->render('user/login');
    }


    /**
     * Show user profile.
     * @return void
     */
    public function showUser()
    {
        $this->redirectIfNotConnected();

        $this->render('user/profile', [
            'profile' => $_SESSION['user']
        ]);
    }


    /**
     * Manage user logout.
     * @return void
     */
    public function disconnect(): void
    {
        // Keeping messages if any
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;

        $_SESSION['user'] = null;
        session_unset();
        session_destroy();

        // Restart session to be able to use messages in session.
        session_start();

        // Setting again existing messages into the session.
        if($error) {
            $_SESSION['error'] = $error;
        }

        if($success) {
            $_SESSION['success'] = $success;
        }
        $this->render('home/home');
    }


    /**
     * Manage contact form
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
     * Manage user profile edition
     * @return void
     */
    public function editUser()
    {
        $this->redirectIfNotConnected();
        $user = $_SESSION['user'];

        if (isset($_POST['submit'])) {
            $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = null;

            // Change password if required by user (if new password provided)
            if(isset($_POST['password'], $_POST['passwordRepeat'])) {
                if (!$this->checkPassword($_POST['password'], $_POST['passwordRepeat'])) {
                    $_SESSION['error'] = "Les password ne correspondent pas, ou il ne respecte pas les critères de sécurité (minuscule, majuscule, nombre, caractère spécial)";
                    header("Location: /?c=user");
                    exit;
                }
                $password = password_hash($_POST['password'], PASSWORD_ARGON2I);
            }

            UserManager::editUser($user->getId(), $firstname, $lastname, $email, $password);
            $user
                ->setFirstName($firstname)
                ->setLastName($lastname)
                ->setEmail($email)
            ;

            // Save the new User data into the session.
            $_SESSION['user'] = $user;
            $_SESSION['success'] = 'Votre profil a bien été modifié';

            $this->render('user/profile', [
                'profile' => $user
            ]);
        }
        else {
            // If form is not send, showing user profile and profile edition form.
            $this->showUser();
        }
    }


    /**
     * Manage user deletion.
     * @return void
     */
    public function deleteUser()
    {
        $this->redirectIfNotConnected();
        $user = $_SESSION['user'];

        // If user still exists.
        if (UserManager::userExists($user->getId())) {
            if(UserManager::deleteUser($user)) {
                $_SESSION['success'] = "Votre compte a bien été supprimé";
                self::disconnect();
            }
            else {
                $_SESSION['error'] = "Impossible de supprimer votre compte, veuillez contacter un administrateur svp";
            }
        }
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