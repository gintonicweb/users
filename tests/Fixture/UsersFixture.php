<?php
namespace GintonicCMS\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'autoIncrement' => true, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'unsigned' => null],
        'email' => ['type' => 'string', 'length' => 255, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'fixed' => null],
        'first' => ['type' => 'string', 'length' => 255, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'fixed' => null],
        'last' => ['type' => 'string', 'length' => 255, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null, 'fixed' => null],
        'verified' => ['type' => 'boolean', 'length' => null, 'default' => 0, 'null' => false, 'comment' => null, 'precision' => null],
        'token' => ['type' => 'string', 'length' => 32, 'default' => '0', 'null' => false, 'comment' => null, 'precision' => null, 'fixed' => null],
        'token_creation' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => false, 'comment' => null, 'precision' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'default' => null, 'null' => true, 'comment' => null, 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'email' => 'user1@blackhole.io',
            'password' => '123456',
            'first' => 'First1',
            'last' => 'Last1',
            'verified' => 1,
            'token' => 'jhfkjd456d4sgdsg',
            'token_creation' => '2007-05-18 10:39:23',
            'created' => '2007-03-18 10:39:23',
            'modified' => '2007-04-18 15:50:00',
        ],
        [
            'email' => 'user2@blackhole.io',
            'password' => '123456',
            'first' => 'First2',
            'last' => 'Last2',
            'verified' => 1,
            'token' => 'jhsfkjd456d4sgdsg',
            'token_creation' => '2008-05-18 10:39:23',
            'created' => '2008-04-18 10:39:23',
            'modified' => '2008-09-18 15:50:00',
        ],
        [
            'email' => 'user3@blackhole.io',
            'password' => '123456',
            'first' => 'Fist3',
            'last' => 'Last3',
            'verified' => 1,
            'token' => 'jhfkjdygh456d4sgdsg',
            'token_creation' => '2009-03-18 10:39:23',
            'created' => '2009-03-18 10:39:23',
            'modified' => '2009-04-18 15:50:00',
        ],
        [
            'email' => 'user4@blackhole.io',
            'password' => '123456',
            'first' => 'First4',
            'last' => 'Last4',
            'verified' => 1,
            'token' => 'jhfkjd456d4sgdsg',
            'token_creation' => '2010-03-18 10:39:23',
            'created' => '2010-03-18 10:39:23',
            'modified' => '2010-04-18 15:50:00',
        ],
        [
            'email' => 'user5@blackhole.io',
            'password' => '123456',
            'first' => 'First5',
            'last' => 'Last5',
            'verified' => 1,
            'token' => 'jhfkjd456d4sgdsg',
            'token_creation' => '2010-03-18 10:39:23',
            'created' => '2010-03-18 10:39:23',
            'modified' => '2010-04-18 15:50:00',
        ]
    ];
}
