<?php

namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManagerInterface;
use Authentication\Form\LoginForm;
use Application\Entity\User;
use Authentication\Model\AppAuthStorage;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class LoginController extends AbstractActionController
{
    private $entityManager;
    private $loginForm;
    private $ormAuthService;
    private $authStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoginForm $loginForm,
        $ormAuthService,
        AppAuthStorage $authStorage
    ) {
        $this->entityManager = $entityManager;
        $this->loginForm = $loginForm;
        $this->ormAuthService = $ormAuthService;
        $this->authStorage = $authStorage;
    }

    public function indexAction()
    {
        if ($this->identity()) {
            return $this->redirect()->toRoute('home');
            die;
        }

        $incorrect = '';
        $user = new User();
        $form = $this->loginForm;

        $form->setHydrator(new DoctrineObject($this->entityManager));
        $form->bind($user);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user = $form->getData();

                /* if ($result === false) it means user profile deleted */
                $result = $this->isUserActive($user->getUsername());
                if ($result === false) {
                    $incorrect = 'Profile deleted';
                } else {
                    $authResult = $this->entityManager->getRepository(User::class)->login($user, $this->ormAuthService);

                    if ($authResult->getCode() != \Zend\Authentication\Result::SUCCESS) {
                        $incorrect = 'Incorrect username or password';
                    } else {
                        if ($request->getPost('rememberMe') == 1) {
                            $time = 3600 * 24 * 360;
                            $this->authStorage->setRememberMe(1, $time);
                            $this->ormAuthService->setStorage($this->authStorage);
                        }
                        return $this->redirect()->toRoute('home');
                    }
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'incorrect' => $incorrect,
        ]);
    }
}
