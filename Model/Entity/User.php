<?php

namespace App\Model\Entity;

use AbstractEntity;

class User extends AbstractEntity
{
    private string $email;
    private string $firstname;
    private string $lastname;
    private string $password;
    private string $validationKey;
    private bool $valid;

    /**
     * @return string
     */
    public function getValidationKey(): string
    {
        return $this->validationKey;
    }

    /**
     * @param string $validationKey
     * @return User
     */
    public function setValidationKey(string $validationKey): self
    {
        $this->validationKey = $validationKey;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid): self
    {
        $this->valid = $valid;
        return $this;
    }
    private array $role;

    public function __construct()
    {
        $this->role= [];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return User
     */
    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getRole(): array
    {
        return $this->role;
    }

    /**
     * @param array|null $role
     * @return User
     */
    public function setRole(?array $role): self
    {
        $this->role = $role;
        return $this;
    }
}