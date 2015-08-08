<?php
namespace GintonicCMS\Test\TestCase\Model\Entity;

use Cake\TestSuite\TestCase;
use GintonicCMS\Model\Entity\Thread;

/**
 * GintonicCMS\Model\Entity\Thread Test Case
 */
class ThreadTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Thread = new Thread();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Thread);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
