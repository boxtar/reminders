<?php

namespace App\Services\Auth;

use App\Models\User;

class Auth
{
    // User id key for session
    protected $sessionKey = 'user_id';

    // HTTP Agent key for session (added session security)
    protected $securityKey = 'agent';

    // Cached user if authenticated
    protected $user;

    // Indicates if user logs out during the request.
    protected $loggedOut = false;

    /**
     * Checks if the request has previously authenticated.
     * I say request instead of user as it may be a programmatic access.
     * 
     * @return bool
     */
    public function check(): bool
    {
        if (
            !isset($_SESSION[$this->securityKey]) ||
            $_SESSION[$this->securityKey] != $this->getSecurityHash()
        ) {
            return false;
        }
        return true;
        // return isset($_SESSION[$this->sessionKey]);
    }

    /**
     * Returns true if current request is not authenticated
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Returns the currently authenticated user instance or null if not authenticated
     * 
     * @return User|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (!is_null($this->user)) {
            return $this->user;
        }

        $this->user = User::find($_SESSION[$this->sessionKey]);

        return $this->user;
    }

    /**
     * Attempt to authenticate user
     * 
     * @param User $user Instance of the user to try and authenticate using the provided password
     * @param string $password The password provided to compare against the provided user.
     */
    public function attempt($user, $password)
    {
        // Verify password
        if (!password_verify($password, $user->password)) {
            return false;
        }

        // Log the userin
        $this->login($user);

        return true;
    }

    // Saves the user id to the server session
    // and caches the User instance.
    public function login($user)
    {
        return $this
            ->setSession($user->id)
            ->setUser($user);
    }

    /**
     * Logs the user out.
     */
    public function logout()
    {
        // Clear session variables
        $this->clearSession();
        // Destroy the session
        session_destroy();
        // Destroy the session cookie:
        setcookie(session_name(), '', time() - 3600, '/');
        // Clear cached user ref and mark as logged out
        $this->clearUser();
    }

    /**
     * Set user id into session. I've extracted this bit out to a 
     * separate method as I may want to add more functionality
     * and security measures as I learn more about sessions.
     */
    protected function setSession($id)
    {
        $_SESSION[$this->sessionKey] = $id;
        $_SESSION[$this->securityKey] = $this->getSecurityHash();
        return $this;
    }

    /**
     * Clears the session array
     */
    protected function clearSession()
    {
        $_SESSION = array();
        return $this;
    }

    /**
     * Cache the provided user. Extracted to a separate function incase
     * extra functionality is required in the future.
     */
    protected function setUser($user)
    {
        $this->user = $user;
        $this->loggedOut = false;
        return $this;
    }

    /**
     * Clears the cached user reference and sets loggedOut to true for the 
     * remainder of the request.
     */
    protected function clearUser()
    {
        // reset ref to user for remainder of request
        $this->user = null;
        // indicate logged out status for remainder of the request
        $this->loggedOut = true;
        return $this;
    }

    protected function getSecurityHash()
    {
        return md5($_SERVER['HTTP_USER_AGENT']);
    }
}
