<?php

namespace Admin\Controller;

use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManagerInterface;
use Application\Entity\User;
use Application\Entity\Gallery;
use Authentication\Form\UpdateForm;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

class UserController extends AbstractActionController
{
    private $entityManager;
    private $updateForm;
    private $repository;
    private $galleryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UpdateForm $updateForm
    ) {
        $this->entityManager = $entityManager;
        $this->updateForm = $updateForm;
        $this->repository = $this->entityManager->getRepository(User::class);
        $this->galleryRepository = $this->entityManager->getRepository(Gallery::class);
    }

    public function indexAction()
    {
        $admins = $this->repository->findBy(['role' => 'admin']);
        return new ViewModel([
            'admins' => $admins,
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $user = $this->repository->find($id);

        if (! $id || ! $user) {
            return $this->notFoundAction();
        }

        $form = $this->updateForm;
        $form->setHydrator(new DoctrineObject($this->entityManager));
        $form->bind($user);
        $form->setValidationGroup(['role', 'active']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $user = $form->getData();

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->flashMessenger()->addSuccessMessage('User edited');
                return $this->redirect()->toRoute('admin/users', ['action' => 'edit', 'id' => $id]);
            }
        }

        return new ViewModel([
            'id'   => $id,
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function searchAction()
    {
        $results = [];
        $request = $this->getRequest();
        $response = $this->getResponse();

        if (! $request->isPost()) {
            return $this->notFoundAction();
        } else {
            $search = $request->getPost('admin-search-user');

            if (! empty($search)) {
                $search = $this->clearString($search);
                $users = $this->repository->searchAdminPage($search);

                $result = [];
                $results = [];

                if (is_array($users) && ! empty($users)) {
                    foreach ($users as $user) {
                        $result['id'] = $user->getId();

                        if ($user->getFirstName() && $user->getLastName()) {
                            $result['name'] = $user->getFirstName() . ' ' . $user->getLastName();
                        } else {
                            $result['name'] = $user->getUsername();
                        }

                        $results[] = $result;
                    }
                }
            }
        }

        $response->setContent(Json::encode(['results' => $results]));
        return $response;
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $user = $this->repository->find($id);

        if (! $id || ! $user) {
            return $this->notFoundAction();
        }

        $form = $this->updateForm;
        $form->setHydrator(new DoctrineObject($this->entityManager));
        $form->bind($user);
        $form->setValidationGroup(['csrf']);

        $form->setData($this->request->getPost());

        if ($form->isValid()) {
            $user = $form->getData();

            /* Block for deletion user profile image on server */
            $userImage = $user->getImage();
            if ($userImage) {
                if (is_file(getcwd() . '/public_html' . $userImage)) {
                    unlink(getcwd() . '/public_html' . $userImage);
                }
            }
            /* End block */

            /* Block for deletion gallery image on server */
            $gallery = $this->galleryRepository->findBy(['user' => $user]);

            array_walk($gallery, function ($imgObj) {
                if ($imgObj) {
                    if (is_file(getcwd() . '/public_html' . $imgObj->getImage())) {
                        unlink(getcwd() . '/public_html' . $imgObj->getImage());
                    }
                }
            });
            /* End block */

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->flashMessenger()->addSuccessMessage('User deleted');
            return $this->redirect()->toRoute('admin/users');
        }

        return $this->notFoundAction();
    }
}
