<?php

namespace App\PublicModule\presenters;

use App\model\Constants;
use App\Presenters\BasePresenter;
use Nette;
use Nette\Application\UI\Form;

Class RegistrationPresenter extends BasePresenter
{
  /**
   * @return Form
   */
  public function createComponentRegistrationForm() : Form {
    $form = new Form();
    $form->addText('first_name', 'Jméno:')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH,Constants::FORM_LONG, 30);
    $form->addText('last_name','Přijmení:')
      ->setRequired('přijmení: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH, Constants::FORM_LONG, 30);
    $form->addEmail('email', 'Email:')
      ->setRequired('email: '. Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::EMAIL, Constants::FORM_VALID_EMAIL)
      ->addRule(FORM::IS_NOT_IN,Constants::FORM_EMAIL_UNIQ, $this->userModel->getAllFromOneColumn('email'));
    $form->addPassword('passwd', 'Heslo:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::LENGTH, Constants::FORM_LENGHT_PASSWD, [5, 40]);
    $form->addPassword('passwd_verify', 'Heslo znova:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::EQUAL, Constants::FORM_MATCH_PASSWD, $form['passwd'])
      ->setOmitted();
    $form->addCheckbox('agree_with_terms', 'Souhlasím s licenčníma podmínkama')
      ->setRequired('souhlas s licenčními podmínkami je povinný')
      ->setOmitted();
    $form->addSubmit('submit', 'registrovat');
    $form->onSuccess[] = [$this, 'registrationFormSucceed'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param Nette\Utils\ArrayHash $values
   * @throws Nette\Application\AbortException
   */
  public function registrationFormSucceed(Form $form, Nette\Utils\ArrayHash $values): void {
    try {
      $this->userModel->newUser($values);
      $this->flashMessage('Registrace proběhla úspešně.');
    } catch (\Exception $e) {
      $this->flashMessage('Neúspešná registrace');
      $this->redirect('this');
    }
   $this->redirect('Registration:login');
  }
  
  
  /**
   * @return Form
   */
  public function createComponentLoginForm() : Form {
    $form = new Form();
    $form->addEmail('email', 'Email:')
      ->setRequired('email: '. Constants::FORM_MSG_REQUIRED);
    $form->addPassword('passwd', 'Heslo:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED);
    $form->addSubmit('submit', 'Přihlásít');
    $form->onSuccess[] = [$this, 'loginFormSucceeded'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param Nette\Utils\ArrayHash $values
   * @throws Nette\Application\AbortException
   */
  public function loginFormSucceeded(Form $form, Nette\Utils\ArrayHash $values) : void {
    try {
      $this->getUser()->login($values->email, $values->passwd);
      $this->flashMessage('Byl jste úspěšně přihlášen.');
    } catch (\Exception $e) {
      $this->flashMessage($e->getMessage());
    }
    $this->redirect(':Sys:Homepage:default');
  
  }
}
