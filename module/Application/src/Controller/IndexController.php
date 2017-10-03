<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\Status;
use Application\Entity\StatusLike;
use Application\Entity\User;
use Application\Entity\Relationship;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Authentication\Controller\LogoutController;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    private $entityManager;
    private $statusRepository;
    private $relationshipRepository;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager          = $entityManager;
        $this->statusRepository       = $this->entityManager->getRepository(Status::class);
        $this->relationshipRepository = $this->entityManager->getRepository(Relationship::class);
        $this->userRepository         = $this->entityManager->getRepository(User::class);
        $this->statusLikeRepository   = $this->entityManager->getRepository(StatusLike::class);
    }

    public function indexAction()
    {
        $view =  new ViewModel();

        if (! $this->identity()) {
            $view->setTemplate('application/index/index');
            return $view;
        }

        if ($this->isUserActive($this->identity()->getUsername()) === false) {
            return $this->forward()->dispatch(LogoutController::class, ['action' => 'index']);
        }

        $status = new Status();
        $form = $this->getAnnotationForm($this->entityManager, $status);
        $form->setValidationGroup(['content']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $status = $form->getData();
                $status->setUser($this->identity());

                $this->entityManager->persist($status);
                $this->entityManager->flush();

                return $this->redirect()->toRoute('home');
            }
        }

        $hasFriends = $this->relationshipRepository
                           ->getFriends($this->identity()->getId());

        $paginator = $this->getStatuses($hasFriends);
        if ($paginator) {
            $pageNumber = (int)$paginator->getCurrentPageNumber();
        } else {
            $pageNumber = false;
        }


        $view->setVariables([
            'form'       => $form,
            'replayForm' => $form,
            'statuses'   => $paginator,
            'hasFriends' => $hasFriends,
            'pageNumber' => $pageNumber,
        ]);

        $view->setTemplate('application/index/main');
        return $view;
    }

    public function replayAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $pageNumber = (int)$this->params()->fromRoute('page', 0);
        $status = new Status();
        $form = $this->getAnnotationForm($this->entityManager, $status);
        $form->setValidationGroup(['content']);

        $request = $this->getRequest();
        if (! $request->isPost()) {
            return $this->notFoundAction();
        }

        $content = $request->getPost()->toArray();
        $content = $this->clearString($content['replay-' . $id]);

        if (! empty($content)) {

            $status->setUser($this->identity());
            $status->setParentId($id);
            $status->setContent($content);

            $this->entityManager->persist($status);
            $this->entityManager->flush();
        }

        return $this->redirect()->toRoute('application', ['page' => $pageNumber]);
    }

    public function getStatuses($hasFriends)
    {
        //////   If user has friends only then show statuses   //////

        if ($hasFriends) {
            $friendsIds = $this->getUserFriends($this->entityManager, $this->identity(), true);
            $statusesQuery = $this->statusRepository
                ->getStatuses($this->identity()->getId(), $friendsIds);

            $adapter = new DoctrinePaginator(new ORMPaginator($statusesQuery));
            $paginator = new Paginator($adapter);

            $currentPageNumber = (int)$this->params()->fromRoute('page', 1);
            $paginator->setCurrentPageNumber($currentPageNumber);

            $itemCountPerPage = 10;
            $paginator->setItemCountPerPage($itemCountPerPage);
        } else {
            $paginator = false;
        }

        return $paginator;
    }

    public function translateAction()
    {
        $query = $this->request->getQuery('language', 'en_US');

        $container = new Container('language');
        $container->language = $query;

        return $this->redirect()->toRoute('home');
    }
}
