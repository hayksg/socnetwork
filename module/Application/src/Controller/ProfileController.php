<?php

namespace Application\Controller;

use Application\Entity\Relationship;
use Application\Entity\User;
use Application\Entity\Gallery;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManagerInterface;
use Zend\View\Model\ViewModel;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Authentication\Form\UpdateForm;
use Authentication\Controller\LogoutController;
use Zend\Crypt\Password\Bcrypt;

class ProfileController extends AbstractActionController
{
    private $entityManager;
    private $updateForm;
    private $repository;
    private $galleryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UpdateForm $updateForm
    ) {
        $this->entityManager     = $entityManager;
        $this->updateForm        = $updateForm;
        $this->repository        = $this->entityManager->getRepository(User::class);
        $this->galleryRepository = $this->entityManager->getRepository(Gallery::class);
    }

    public function indexAction()
    {
        $username = $this->params()->fromRoute('username');
        $username = $this->clearString($username);
        $user = $this->repository->findOneBy(['username' => $username]);

        if (! $user) {
            return $this->notFoundAction();
        }

        $friends = $this->getUserFriends($this->entityManager, $user);

        // request information for identified user  ///////////////////////////

        $pendingAnswerIdentityUser = $this->entityManager
                              ->getRepository(Relationship::class)
                              ->pendingAnswer($this->identity()->getId(), $user->getId());

        // request information for not identified user  ///////////////////////

        $pendingAnswerUser = $this->entityManager
                                  ->getRepository(Relationship::class)
                                  ->pendingAnswer($user->getId(), $this->identity()->getId());

        ///////////////////////////////////////////////////////////////////////

        $isFriends = $this->entityManager
                              ->getRepository(Relationship::class)
                              ->checkingFriendship($this->identity()->getId(), $user->getId());

        ///////////////////////////////////////////////////////////////////////

        return new ViewModel([
            'user'          => $user,
            'friends'       => $friends,
            'pendingAnswerIdentityUser' => $pendingAnswerIdentityUser,
            'pendingAnswerUser' => $pendingAnswerUser,
            'isFriends'     => $isFriends,
        ]);
    }

    public function editAction()
    {
        $username = $this->params()->fromRoute('username');
        $username = $this->clearString($username);
        $user = $this->repository->findOneBy(['username' => $username]);

        if (! $user || ! $this->isUserActive($username)) {
            return $this->notFoundAction();
        }

        $form = $this->updateForm;

        $form->setHydrator(new DoctrineObject($this->entityManager));
        $form->bind($user);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $files = $request->getFiles()->toArray();
            if ($files) { $fileName = $files['file']['name']; }

            $form->setData($data);

            // Remove 'role' from validationGroup (We do not need 'role' in this form)
            $form->getInputFilter()->remove('role');

            // Remove 'active' from validationGroup (We do not need 'active' in this form)
            $form->getInputFilter()->remove('active');

            // Remove 'password' from validationGroup if we do not want to change password
            if ($form->get("password")->getValue() == ""){
                $form->getInputFilter()->remove('password');
            }

            if ($form->isValid()) {
                $user = $form->getData();

                /* Actions with image */
                if ($fileName) {
                    $oldImage = $user->getImage();
                    if (is_file(getcwd() . '/public_html' . $oldImage)) {
                        unlink(getcwd() . '/public_html' . $oldImage);
                    }

                    /*
                        Here very important consider order
                        ( first use \Zend\Filter\File\Rename, then \Zend\Filter\File\RenameUpload )
                        In order to give $username to filename use this two classes here instead of
                        Authentication\Filter\UpdateFilter where not exists $username
                    */

                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = $username . '.' . $extension;

                    $filter = new \Zend\Filter\File\Rename("./public_html/img/user/" . $newFileName);
                    $filter->filter($files['file']);

                    $filter = new \Zend\Filter\File\RenameUpload([
                        'target'            => './public_html/img/user/',
                        'useUploadName'     => true,
                        'useUploadExtension'=> true,
                        'overwrite'         => true,
                        'randomize'         => false
                    ]);

                    $filter->filter($files['file']);

                    $user->setImage('/img/user/' . $newFileName);
                }
                /* End actions */

                /* In order, to not work, when an empty password  */
                $postArray = $request->getPost()->toArray();

                // more than 2 characters allowed
                if (strlen($user->getPassword()) >= 2) {
                    $bcrypt = new Bcrypt();
                    $hash = $bcrypt->create($user->getPassword());
                    $user->setPassword($hash);
                }
                /* End block */

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->flashMessenger()->addSuccessMessage('Profile edited');
                $this->redirect()->toRoute('profile', ['username' => $user->getUsername()]);
            }
        }

        return new ViewModel([
            'form' => $form,
            'user' => $user,
        ]);
    }

    public function deleteAction()
    {
        $username = $this->params()->fromRoute('username', '');
        $username = $this->clearString($username);
        $user = $this->repository->findOneBy(['username' => $username]);
        if (! $user) {
            return $this->notFoundAction();
        }

        $user->setActive('0');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->forward()->dispatch(LogoutController::class, ['action' => 'index']);
    }
}
