<?php

namespace App\Presenters;
use App\model\UserModel;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;

abstract class BasePresenter extends Presenter {
  
  
  /**
   * @var UserModel
   */
  
  /**
   * @var Container
   */
  public $container;
  /**
   * @var UserModel
   */
  public $userModel;
  
  
  public function __construct(Container $container, UserModel $userModel) {
    $this->container = $container;
    $this->userModel = $userModel;
    parent::__construct();
  }
  
  /*public function startup() : void {
    parent::startup();
  }*/

  public function handleDeleteItem($type, $id) {

    try {
      $instance = $this->context->createInstance($type);
      $instance->initId($id);
      $instance->delete();
    } catch (Exception $e) {
      $this->flashMessage('Něco se pokazilo. Zkuste obnovit stránku', 'danger');
    }
    $this->flashMessage('Úspěšně smazáno', 'success');
    $this->redirect('this');
  }
}
