<?php

require __DIR__ .'/../../../Model/Entity/AbstractEntity.php';
require __DIR__ .'/../../../Model/Entity/User.php';
require __DIR__ .'/../../../Model/Entity/Role.php';
require __DIR__ .'/../../../Model/Connect.php';
require __DIR__ .'/../../../Config.php';
require __DIR__ .'/../../../Model/Manager/UserManager.php';
require __DIR__ .'/../../../Model/Manager/RoleManager.php';

use App\Model\Entity\User;
use App\Model\Manager\UserManager;
use PHPUnit\Framework\TestCase;

class UserManagerTest extends TestCase
{
    /**
     * I test the function getUserByMail and I check if by putting a known address it sends it back to me
     * @return void
     */
    public function testCheckUserInBdd()
    {
        $user = new User();
        $userVerif = UserManager::getUserByMail("dehainaut.angelique@orange.fr");
        $user->setEmail('dehainaut.angelique@orange.fr');
        $this->assertEquals("dehainaut.angelique@orange.fr", $userVerif->getEmail());
    }
}

