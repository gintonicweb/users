<?php
namespace Users\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
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
        $controller = new UsersController();
        $controller->initialize();
        $result = $controller->components()->loaded();

        $this->assertTrue(in_array('Auth', $result));
        $this->assertTrue(in_array('Cookie', $result));
        $this->assertTrue(in_array('Flash', $result));
    }

    /**
     * Test signup method
     *
     * @return void
     */
    public function testSignup()
    {
        $this->get('/signup');
        $this->assertResponseOk();

        $data = [
            'email' => 'phillaf@blackhole.io',
            'password' => '123456',
            'username' => 'phillaf',
        ];
        $this->post('/signup', $data);
        $this->assertResponseSuccess();

        $this->post('/signup', $data);
        $this->assertResponseFailure();

        $usersTable = TableRegistry::get('Users.Users');
        $user = $usersTable
            ->find()
            ->select(['email', 'password', 'username', 'verified', 'token'])
            ->where(['email' => 'phillaf@blackhole.io'])
            ->first();

        $user->hiddenProperties([]);
        $user = $user->toArray();

        $this->assertEquals($user['email'], 'phillaf@blackhole.io');
        $this->assertEquals($user['username'], 'phillaf');
        $this->assertEquals(60, strlen($user['password']));
        $this->assertFalse($user['verified']);
        $this->assertEquals(32, strlen($user['token']));
    }

    /**
     * Test signin method
     *
     * @return void
     */
    public function testSignin()
    {
        $this->get('/signin');
        $this->assertResponseOk();

        $data = [
            'email' => 'test@blackhole.io',
            'password' => 'wrong password',
        ];
        $this->post('/signin', $data);
        $this->assertResponseFailure();

        $data = [
            'email' => 'test@blackhole.io',
            'password' => '123456',
            'remember' => 'remember-me',
        ];
        $this->post('/signin', $data);
        $this->assertResponseSuccess();

        $cookie = (array)json_decode($this->_response->cookie('User')['value']);
        $this->markTestIncomplete('Not implemented yet.');
        //$this->assertEquals($cookie['id'], 1);
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
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
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
