<?php

namespace GintonicCMS\Test\TestCase\Model\Entity;

use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use GintonicCMS\Model\Entity\User;

/**
 * GintonicCMS\Model\Entity\User Test Case
 */
class UserTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = new User();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);

        parent::tearDown();
    }

    /**
     * Test getFullName method
     *
     * @return void
     */
    public function testGetFullName()
    {
        $entity = new User([
            'id' => 1,
            'first' => 'Matt',
            'last' => 'Farrell',
        ]);
        $expected = 'Matt Farrell';
        $this->assertEquals($expected, $entity->fullName);
    }
    
    /**
     * Test sendRecovery method
     *
     * @return void
     */
    public function testSendRecovery()
    {
        $this->markTestIncomplete('Not implemented yet.');
//        $entity = new User([
//            'id' => 1,
//            'email' => 'hitesh@securemetasys.com',
//            'token' => 'jhfkjd456d4sgdsg'
//        ]);
////        debug($entity->sendRecovery());
//        $email = $this->getMock('Cake\Network\Email\Email', ['profile']);
//        $email->expects($this->once())
//            ->method('profile')
//            ->with('GintonicCMS.default');
//        
//        $entity->Email = $email;
//        $entity->sendRecovery();
//        exit;
        
    }

    /**
     * Test sendSignup method
     *
     * @return void
     */
    public function testSendSignup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test sendVerification method
     *
     * @return void
     */
    public function testSendVerification()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test updateToken method
     *
     * @return void
     */
    public function testUpdateToken()
    {
        $entity = new User([
            'id' => 1,
            'verified' => 0,
            'token' => 'jhfkjd456d4sgdsg',
            'token_creation' => '2015-05-22 15:39:23'
        ]);
        $entity->updateToken();
        $this->assertTrue($entity->token_creation->wasWithinLast('1 seconds'));
    }

    /**
     * Test verify method
     *
     * @return void
     */
    public function testVerify()
    {
        $entity = new User([
            'id' => 1,
            'verified' => false,
            'token' => 'jhfkjd456d4sgdsg',
            'token_creation' => '2015-05-20 15:39:23'
        ]);
        $this->assertFalse($entity->verify('jhfkjd456d4sgdsg'));
    }
}
