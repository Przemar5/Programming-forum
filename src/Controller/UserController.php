<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private const AVATARS_DIRECTORY = '/public/images/avatars/';
    private const DEFAULT_AVATAR = 'no_avatar.png';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->allNotDeleted(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show($id, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy([
            'id' => $id,
            'deleted_at' => null,
        ]);
        
        if (!$user) {
            throw $this->createNotFoundException();
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, UserRepository $userRepo, Security $security, FileUploader $fileUploader): Response
    {
        $user = $userRepo->findOneBy([
            'id' => $id,
            'deleted_at' => null,
        ]);
        
        if (!$user) {
            throw $this->createNotFoundException();
        }

        if ($user !== $security->getUser() && !$security->getUser()->hasRole('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->beginTransaction();

            $oldAvatarFilename = $user->getAvatar();
            $newAvatarFilename = null;

            try {
                $file = $request->files->get('user')['avatar'];
                if ($file) {
                    $newAvatarFilename = $fileUploader->generateFilename($file);
                    $user->setAvatar($newAvatarFilename);

                    $this->getDoctrine()->getManager()->flush();

                    $fileUploader->uploadFile($file, $newAvatarFilename);

                    if ($oldAvatarFilename !== self::DEFAULT_AVATAR) {
                        $fileUploader->deleteFile(self::AVATARS_DIRECTORY . $oldAvatarFilename);
                    }

                } else {
                    $this->getDoctrine()->getManager()->flush();
                }

                $this->entityManager->commit();

            } catch (\Exception $e) {
                if ($newAvatarFilename) {
                    $fileUploader->deleteFile(self::AVATARS_DIRECTORY . $newAvatarFilename);
                }
                $this->entityManager->rollBack();

                throw $e;
            }

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit-password", name="user_edit_password", methods={"GET","POST"})
     */
    public function editPassword($id, Request $request, UserRepository $userRepo, UserPasswordEncoderInterface $encoder, Security $security): Response
    {
        $user = $userRepo->findOneBy([
            'id' => $id,
            'deleted_at' => null,
        ]);
        
        if (!$user) {
            throw $this->createNotFoundException();
        }

        if ($user !== $security->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setPassword($encoder->encodePassword($user, $plainPassword));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request, UserRepository $userRepo, Security $security, FileUploader $fileUploader, TokenStorageInterface $tokenStorage): Response
    {
        $user = $userRepo->findOneBy([
            'id' => $id,
            'deleted_at' => null,
        ]);
        
        if (!$user) {
            throw $this->createNotFoundException();
        }
        
        if ($user !== $security->getUser() && !$security->getUser()->hasRole('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {

            if ($user->getAvatar() !== self::DEFAULT_AVATAR) {
                $fileUploader->deleteFile(self::AVATARS_DIRECTORY . $user->getAvatar(), true);
            }
            $user->setAvatar(self::DEFAULT_AVATAR);
            $user->setLogin($user->getLogin().'_deleted_'.time());
            $user->setEmail($user->getEmail().'_deleted_'.time());
            $user->setDeletedAt();

            $this->getDoctrine()->getManager()->flush();

            $tokenStorage->setToken();
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/{id}/ban", name="user_ban", methods={"POST"})
     */
    public function ban(User $user)
    {
        
    }
}
