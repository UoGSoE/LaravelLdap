# Mixed LDAP/Eloquent user provider for Laravel

WIP! Do not use!

This adds an 'ldapusers' provider to the Laravel auth scheme.  This is for our own particular needs and works the way we generally use our local LDAP service.

It does :

* If the user logs in with a username & password found in the users table - they are logged in as per a default laravel app.  This is to support 'external' users not in our LDAP system.
* Otherwise, if we find the username in LDAP we try and look up their details, create a local record for them in the 'users' table and (assuming their password is ok) log them in.

## Usage

In your `config/auth.php` file add the new provider and change 'users' to 'ldapusers' in the default web guard :
```
...
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'ldapusers',
            // originally 'provider' => 'users'
        ],
    ...
    'providers' => [
        ...
        'ldapusers' => [
            'driver' => 'ldapeloquent',
            'model' => App\User::class,
        ],
    ]
```

And you need to add the following to app\Http\Controllers\Auth\LoginController.php (assumes you've run `artisan make:auth`) :

```
    public function username()
    {
        return 'username';
    }
```

You need to set two ENV variables in your .env file :

```
LDAP_SERVER=your.ldap.server
LDAP_OU=your-base-ou
```

eg :

```
LDAP_SERVER=ldap.yourcompany.com
LDAP_OU=Staff
```

## Assumptions

We assume your user model is in the default `App\User` class.  We also expect the `users` table to have the following fields :
```
username (string) - the primary username to look up (rather than 'email')
email (string) - the users email address
surname (string) - the users surname
forenames (string) - the users forenames
is_staff (boolean) - whether the user is a member of staff or not
```
