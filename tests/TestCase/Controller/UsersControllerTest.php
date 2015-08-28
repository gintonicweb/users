<?php
namespace Users\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Users\Controller\UsersController;

/**
 * Users\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.users.users'
    ];

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isAuthorized method
     *
     * @return void
     */
    public function testIsAuthorized()
    {
        $this->get('/users/view');
        $this->assertResponseCode(302);
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1
                ]
            ]
        ]);
        $this->get('/users/view');
        $this->assertResponseOk();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test signup method
     *
     * @return void
     */
    public function testSignup()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test signin method
     *
     * @return void
     */
    public function testSignin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test signout method
     *
     * @return void
     */
    public function testSignout()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test verify method
     *
     * @return void
     */
    public function testVerify()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test recover method
     *
     * @return void
     */
    public function testRecover()
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
     * Test sendRecovery method
     *
     * @return void
     */
    public function testSendRecovery()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getMailer method
     *
     * @return void
     */
    public function testGetMailer()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
