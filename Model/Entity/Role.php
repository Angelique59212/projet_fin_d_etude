<?php

namespace App\Model\Entity;

use AbstractEntity;

class Role extends AbstractEntity
{
    private ?string $role_name;

    /**
     * @return string|null
     */
    public function getRoleName(): ?string
    {
        return $this->role_name;
    }

    /**
     * @param string|null $role_name
     * @return Role
     */
    public function setRoleName(?string $role_name): self
    {
        $this->role_name = $role_name;
        return $this;
    }


}