# phpBB Bundle

Symfony integration with phpBB. Use phpBB as a authentication provider and share its sessions.


## Configuration

First of all, make sure in your application to ignore the phpBB tables, by using (This is needed for each entity manager):

```yaml
doctrine:
    dbal:
        schema_filter: ~^(?!phpbb_)~
```
where phpbb_ is your table prefix for tables generated by phpBB. Not making this configuration change can cause your forum tables to be deleted!

Then, if you have your forum in other database, add a custom entity_manager and dbal to your doctrine connections:
```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            acme: # this is an example for your website's database but it's not required
                driver:   "%database_driver%"
                host:     "%database_host%"
                port:     "%database_port%"
                dbname:   "%database_name%"
                user:     "%database_user%"
                password: "%database_password%"
                charset:  "UTF8"
            forum:
                driver:   "%forum_database_driver%"
                host:     "%forum_database_host%"
                port:     "%forum_database_port%"
                dbname:   "%forum_database_name%"
                user:     "%forum_database_user%"
                password: "%forum_database_password%"
                charset:  "UTF8"

    orm:
        entity_managers:
            default: # same here, not required, but you will probably have this in your configuration
                connection: default
            forum:
                connection: forum
                mappings:
                    PhpbbBundle: ~
```

Then add the bundle configuration to `config/packages/phpbb.yaml`
```yaml
phpbb:
    session:
        cookie_name: "phpbb_foo" # must match your forum admin cookie name configuration
        login_page: "ucp.php?mode=login" # your login page, by default phpbb login page but you can use a custom page
        force_login: false # if true, anonymous users will be redirected to the login page
    database:
        entity_manager: "forum" # must match the key bellow doctrine.orm.entity_managers
        prefix: "phpbb_" # change this if you do not use the default "phpbb_" prefix
    roles: #relation between group_id from groups table of phpBB and roles of your application
        1: anonymous #GUESTS
        2: user #REGISTERED
        4: moderator #GLOBAL_MODERATORS
        5: administrator #ADMINISTRATORS
        6: bot #BOTS
        7: app_role_name #example of new group create in phpBB and new role in your application
        8: administrator #you can assing same application roles to various phpBB groups
```

Update your `config/packages/security.yaml` to match this:
```yaml
security:
    enable_authenticator_manager: true
    firewalls:
        main:
            pattern: ^/
            stateless: true # stateless should be set to true, or your symfony user may be stored in the session even if you logged out from the phpbb instance
            custom_authenticators:
                - TeLiXj\PhpbbBundle\Security\PhpbbSessionAuthenticator
```

And to use remember me function you must edit the `ucp.php` in your forum to enable the redirection after detect a correct session key.
Change the line `redirect(append_sid("{$phpbb_root_path}index.$phpEx"));` to `redirect($request->variable('redirect', append_sid("{$phpbb_root_path}index.$phpEx")));`

## Missing functionality

There are some few edge functionality missing:

  * `Session IP validation` is considered as "A.B.C", no matter what you specified in your Admin Control Panel configuration
