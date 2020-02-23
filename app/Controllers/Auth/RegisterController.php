<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use Respect\Validation\Validator as v;
use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class RegisterController extends Controller
{
    public function index(Request $request, Response $response)
    {
        return $this
            ->view
            ->render($response, 'auth/register.twig');
    }

    public function store(Request $request, Response $response)
    {
        $input = (object) $request->getParsedBody();

        $validation = $this->validate($input, [
            'email' => v::noWhitespace()->notEmpty()->email()->uniqueEmail(),
            'password' => v::noWhitespace()->notEmpty()->length(6),
        ]);

        if ($validation->failed()) {
            $this->flash()->addMessage('errors', $validation->errors());
            return $this->redirectToRoute($request, $response, 'register.index');
        }

        // Persist the new user. Perhaps move this to Model as a static and also
        // chuck out a verification email (or learn DDD)
        $user = User::create([
            'name' => $input->name ?? explode("@", $input->email)[0],
            'email' => $input->email,
            'password' => password_hash($input->password, PASSWORD_BCRYPT),
            'channels' => [
                'mail' => [
                    'to' => $input->email
                ]
            ],
        ]);

        // Validation passed, User created, log them in.
        $this->auth->login($user);

        // Flash a message and redirect.
        $this->flash('messages', 'Welcome ' . $user->name);
        return $this->redirectToRoute($request, $response, 'reminders.index');
    }
}
