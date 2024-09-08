<?php 

namespace Caramel\Controller;

use Caramel\Model\User;

class RegisterController extends Controller
{

    public function create()
    {
        return $this->response('auth.register.create');
    }

    public function store($body)
    {

        $u = User::store([
            'username' => $body['username'],
            'email' => $body['email'],
            'password' => password_hash($body['password'], PASSWORD_BCRYPT),
        ]);

        return $this->redirect('/login');
    }

}