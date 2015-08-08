<?php
namespace GintonicCMS\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use GintonicCMS\Model\Table\UsersTable;

/**
 * GintonicCMS\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.gintonic_c_m_s.users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Users') ? [] : ['className' => 'GintonicCMS\Model\Table\UsersTable'];
        $this->Users = TableRegistry::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

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
     * Test findAvatar method
     *
     * @return void
     */
    public function testFindAvatar()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test findProfile method
     *
     * @return void
     */
    public function testFindProfile()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test changePassword method
     *
     * @return void
     */
    public function testChangePassword()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
