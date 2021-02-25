<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class UserModel
 * @package App\model
 */
class UserModel extends BaseFactory
{
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'user';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function newUser($data) {
    $data['passwd'] = \Model\Passwords::hash($data['passwd']);
    $this->db->table($this->table)->insert($data);
  }
  
  public function changePasswd($email, $passwd) {
    $this->db->table($this->table)
      ->where('email = ?', $email)
      ->update(['passwd' => \Model\Passwords::hash($passwd)]);
  }
  
  
  public function getAllEmails() {
    $this->getAllFromOneColumn('email');
    $emails = array();
    $result = $this->db->table($this->table)
      ->select('email')
      ->fetchAll();
    foreach ($result as $r) {
      $emails[] = $r->email;
    }
    
    bdump($emails);
    return $emails;
  }
}