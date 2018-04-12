# LDAP/Eloquent user provider for Laravel

WIP! Do not use!

This adds an 'ldapusers' provider to the Laravel auth scheme.  This is for our own particular needs and works the way we generally use our local LDAP service.

It does :

* If the user logs in with a username & password found in the users table - they are logged in as per a default laravel app.  This is to support 'external' users not in our LDAP system.
* Otherwise, if we find the username in LDAP we try and look up their details, create a local record for them in the 'users' table and (assuming their password is ok) log them in.

## Usage

In your `config/auth.php` file just change 'users' to 'ldapusers' in the default web guard :
```
...
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'ldapusers',
            // was 'users'
        ],
    ...
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
username - the primary username to look up (rather than 'email')
email - the users email address
surname - the users surname
forenames - the users forenames
is_staff - whether the user is a member of staff or not
```
