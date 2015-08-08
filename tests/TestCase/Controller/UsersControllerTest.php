<?php
namespace GintonicCMS\Test\TestCase\Controller;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\IntegrationTestCase;
use GintonicCMS\Controller\UsersController;

/**
 * GintonicCMS\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.gintonic_c_m_s.users',
        'plugin.gintonic_c_m_s.acos',
        'plugin.gintonic_c_m_s.aros',
        'plugin.gintonic_c_m_s.aros_acos'
    ];

    /**
     * Test beforeFilter method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $this->get('/users/signin');
        $this->assertResponseOk();
        $this->assertLayout('bare');

        $this->get('/users/signup');
        $this->assertResponseOk();
        $this->assertLayout('bare');

        $this->get('/users/verify/1/jhfkjd456d4sgdsg');
        $this->assertSession('alert-success', 'Flash.flash.params.class');
        $this->assertLayout('bare');

        $this->get('/users/recover/1/jhfkjd456d4sgdsg');
        $this->assertResponseOk();
        $this->assertLayout('bare');
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
        $this->assertResponseNotEmpty();
        $this->assertResponseContains('First1 Last1');
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
        $this->get('/users/edit');
        $this->assertResponseOk();

        $this->post('/users/edit', [
            'email' => 'newmail@blackhole.io',
            'pwd' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $this->assertResponseCode(302);

        $this->post('/users/edit', [
            'email' => 'newmail@blackhole.io',
            'pwd' => 'dummy',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $this->assertResponseCode(302);
    }

    /**
     * Test signup method
     *
     * @return void
     */
    public function testSignup()
    {
        // Default form should show up on get
        $this->get('/users/signup');
        $this->assertResponseOk();

        // New users should be able to create an account
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $this->assertSession('alert-info', 'Flash.flash.params.class');
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'view',
            'plugin' => 'GintonicCMS'
        ]);

        $this->get('/users/signout');
        $this->assertResponseCode(302);

        // Emails should be unique
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => 'abababa',
            'first' => 'Phil2',
            'last' => 'Laf2'
        ]);
        $this->assertResponseOk(); // no redirect
    }

    /**
     * Test signin method
     *
     * @return void
     */
    public function testSignin()
    {
        // Regular form shows up
        $this->get('/users/signin');
        $this->assertResponseOk();

        // Lets start by creating a test-account
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $this->get('/users/signout');

        // Shouldn't be able to signin with wrong password
        $this->post('/users/signin', [
            'email' => 'newmail@blackhole.io',
            'password' => 'wrong password',
        ]);
        $this->assertResponseOk();

        // Should be able to signin with right password
        $this->post('/users/signin', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
        ]);
        $this->assertSession('alert-warning', 'Flash.flash.params.class');
        $this->assertSession('6', 'Auth.User.id');
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'view',
            'plugin' => 'GintonicCMS'
        ]);
    }

    /**
     * Test signout method
     *
     * @return void
     */
    public function testSignout()
    {
        // Lets start by creating a test-account
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        // We should be logged in
        $this->assertSession('6', 'Auth.User.id');

        // See if we're logged out
        $this->get('/users/signout');
        $this->assertSession(null, 'Auth.User.id');
    }

    /**
     * Test verify method
     *
     * @return void
     */
    public function testVerify()
    {
        // Lets start by creating a test-account
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $this->assertSession(false, 'Auth.User.verified');

        // We should be able to verify it
        $token = $_SESSION['Auth']['User']['token'];
        $this->post('/users/verify/6/'.$token);
        $this->assertSession('alert-success', 'Flash.flash.params.class');

        // The user should be verified now
        $this->get('/users/signout');
        $this->post('/users/signin', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
        ]);
        $this->assertSession(true, 'Auth.User.verified');
    }

    /**
     * Test recover method
     *
     * @return void
     */
    public function testRecover()
    {
        // Lets start by creating a test-account
        $this->post('/users/signup', [
            'email' => 'newmail@blackhole.io',
            'password' => '123456',
            'first' => 'Phil',
            'last' => 'Laf'
        ]);
        $token = $_SESSION['Auth']['User']['token'];
        $this->get('/users/signout');

        // Shouldn't be possible to update with an invalid token
        $this->post('/users/recover/6/badtoken', [
            'email' => 'newmail@blackhole.io',
            'password' => 'abcdef',
        ]);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'sendRecovery',
            'plugin' => 'GintonicCMS'
        ]);

        // Reset should be possible with correct token
        $this->post('/users/recover/6/'.$token, [
            'email' => 'newmail@blackhole.io',
            'password' => 'abcdef',
        ]);
        $this->assertSession('alert-success', 'Flash.flash.params.class');
        $this->assertSession(true, 'Auth.User.id');

        // New password should be working too
        $this->get('/users/signout');
        $this->post('/users/signin', [
            'email' => 'newmail@blackhole.io',
            'password' => 'abcdef',
        ]);
        $this->assertSession(true, 'Auth.User.id');
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
        $this->get('/users/sendVerification');
        $this->assertSession('alert-success', 'Flash.flash.params.class');
        $this->assertResponseCode(302);
    }

    /**
     * Test sendRecovery method
     *
     * @return void
     */
    public function testSendRecovery()
    {
        $this->get('/users/sendRecovery');
        $this->assertResponseOk();

        $this->post('/users/sendRecovery',[
            'email' => 'user1@blackhole.io',
        ]);
        $this->assertResponseCode(302);
    }
}
