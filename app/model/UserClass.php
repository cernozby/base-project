<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class User
 **/
class UserClass extends BaseFactory
{
  
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'user';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function getFullName() :string {
    return $this->get('first_name') . $this->get('last_name');
  }
  
  public function getRole() :string {
    return Constants::$user[$this->get('role')];
  }
  
  public function isAdmin() :bool {
    return $this->get('role') === Constants::USER_ADMIN;
  }
  
  public function isUser() :bool {
    return $this->get('role') === Constants::USER_USER;
  }
}