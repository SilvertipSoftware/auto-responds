# AutoResponds

Opinionated response discovery for Laravel.

## About

Rather than specifically return views or objects from each of your controller methods, the `AutoResponds` trait
determines and automatically creates responses based on the route, controller, and desired response format. In most
cases, this means your controller methods become query-only.

Importantly, however, returns from controller methods are handled the same way they would be natively (mostly), so if
granular control is needed, you can have that too.


## Caveats

- you have to return the correct namespace in the `controllerRootNamespace()` function of your controller. We provide
the correct default, but you may have to change it, depending on your configuration. Laravel does not seem to provide
access to this parameter.
- this only works for controller-based routes.


## Installation

- Require the `silvertipsoftware/auto-responds` package in your `composer.json` and update your
dependencies:

```sh
$ composer require silvertipsoftware/auto-responds
```

- Add `use AutoResponds` in your base `Controller`, or anywhere you want it.

- If you're using a non-standard setup, implement `controllerRootNamespace()` in the same controller.


## View-Based Responses and Naming

By default, views are discovered based on the controller action being called, and prefixed by any intermediate
namespaces. For example, for HTML format, based on the default root controller namespace:

Controller Action...              | ...Maps to view
----------------------------------|----------------------------------
UsersController@show              | users.show
AccountsController@index          | accounts.index
Admin\InvitesController@new       | admin.invites.new

If you're using Javascript request/response (eg. UJS, or some other on-the-fly-js library):

Controller Action...              | ...Maps to view
----------------------------------|----------------------------------
UsersController@edit              | users.js.edit
AccountsController@update         | accounts.js.update
Admin\InvitesController@new       | admin.invites.js.new


## Using the Route Name

If you prefer, view names can be based off of the route name (defined using `'as'` in your routes file, for example).
To your base controller, add:

```php
    protected $useActionForViewName = false;
```

Now a route named `users.index` will map to a view called `users.index` (or `users.js.index` for the JS format).


## Overriding Detection

In some cases, you may want to use a specific view for a specific action. In that case, set the `viewNameForResponse`
property in your action. For example:

```php
    public function index($request) {
        if (!\Auth::user()) {
            $this->viewNameForResponse = 'need_to_login';
        }
    }
```

You may also return any standard response from Laravel, from strings, to views, to model instances. The `AutoResponds`
functionality is bypassed in these cases. 

The exception is for Javascript formats, where a specific view response is
used. By default, `AutoResponds` looks for a view called `js_redirect`, and passes it the target location for the
redirect as a `redirectToUrl` variable. Typically, this just looks like:

```javascript
window.location.href = '{{ $redirectToUrl }}';
```

but you're free to do whatever you need to.

## Format-Specific Notes

### Javascript

Javascript responses are all automatically wrapped as a Immediately Invoked Function Expression (or Self Executing 
Anonymous Function) to encapsulate variables, etc. Your js views do not need to include this.

### JSON

