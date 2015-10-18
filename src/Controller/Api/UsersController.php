<?php
namespace Users\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use JWT;

/**
 * Users Controller
 * Based on admad/cakephp-jwt-auth and friendsofcake/crud
 *
 * @see http://www.bravo-kernel.com/2015/04/how-to-add-jwt-authentication-to-a-cakephp-3-rest-api/
 */
class UsersController extends AppController
{
    public $paginate = [
        'page' => 1,
        'limit' => 5,
        'maxLimit' => 15,
        'sortWhitelist' => [
            'id', 'name'
        ]
    ];

    /**
     * {@inherit}
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'token']);
    }

    public function index()
    {
        $this->Crud->on('beforePaginate', function(Event $event) {
            $query = $this->Users->find('search', $this->request->query);
            $query = $query->select(['username']);
            $event->subject->query = $query;
        });
        $this->Crud->execute();
    }

    /**
     * Regular register method now also returns a token upon registration
     *
     * token expiration is set for 1 week
     */
    public function add()
    {
        $this->Crud->on('afterSave', function (Event $event) {
            if ($event->subject->created) {
                $token = [
                    'id' => $event->subject->entity->id,
                    'exp' => time() + 60 * 60 * 24 * 7,
                ];
                $this->set('data', [
                    'id' => $event->subject->entity->id,
                    'token' => JWT::encode($token, Security::salt())
                ]);
                $this->Crud->action()->config('serialize.data', 'data');
            }
        });
        return $this->Crud->execute();
    }

    /**
     * Tries to authentify user based on POST data and returns a private token
     */
    public function token()
    {
        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException('Invalid username or password');
        }

        $token = [
            'id' => $user['id'],
            'exp' => time() + 60 * 60 * 24 * 7
        ];

        $this->set([
            'success' => true,
            'data' => ['token' => JWT::encode($token, Security::salt())],
            '_serialize' => ['success', 'data']
        ]);
    }
}
