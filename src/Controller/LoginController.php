<?php 

namespace Caramel\Controller;

use Caramel\Core\Http\Request;
use Caramel\Model\User;

class LoginController extends Controller
{

    public function index()
    {
        return $this->response('auth.login.index');
    }

    public function login(Request $request)
    {   
        $u = User::whereFirst('email', $request->body('email'), '=');

        if($u && password_verify($request->body('password'), $u->password))
        {
            $_SESSION['user'] = [
                'id' => $u->id,
            ];

            return $this->redirect('/');
        }

        $_SESSION['_flash']['errors'] = 'Invalid user info';

        return $this->redirect(route('auth.login'));
    }

}