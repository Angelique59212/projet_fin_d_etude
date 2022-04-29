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
     * @return array
     */
    public static function getRoleByUser(User $user): array
    {
        $roles = [];
        $query = Connect::dbConnect()->query("
            SELECT * FROM mdf58_role
                        WHERE id IN (SELECT mdf58_role_fk FROM mdf58_user WHERE id = {$user->getId()})");
        if($query){
            foreach($query->fetchAll() as $roleData) {
                $roles[] = (new Role())
                    ->setId($roleData['id'])
                    ->setRoleName($roleData['role_name'])
                ;
            }
        }
        return $roles;
    }

    /**
     * @param string $roleName
     * @return Role
     */
    public static function getRoleByName(string $roleName): Role
    {
        $role = new Role();
        $rQuery = Connect::dbConnect()->query("
            SELECT * FROM mdf58_role WHERE role_name = '".$roleName."'
        ");
        if($rQuery && $roleData = $rQuery->fetch()) {
            $role->setId($roleData['id']);
            $role->setRoleName($roleData['role_name']);
        }
        return $role;
    }
}