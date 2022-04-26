<?php

namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Manager\RoleManager;
use Connect;

class UserManager
{
    public const TABLE = 'mdf58_user';

    /**
     * @return array
     */
    public static function getAll(): array
    {
        $users = [];
        $result = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE);

        if ($result) {
            foreach ($result->fetchAll() as $data) {
                $users[] = self::makeUser($data);
            }
        }
        return $users;
    }

    /**
     * Login
     * @param string $mail
     * @param string $password
     * @return void
     */
    public static function login(string $mail, string $password)
    {
        $stmt = Connect::dbConnect()->prepare("
            SELECT * FROM " . self::TABLE . " WHERE email = :email
        ");

        $stmt->bindParam(':email', $mail);

        if ($stmt->execute()) {
            $user = $stmt->fetch();
            if (isset($user['password'])) {
                if (password_verify($password, $user['password'])) {
                    $userSession = (new User())
                        ->setId($user['id'])
                        ->setEmail($user['email'])
                        ->setFirstname($user['firstname'])
                        ->setLastname($user['lastname'])
                        ->setPassword($user['password'])
                    ;

                    if (!isset($_SESSION['user'])) {
                        $_SESSION['user'] = $userSession;
                    }

                    $_SESSION['id'] = $userSession->getId();
                    header("Location: /?c=home&f=0");
                } else {
                    header("Location: /?c=user&a=login&f=10");
                }
            } else {
                header("Location: /?c=user&a=login&f=12");
            }
        }
    }

    /**
     * @param int $id
     * @return User|null
     */
    public static function getUserById(int $id): ?User
    {
        $result = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE . " WHERE id = $id");
        return $result ? self::makeUser($result->fetch()) : null;
    }

    /**
     * @param array $data
     * @return User
     */
    private static function makeUser(array $data): User
    {
        return (new User())
            ->setId($data['id'])
            ->setPassword($data['password'])
            ->setEmail($data['email'])
            ->setLastname($data['lastname'])
            ->setFirstname($data['firstname'])
            ->setRole($data['mdf58_role_fk']);
    }

    /**
     * @param string $mail
     * @return bool
     */
    public static function mailExists(string $mail): bool
    {
        $result = Connect::dbConnect()->query("SELECT count(*) as cnt FROM " . self::TABLE . " WHERE email = \"$mail\"");
        return $result ? $result->fetch()['cnt'] : 0;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function userExists(int $id): bool
    {
        $result = Connect::dbConnect()->query("SELECT count(*) as cnt FROM " . self::TABLE . " WHERE id = $id");
        return $result ? $result->fetch()['cnt'] : 0;
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function addUser(User &$user): bool
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO " . self::TABLE . " (email, firstname, lastname, password, mdf58_role_fk) 
            VALUES (:email, :firstname, :lastname, :password, :mdf58_role_fk)
        ");

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':firstname', $user->getFirstname());
        $stmt->bindValue(':lastname', $user->getLastname());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':mdf58_role_fk', $user->getRole());

        $result = $stmt->execute();
        $user->setId(Connect::dbConnect()->lastInsertId());

        return $result;
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function deleteUser(User $user): bool
    {
        if (self::userExists($user->getId())) {
            return Connect::dbConnect()->exec("
            DELETE FROM " . self::TABLE . " WHERE id = {$user->getId()}
        ");
        }
        return false;
    }

    /**
     * @param int $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return void
     */
    public static function editUser(int $id, string $firstname, string $lastname, string $email)
    {
        $stmt = Connect::dbConnect()->prepare("
            UPDATE " . self::TABLE ." SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = $id
        ");
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);

        ;

        $stmt->execute();
    }

    /**
     * @param string $mail
     * @return User|null
     */
    public static function getUserByMail(string $mail): ?User
    {
        $stmt = Connect::dbConnect()->prepare("SELECT * FROM " . self::TABLE . " WHERE email = :mail LIMIT 1");
        $stmt->bindParam(':email', $mail);
        return $stmt->execute() ? self::makeUser($stmt->fetch()) : null;
    }
}