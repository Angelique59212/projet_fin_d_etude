<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Model\Manager\RoleManager;
use App\Model\Manager\UserManager;

class UserController extends AbstractController
{
    private const TOKEN_MAX_VALIDITY = '+1 hours';
    private array $mailHeaders;

    public function __construct()
    {
        $this->mailHeaders = [
            'From' => 'dehainaut.angelique@orange.fr',
            'Reply-To' => 'dehainaut.angelique@orange.fr',
            'X-Mailer' => 'PHP/' . phpversion(),
            'Mime-Version' => '1.0',
            'Content-Type' => 'text/html; charset=utf-8'
        ];
    }

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
        $subject = 'Validation email';
        $message = "
            <a href=\"http://troubles-dys.angeliquedehai.fr/?c=user&a=email-validation&key=" . $validationKey . "&id=" . $user->getId() . "\"> 
            Valider mon adresse e-mail, afin de valider mon inscription sur les troubles-dys</a>
        ";

        if(!mail($mail, $subject, $message, $this->mailHeaders)) {
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

            // If user where found from database and password is ok.
            if ($user && password_verify($password, $user->getPassword())) {
                $user->setRole(RoleManager::getRoleByUser($user));

                // Account not validated.
                if (!$user->isValid()) {
                    $_SESSION['error'] = "Votre mail n'a pas été validé";
                }
                // Account validated, storing user in session.
                else {
                    $_SESSION['user'] = $user;
                }
            }
            else {
                $_SESSION['error'] = 'Mot de passe ou adresse mail incorrect';
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

        header("Location: /index.php");
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
            if (filter_var($userMail, FILTER_VALIDATE_EMAIL)) {
                if (strlen($message) >= 20 && strlen($message) <= 250) {
                    if (mail($to, $subject, $message, $this->mailHeaders, $userMail)) {
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
     * Send a reset password request. - Step 1 => Sending email with token.
     * @return void
     * @throws \Exception
     */
    public function resetPasswordRequest()
    {
        if(isset($_POST['reset-password-send-email']))
        {
            $mail = $this->dataClean($this->getFormField('email'));
            $token = $this->generateRandomString(30);

            // Saving reset password request into the database.
            $result = UserManager::addUserResetPasswordEntry($mail, $token);
            if(!$result) {
                $_SESSION['error'] = "Nous sommes désolé, une erreur interne est survenue, veuillez réessayez plus tard !";
                $this->render('user/reset-password-request');
            }

            // Getting the right user.
            $user = UserManager::getUserByMail($mail);
            if(!$user) {
                $_SESSION['error'] = "Nous n'avons pas trouvé votre compte, veuillez vérifier votre adresse mail";
                $this->render('user/reset-password-request');
            }

            $subject = 'Réinitialisation du mot de passe';
            $message = '
                Vous avez demandé à réinitialiser votre mot de passe, cliquez sur le lien suivant et suivez les instructions.
                Le lien est valable 1 heure, passé ce délai vous devrez faire une nouvelle demande.
                <a href="http://troubles-dys.angeliquedehai.fr/?c=user&a=reset-password-check-token&token='.$token.'"> 
                    Réinitialiser le mot de passe
                </a>
                Si le lien ne fonctionne pas, copiez collez le texte suivant dans la barre d\'adresse de votre navigateur.
                http://troubles-dys.angeliquedehai.fr/?c=user&a=reset-password-check-token&token='.$token.'
            ';

            if(!mail($mail, $subject, $message, $this->mailHeaders)) {
                $_SESSION['error'] = "Echec de l'envoi du mail.";
                //header("Location: /?c=home");
                //die();
            }

            echo "http://localhost:8000/?c=user&a=reset-password-check-token&token=$token";
            die();
        }
        $this->render('user/reset-password-request');
    }


    /**
     * Checking token validity and display reset password form.
     * @return void
     */
    public function resetPasswordCheckToken()
    {
        $token = $this->dataClean($_GET['token']);
        $tokenData = UserManager::getResetPasswordTokenData($token);
        $userMail = $tokenData['email'];

        if($tokenData) {
            $validUntil = \DateTime::createFromFormat('Y-m-d H:i:s', $tokenData['date_add'])->modify(self::TOKEN_MAX_VALIDITY);
            // checking token is still valid.
            if($validUntil > new \DateTime() ) {
                // token is valid, display the new password form.
                $_SESSION['tmp_user'] = UserManager::getUserByMail($userMail);
                $_SESSION['token'] = $token;
                $this->resetPasswordSetNewPassword();
            }
            else {
                // Token is not valid, redirect to the login page.
                $_SESSION['error'] = "Votre jeton d'authentification a expiré, veuillez faire une nouvelle demande";
                $this->login();
            }
        }
        else {
            $_SESSION['error'] = "Votre jeton d'authentification n'a pas été trouvé";
        }
    }


    /**
     * Display the reset password form. Step 2 => User choose a new password.
     * @return void
     */
    public function resetPasswordSetNewPassword()
    {
        if(!isset($_SESSION['tmp_user'])) {
            $_SESSION['error'] = "Impossible de trouver votre session";
            $this->render('user/reset-password-request');
        }

        if (isset($_POST['submit-new-password'])) {
            $user = $_SESSION['tmp_user'];
            $password = $this->getFormField('password');
            $passwordRepeat = $this->getFormField('password-repeat');

            if (!$this->checkPassword($password, $passwordRepeat)) {
                $_SESSION['error'] = "Les password ne correspondent pas, ou il ne respecte pas les critères de sécurité (minuscule, majuscule, nombre, caractère spécial)";
                $this->render('user/reset-password-request');
            }

            $password = password_hash($password, PASSWORD_ARGON2I);
            $user->setPassword($password);
            UserManager::editUser($user->getId(), $user->getFirstname(), $user->getLastname(), $user->getEmail(), $password);
            UserManager::deleteUserResetPasswordEntry($user->getEmail(), $_SESSION['token']);
            $_SESSION['success'] = "Votre mot de passe a bien été changé, vous pouvez maintenant vous connecter";
            $this->login();
        }

        $this->render('user/reset-password-set-new-password');
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