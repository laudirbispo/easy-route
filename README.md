# EasyRouter

This is a simple class for you to start working with routes in your applications.



### Installation

Install the latest version with

```bash
$ composer require laudirbispo/easy-route
```

### Basic Usage

```php
<?php
use laudirbispo\EasyRouter\EasyRouter;
use laudirbispo\EasyRouter\Exceptions\RouterException;

$router = new EasyRouter();

/**
 * @param $pattern - string|array routes - accept multiple routes
 * @param $calback - class with method string|closure
 */
$router->add($pattern, $callback);

//Examples
$router->add(
    ['/admin/login', '/admin/login?(.*)'], 
    'app\IdentityAccess\Application\Controllers\AccountController::pageLogin'
);
// (.*) that is to say that it accepts any parameter after "?"
// example - /admin/login?return=other_url
 

// Closures and Regular expressions
$router->add(['/admin/users/group/edit/([-\w]{36}$)'], function ($groupId){
  $controller = new app\IdentityAccess\Application\Controllers\AdminGroups;
  $controller->pageEditGroup($groupId);
});
// For example, this regular expression, accepts only string in the format "4d428391-8975-4158-b68a-9e3054e3df2c" 
// "4d428391-8975-4158-b68a-9e3054e3df2c" is uuid4 string

// Other example
$router->add('news/(\w+)/(\d+)', function($category, $year){
  $controller = new app\News\Application\Controllers\News;
  $controller->getNewsByYear($category, $year);
});

// Check if router exists
$router->has(string $pattern);

// Execute 
$router->execute($_SERVER['REQUEST_URI']);

// Exeptions 
// RouterException
// ControllerDoesNotExists
// InvalidRoute
// MethodDoesNotExists
// RouteNotFound

```

### Author

Laudir Bispo - <laudirbispo@outlook.com> - <https://twitter.com/laudir_bispo><br />

### License

EasyRouter is licensed under the MIT License - see the `LICENSE` file for details
**Free Software, Hell Yeah!**
