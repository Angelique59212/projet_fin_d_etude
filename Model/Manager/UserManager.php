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
     * @param int $id
     * @return bool
     */
    public static function validUser(int $id) : bool
    {
        $stmt = Connect::dbConnect()->prepare("
            UPDATE " . self::TABLE . " SET valid = :valid WHERE id = $id
        ");
        $stmt->bindValue(':valid', true);

        return $stmt->execute();
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
        $user = (new User())
            ->setId($data['id'])
            ->setPassword($data['password'])
            ->setEmail($data['email'])
            ->setLastname($data['lastname'])
            ->setFirstname($data['firstname'])
            ->setValidationKey($data['validation_key'])
            ->setValid($data['valid']);
        return $user->setRole(RoleManager::getRoleByUser($user));
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
            INSERT INTO " . self::TABLE . " (validation_key, email, firstname, lastname, password, role_fk) 
            VALUES (:validation_key, :email, :firstname, :lastname, :password, :role_fk)
        ");

        $stmt->bindValue(':email', $user->getEmail());
        $stmt->bindValue(':firstname', $user->getFirstname());
        $stmt->bindValue(':lastname', $user->getLastname());
        $stmt->bindValue(':password', $user->getPassword());
        $stmt->bindValue(':role_fk', $user->getRole()->getId());
        $stmt->bindValue(':validation_key', $user->getValidationKey());

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
     * @param string|null $password
     * @return void
     */
    public static function editUser(int $id, string $firstname, string $lastname, string $email, string $password = null)
    {
        $passwordField = null !== $password ? ', password=:password' : '';
        $stmt = Connect::dbConnect()->prepare("
            UPDATE " . self::TABLE .
                " SET firstname = :firstname, lastname = :lastname, email = :email" . $passwordField .
                " WHERE id = $id
        ");
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        if(null !== $password) {
            $stmt->bindParam(':password', $password);
        }

        $stmt->execute();
    }

    /**
     * @param string $mail
     * @return User|null
     */
    public static function getUserByMail(string $mail): ?User
    {
        $stmt = Connect::dbConnect()->prepare("SELECT * FROM " . self::TABLE . " WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $mail);
        return $stmt->execute() ? self::makeUser($stmt->fetch()) : null;
    }
}