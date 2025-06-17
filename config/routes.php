<?php
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->plugin('AuditLog', function (RouteBuilder $routes): void {
        $routes->prefix('admin', function (RouteBuilder $routes): void {
            $routes->fallbacks('DashedRoute');
        });
        $routes->fallbacks('DashedRoute');
    });
};
