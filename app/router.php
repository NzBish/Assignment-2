<?php
use PHPRouter\RouteCollection;
use PHPRouter\Router;
use PHPRouter\Route;

$collection = new RouteCollection();

// example of using a redirect to another route
$collection->attachRoute(
    new Route(
        '/',
        array(
            '_controller' => 'agilman\a2\controller\HomeController::indexAction',
            'methods' => 'GET',
            'name' => 'Home'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::indexAction',
        'methods' => 'GET',
        'name' => 'accountIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/create/',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::createAction',
        'methods' => array('GET', 'POST'),
        'name' => 'accountCreate'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/delete/:id',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::deleteAction',
        'methods' => 'GET',
        'name' => 'accountDelete'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/account/update/:id',
        array(
        '_controller' => 'agilman\a2\controller\AccountController::updateAction',
        'methods' => 'GET',
        'name' => 'accountUpdate'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/transactions/',
        array(
            '_controller' => 'agilman\a2\controller\TransactionController::indexAction',
            'methods' => 'GET',
            'name' => 'transactionIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/users/',
        array(
            '_controller' => 'agilman\a2\controller\UserController::indexAction',
            'methods' => 'GET',
            'name' => 'userIndex'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/users/login/',
        array(
            '_controller' => 'agilman\a2\controller\UserController::loginAction',
            'methods' => array('GET','POST'),
            'name' => 'userLogin'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/users/logout/',
        array(
            '_controller' => 'agilman\a2\controller\UserController::logoutAction',
            'methods' => 'GET',
            'name' => 'userLogout'
        )
    )
);

$collection->attachRoute(
    new Route(
        '/users/create/',
        array(
            '_controller' => 'agilman\a2\controller\UserController::createAction',
            'methods' => array('GET','POST'),
            'name' => 'userCreate'
        )
    )
);

$router = new Router($collection);
$router->setBasePath('/');
