<?php
namespace GintonicCMS\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use GintonicCMS\View\Helper\MenuHelper;

/**
 * GintonicCMS\View\Helper\MenuHelper Test Case
 */
class MenuHelperTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Menu = new MenuHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Menu);

        parent::tearDown();
    }

    /**
     * Test active method
     *
     * @return void
     */
    public function testActive()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test li method
     *
     * @return void
     */
    public function testLi()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test templates method
     *
     * @return void
     */
    public function testTemplates()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test formatTemplate method
     *
     * @return void
     */
    public function testFormatTemplate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test templater method
     *
     * @return void
     */
    public function testTemplater()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
