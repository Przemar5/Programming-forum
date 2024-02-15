<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Rating;
use App\Form\PostEditType;
use App\Repository\PostRepository;
use App\Repository\RatingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use ChrisKonnertz\BBCode\BBCode;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        $bbcode = new BBCode();

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'bbcode' => $bbcode,
        ]);
    }

    /**
     * @Route("/create", name="post_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAccepted(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Post was created successfully.');

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, PostRepository $postRepo, Security $security): Response
    {
        $post = $postRepo->findOneBy([
            'id' => $id,
        ]);

        $oldUser = $post->getUser();
        $user = $security->getUser();

        if (!$post || $post->getDeletedAt()) {
            throw $this->createNotFoundException();
        }

        if (($post->getUser() !== $user || $post->getAccepted() === false) && 
            !$user->hasRole('ROLE_ADMIN')) {
            
            throw $this->createAccessDeniedException();
        }

        if ($post->getContentToAccept() === null) {
            $post->setContentToAccept($post->getContent());
        }

        $form = $this->createForm(PostEditType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !empty($user) && 
            (!$post->getTopic()->getClosed() || $user->hasRole('ROLE_ADMIN'))) {

            if ($user->hasRole('ROLE_ADMIN')) {
                $post->setEditAccepted(true);
                $post->setContent($post->getContentToAccept());
                $post->setContentToAccept(null);
                $post->setUser($oldUser);
            
            } else {
                $post->setEditAccepted(false);
            }

            $post->setUpdatedAt();

            $this->getDoctrine()->getManager()->flush();

            $message = ($user->hasRole('ROLE_ADMIN')) 
                    ? 'Post was updated successfully.' 
                    : 'Your post was updated and is waiting for approval.';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('topic_show', [
                'id' => $post->getTopic()->getId(),
            ]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request, PostRepository $postRepo, Security $security): Response
    {
        $post = $postRepo->findOneBy([
            'id' => $id,
            'accepted' => true,
        ]);
        
        if (!$post || $post->getDeletedAt()) {
            throw $this->createNotFoundException();
        }
        
        if ($post->getUser() !== $security->getUser() && !$security->getUser()->hasRole('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $topic = $post->getTopic();
        $user = $security->getUser();

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token')) && 
            !empty($user) && (!$topic->getClosed() || $user->hasRole('ROLE_ADMIN'))) {

            $post->setDeletedAt();

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Post was deleted successfully.');
        }

        return $this->redirectToRoute('topic_show', [
            'id' => $topic->getId(),
        ]);
    }

    /**
     * @Route("/{id}/rate", name="post_rate", methods={"POST"})
     */
    public function rate($id, Request $request, PostRepository $postRepo, RatingRepository $ratingRepo, Security $security)
    {
        if (!$this->isCsrfTokenValid('rate'.$id, $request->get('_token'))) {
            return $this->redirect($request->headers->get('referer'));
        }
     
        $post = $postRepo->findOneBy([
            'id' => $id,
            'accepted' => true,
            'deleted_at' => null,
        ]);

        if (!$post) {
            return $this->redirect($request->headers->get('referer'));
        }

        if ($post->getDeletedAt()) {
            return $this->redirect($request->headers->get('referer'));
        }
            
        if ($request->get('type') == 1) {
            $points = 1;

        } elseif ($request->get('type') == -1) {
            $points = -1;
        }

        $user = $security->getUser();

        $rating = $ratingRepo->findOneBy([
            'user' => $user,
            'post' => $post,
        ]);

        if ($rating) {

            if ($rating->getPoints() === $points) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($rating);
                $entityManager->flush();
            
            } else {
                $rating->setPoints($points);
                $entityManager = $this->getDoctrine()->getManager()->flush();
            }
        
        } else {
            $rating = new Rating();
            $rating->setUser($user);
            $rating->setPost($post);
            $rating->setPoints($points);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();
        }

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {
            return $this->json([
                'liked' => $post->isLikedBy($user),
                'disliked' => $post->isDislikedBy($user),
                'points' => $post->getPoints(),
                'userId' => $post->getUser()->getId(),
                'userPoints' => $post->getUser()->getPoints(),
            ]);

        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * @Route("/{id}/accept", name="post_accept", methods={"POST"})
     */
    public function accept($id, Request $request, PostRepository $postRepo, Security $security)
    {
        if (!$this->isCsrfTokenValid('accept'.$id, $request->get('_token'))) {
            return $this->redirect($request->headers->get('referer'));
        }

        $post = $postRepo->find($id);

        if (!$post) {
            return $this->redirect($request->headers->get('referer'));
        }

        if ($post->getAccepted() === false) {
            $post->setAccepted(true);

        } elseif ($post->getEditAccepted() === false) {
            $post->setEditAccepted(true);
            $post->setContent($post->getContentToAccept());
            $post->setContentToAccept(null);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Post was approved.');

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/{id}/remove", name="post_remove", methods={"DELETE"})
     */
    public function remove($id, Request $request, PostRepository $postRepo)
    {
        if (!$this->isCsrfTokenValid('remove'.$id, $request->get('_token'))) {
            return $this->redirect($request->headers->get('referer'));
        }

        $post = $postRepo->find($id);

        if (!$post) {
            return $this->redirect($request->headers->get('referer'));
        }

        $this->getDoctrine()->getManager()->remove($post)->flush();

        return $this->redirectToRoute('dashboard');
    }
}
