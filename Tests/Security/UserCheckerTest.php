<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests\Security;

use FOS\UserBundle\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;

class UserCheckerTest extends TestCase
{

    public function testCheckPreAuthFailsLockedOut()
    {
        $this->expectException(LockedException::class);

        $userMock = $this->getUser(false, false, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\DisabledException
     */
    public function testCheckPreAuthFailsIsEnabled()
    {
        $this->expectException(DisabledException::class);

        $userMock = $this->getUser(true, false, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    /**
     *  User account has expired.
     */
    public function testCheckPreAuthFailsIsAccountNonExpired()
    {
        $this->expectException(AccountExpiredException::class);

        $userMock = $this->getUser(true, true, false, false);
        $checker = new UserChecker();
        $checker->checkPreAuth($userMock);
    }

    public function testCheckPreAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, false);
        $checker = new UserChecker();

        try {
            $this->assertNull($checker->checkPreAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    /**
     * User credentials have expired.
     */
    public function testCheckPostAuthFailsIsCredentialsNonExpired()
    {
        $this->expectException(CredentialsExpiredException::class);

        $userMock = $this->getUser(true, true, true, false);
        $checker = new UserChecker();
        $checker->checkPostAuth($userMock);
    }

    public function testCheckPostAuthSuccess()
    {
        $userMock = $this->getUser(true, true, true, true);
        $checker = new UserChecker();

        try {
            $this->assertNull($checker->checkPostAuth($userMock));
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    private function getUser($isAccountNonLocked, $isEnabled, $isAccountNonExpired, $isCredentialsNonExpired)
    {
        $userMock = $this->getMockBuilder('FOS\UserBundle\Model\User')->getMock();
        $userMock
            ->method('isAccountNonLocked')
            ->willReturn($isAccountNonLocked);
        $userMock
            ->method('isEnabled')
            ->willReturn($isEnabled);
        $userMock
            ->method('isAccountNonExpired')
            ->willReturn($isAccountNonExpired);
        $userMock
            ->method('isCredentialsNonExpired')
            ->willReturn($isCredentialsNonExpired);

        return $userMock;
    }
}
