<?php

namespace GintonicCMS\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use GintonicCMS\Model\Table\ThreadsTable;

/**
 * GintonicCMS\Model\Table\ThreadsTable Test Case
 */
class ThreadsTableTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.gintonic_c_m_s.threads',
        'plugin.gintonic_c_m_s.messages',
        'plugin.gintonic_c_m_s.users',
        'plugin.gintonic_c_m_s.threads_users',
        'plugin.gintonic_c_m_s.message_read_statuses'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Threads = TableRegistry::get('Threads', [
            'className' => 'GintonicCMS\Model\Table\ThreadsTable'
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Threads);
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertInstanceOf(
            'Cake\ORM\Association\BelongsToMany', $this->Threads->Users
        );
        $this->assertInstanceOf(
            'Cake\ORM\Association\HasMany', $this->Threads->Messages
        );
    }

    /**
     * Test findWithUsers method
     *
     * @return void
     */
    public function testFindWithUsers()
    {
        $users = [
            ['id' => 1]
        ];

        $query = $this->Threads->find('withUsers', $users)->select(['id']);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();
        $expected = [
            ['id' => 1],
            ['id' => 2],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Test findDetails method
     *
     * @return void
     */
    public function testFindDetails()
    {
        $threads = [
            ['id' => 1]
        ];
        $query = $this->Threads->find('details', $threads)
            ->select(['id'])
            ->contain(['Messages' => function($query){
                return $query
                    ->select(['id', 'thread_id'])
                    ->contain([
                        'Users'=>function($q){
                        return $q
                            ->select('Users.id');
                    }]);
            }]);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        $expected = [
            [
                'id' => 1,
                'messages' => [
                    [
                        'id' => 1,
                        'thread_id' => 1,
                        'user' => [
                            'id' => 1
                        ]
                    ],
                    [
                        'id' => 2,
                        'thread_id' => 1,
                        'user' => [
                            'id' => 2,
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * Test findWithUserCount method
     *
     * @return void
     */
    public function testFindWithUserCount()
    { 
        $query = $this->Threads->find('withUserCount', ['count' => 2]);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();
        $expected = [
            ['id' => 1],
            ['id' => 3]
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test findUnread method
     *
     * @return void
     */
    public function testFindUnread()
    {
        $query = $this->Threads->find('unread')->select('Messages.id');
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        $expected = [
            [
                '_matchingData' => [
                    'Messages' => [
                        'id' => 3
                    ]
                ]
            ],
            [
                '_matchingData' => [
                    'Messages' => [
                        'id' => 5
                    ]
                ]
            ],
            [
                '_matchingData' => [
                    'Messages' => [
                        'id' => 7
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test findDeleted method
     *
     * @return void
     */
    public function testFindDeleted()
    {
        $users = [];
        $query = $this->Threads->find('deleted', $users)->select('Messages.id');
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        $expected = [
            [
                '_matchingData' => [
                    'Messages' => [
                        'id' => 6
                    ]
                ]
            ]
        ];
        $this->assertEquals($expected, $result);
    }
}
