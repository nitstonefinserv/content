# Reflexions\Content

provides:

- Admin Layout
- ContentTrait
  - SluggableTrait
  - TaggableTrait
  - PublishableTrait
  - UploadTrait

optional traits:
- PostgresSearchableTrait
- UserRolesTrait

## Installation

1. Update `composer.json` to add the `reflexions\content` repository and package.

```json
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/reflexions/content.git"
        }
    ],
    "require" : {
    	// ...
        "reflexions/content" : "dev-master"
	}
```

2. Run `composer update` to install

```bash
$ composer update
```

3. Add `Reflexions\Content\ContentServiceProvider::class` to the `providers` array in `config/app.php`:
```php
    'providers' => [
    	// ...
        Reflexions\Content\ContentServiceProvider::class,
    ],
```

4. Publish the frontend assets
```bash
$ php artisan vendor:publish
```

##

## Running migrations

1. Add migration file:
```bash
$ php artisan make:migration add_reflexions_content
```

2. Add the code below into the migration file:
```php
    public function up()
    {
        Content::migrate();
    }

    public function down()
    {
        Content::rollback();
    }
```

### NB: When using postgreSQL, you have the option to include a migration that will create a postgres table required by `PostgresSearchableTrait`:
`Content::migrate([PostgresSearchMigration::class]);` and `Content::rollback([PostgresSearchMigration::class]);`

3. Run migration
```bash
$ php artisan migrate
```

##

## Setup routes

1. Add the default content routes to `routes.php`:
```php
Content::addRoutes();
```

##

## Adding items to the admin

1. Create a class for the options:
```php
class ClassName extends AdminOptions {
    /**
    * options go here
    */
}
```

2. Add routes to `routes.php`:
```php
Content::admin($route, ClassName::class);
```

Add routes with middleware:
```php
Content::adminWithMiddleware($route, $middleware, ClassName::class);
```

##

## Adding User Roles
1. Add resources available in the CMS to the config (content.php)
Example:
```php
'resources' => [
    'users' => 'Users',   	
]
```

2. In Http/Kernel.php's $routeMiddleware, add:
```php
'can_access' => \Reflexions\Content\Middleware\CheckAllowedPermissions::class,
'role' => \Reflexions\Content\Middleware\CheckUserRoles::class,
```
Usage example:
In routes.php
```php
Route::get('user', ['middleware' => 'can_access:users', 'as' => 'user', 'uses' => 'UserController@show']);
```

3. Add user roles field to user admin options:
```php
$form->addField($model->getUserRoleField());
```

### Restrict roles to only admin
In routes.php
```php
Content::adminWithMiddleware('role', 'role:admin', Reflexions\Content\Models\Admin\UserRoleAdminOptions::class);
```
