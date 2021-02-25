<?php
namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

class BaseModel {
  
  
  /**
   *
   * @var Explorer @inject
   */
  public $db;
  
  /**
   *
   * @var Container
   */
  public $container;
  
  /**
   *
   * @var LinkGenerator
   */
  public $linkGenerator;
  
  
  /**
   *
   * @param Explorer $database Database connection
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    
    $this->db = $database;
    $this->container = $container;
    $this->linkGenerator = $linkGenerator;
  }
  
  
  public function sendMail($typeOfEmail, $params, $toEmail, $subject, $attachments = null, $subjectPrefix = null, $attachmentsNames = null) {
    if ($subjectPrefix) {
      $subject = sprintf('[%s] %s', $subjectPrefix, $subject);
    }
    return $this->container->createInstance('MailModel')->sendEmail($typeOfEmail, $params, $toEmail, $subject, $attachments, null, $attachmentsNames);
  }
  
}