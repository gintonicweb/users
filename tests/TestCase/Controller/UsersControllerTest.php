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
        $this->assertEquals($user['token'],false);
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

        $data = [
            'email' => 'test@blackhole.io',
            'password' => '123456',
            'remember' => 'remember-me',
        ];
        $this->post('/signin', $data);
        $this->assertResponseSuccess();

        $cookie = (array)json_decode($this->_response->cookie('User')['value']);
        $this->assertEquals($cookie['id'], 1);
        $this->assertSession(1, 'Auth.User.id');
    }

    /**
     * Test signout method
     *
     * @return void
     */
    public function testSignout()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1
                ]
            ]
        ]);
        $this->post('/signout');
        $this->assertSession(null, 'Auth.User.id');

        // TODO: Check that cookie is unset
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1
                ]
            ]
        ]);
        $data = [
            'username' => 'new username',
            'email' => 'new@blackhole.io'
        ];
        $this->post('/users/edit', $data);
        $this->assertResponseSuccess();

        $usersTable = TableRegistry::get('Users.Users');
        $count = $usersTable->exists(['email' => 'new@blackhole.io']);
        $this->assertTrue($count);
    }

    /**
     * Test verify method
     *
     * @return void
     */
    public function testVerify()
    {
        $this->post('/users/verify/1/this.is.a.token');
        $this->assertResponseSuccess();

        $usersTable = TableRegistry::get('Users.Users');
        $user = $usersTable->get(1)->hiddenProperties([])->toArray();

        $this->assertEmpty($user['token']);
        $this->assertTrue($user['verified']);
        $this->assertSession(1, 'Auth.User.id');
    }

    /**
     * Test sendVerification method
     *
     * @return void
     */
    public function testSendVerification()
    {
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1
                ]
            ]
        ]);
        $this->post('/users/sendVerification');
        $this->assertResponseSuccess();

        $usersTable = TableRegistry::get('Users.Users');
        $user = $usersTable->get(1)->hiddenProperties([])->toArray();
        $this->assertNotEmpty($user['token']);
        $this->assertEquals(strlen($user['token']), '32');
    }

    /**
     * Test sendRecovery method
     *
     * @return void
     */
    public function testSendRecovery()
    {
        $data = [
            'email' => 'test@blackhole.io'
        ];
        $this->post('/users/sendRecovery', $data);
        $this->assertResponseSuccess();

        $usersTable = TableRegistry::get('Users.Users');
        $user = $usersTable->get(1)->hiddenProperties([])->toArray();
        $this->assertNotEmpty($user['token']);
        $this->assertEquals(strlen($user['token']), '32');
    }
}
