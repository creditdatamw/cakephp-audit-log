<?php

namespace AuditLog\Model\Table;

use Cake\Http\ServerRequestFactory;

trait CurrentUserTrait
{
    public function currentUser()
    {
        $username = isset($_SESSION) ? $_SESSION['Auth']['User']['username'] : '';
        return [
            'id' => $username,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'url' => $_SERVER['REQUEST_URI'],
            'description' => h(sprintf('Action by %s', $username)),
        ];
    }

    public function getDeleteEventDescription()
    {
        $session = ServerRequestFactory::fromGlobals()->getSession();
        $description = $session->consume('Auditable.auditDescription');
        if (!$description) {
            return h(sprintf('Action by %s', $session->read('Auth.User.username')));
        }
        return $description;
    }
}