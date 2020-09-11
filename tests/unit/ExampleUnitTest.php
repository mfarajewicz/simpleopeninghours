<?php
/**
 * SimpleOpeningHours plugin for Craft CMS 3.x
 *
 * Simple plugin for storing place opening hours
 *
 * @link      https://www.gearrilla.com/en/
 * @copyright Copyright (c) 2020 Miroslaw Farajewicz
 */

namespace mfarajewicz\simpleopeninghourstests\unit;

use Codeception\Test\Unit;
use UnitTester;
use Craft;
use mfarajewicz\simpleopeninghours\SimpleOpeningHours;

/**
 * ExampleUnitTest
 *
 *
 * @author    Miroslaw Farajewicz
 * @package   SimpleOpeningHours
 * @since     1.0.0
 */
class ExampleUnitTest extends Unit
{
    // Properties
    // =========================================================================

    /**
     * @var UnitTester
     */
    protected $tester;

    // Public methods
    // =========================================================================

    // Tests
    // =========================================================================

    /**
     *
     */
    public function testPluginInstance()
    {
        $this->assertEquals(1, 2);
        $this->assertInstanceOf(
            SimpleOpeningHours::class,
            SimpleOpeningHours::$plugin
        );
    }

    /**
     *
     */
    public function testCraftEdition()
    {
        Craft::$app->setEdition(Craft::Pro);

        $this->assertSame(
            Craft::Pro,
            Craft::$app->getEdition()
        );
    }
}
