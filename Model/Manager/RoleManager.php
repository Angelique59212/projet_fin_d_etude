<?php

namespace App\Model\Manager;


use App\Model\Entity\Role;
use App\Model\Entity\User;
use Connect;


class RoleManager
{
    public const TABLE = "mdf58_role";

    /**
     * @param User $user
     * @return Role
     */
    public static function getRoleByUser(User $user): Role
    {
        // Getting user role id.
        $roleId = Connect::dbConnect()->query("SELECT role_fk FROM " . UserManager::TABLE . " WHERE id=" . $user->getId());
        $roleId = $roleId->fetch()['role_fk'];

        // Getting user role data.
        $query = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE . " WHERE id = $roleId");
        $roleData = $query->fetch();

        return (new Role())
            ->setId($roleData['id'])
            ->setRoleName($roleData['role_name'])
        ;
    }

    /**
     * @param string $roleName
     * @return Role
     */
    public static function getRoleByName(string $roleName): Role
    {
        $role = new Role();
        $rQuery = Connect::dbConnect()->query("
            SELECT * FROM " . self::TABLE . " WHERE role_name = '".$roleName."'
        ");
        if($rQuery && $roleData = $rQuery->fetch()) {
            $role->setId($roleData['id']);
            $role->setRoleName($roleData['role_name']);
        }
        return $role;
    }

    /**
     * Return the default role name
     * @return Role
     */
    public static function getDefaultRole(): Role
    {
        return self::getRoleByName('user');
    }
}
