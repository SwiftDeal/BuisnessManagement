<?php

// define routes

$routes = array(
    array(
        "pattern" => "about",
        "controller" => "home",
        "action" => "about"
    ),
    array(
        "pattern" => "home",
        "controller" => "home",
        "action" => "index"
    ),
    array(
        "pattern" => "work",
        "controller" => "home",
        "action" => "work"
    ),
    array(
        "pattern" => "team",
        "controller" => "home",
        "action" => "team"
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);
