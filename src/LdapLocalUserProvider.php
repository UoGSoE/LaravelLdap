<?php

namespace UoGSoE\LaravelLdap;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class LdapLocalUserProvider extends \Illuminate\Auth\EloquentUserProvider
{
    protected $ldapUser;

    public function retrieveByCredentials(array $credentials)
    {
        if (array_key_exists('username', $credentials)) {
            $credentials['username'] = strtolower($credentials['username']);
        }

        $user = parent::retrieveByCredentials($credentials);
        if ($user) {
            return $user;
        }

        $this->ldapUser = \Ldap::findUser($credentials['username']);
        if (!$this->ldapUser) {
            return null;
        }
        $userClass = config('auth.providers.ldapusers.model', \App\Models\User::class);
        $user = new $userClass;
        $user->password = bcrypt(Str::random(64));
        $user->username = strtolower($this->ldapUser->username);
        $user->email = strtolower($this->ldapUser->email);
        $user->surname = $this->ldapUser->surname;
        $user->forenames = $this->ldapUser->forenames;
        if ($this->looksLikeMatric($this->ldapUser->username)) {
            $user->is_staff = false;
        } else {
            $user->is_staff = true;
        }
        $user->save();
        return $user;
    }

    public function validateCredentials(UserContract $user, array $credentials)
    {
        if (array_key_exists('username', $credentials)) {
            $credentials['username'] = strtolower($credentials['username']);
        }

        $password = $credentials['password'];
        if (! $password) {
            return false;
        }

        if ($this->hasher->check($password, $user->getAuthPassword())) {
            return true;
        }

        if (! \Ldap::authenticate($credentials['username'], $password)) {
            return false;
        }

        if (! $this->ldapUser) {
            $this->ldapUser = \Ldap::findUser($credentials['username']);
        }
        $localUser = $this->retrieveByCredentials($credentials);
        if (! $localUser) {
            throw new \Exception('Could not find local user matching username');
        }
        $localUser->username = strtolower($this->ldapUser->username);
        $localUser->email = strtolower($this->ldapUser->email);
        $localUser->surname = $this->ldapUser->surname;
        $localUser->forenames = $this->ldapUser->forenames;
        $localUser->save();

        return true;
    }

    protected function looksLikeMatric($username)
    {
        if (preg_match('/^[0-9]/', $username) === 1) {
            return true;
        }
        return false;
    }
}
