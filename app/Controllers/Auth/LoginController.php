<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class LoginController extends Controller
{
    public function index(Request $request, Response $response)
    {
        return $this
            ->view
            ->render($response, 'auth/login.twig');
    }

    public function store(Request $request, Response $response)
    {
        $input = (object) $request->getParsedBody();

        $validation = $this->validate($input, [
            'email' => v::noWhitespace()->notEmpty()->email()->not(v::uniqueEmail())
        ]);

        if ($validation->failed()) {
            $this->flash()->addMessage('errors', $validation->errors());
            return $this->redirectToRoute($request, $response, 'login.index');
        }

        // Authentication attempt
        $authenticated = $this->auth->attempt(
            $user = User::whereEmail($input->email)->first(),
            $input->password
        );

        if (!$authenticated) {
            $this->flash()->addMessage('errors', [
                'password' => ['Incorrect password']
            ]);
            return $this->redirectToRoute($request, $response, 'login.index');
        }

        $this->flash('messages', 'Welcome back ' . $user->name);
        return $this->redirectToRoute($request, $response, 'reminders.index');
    }
}
