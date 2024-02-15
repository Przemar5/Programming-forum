<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\Tag;
use App\Form\PostCreateType;
use App\Form\TopicType;
use App\Form\TopicPostCreateType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\TopicRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use ChrisKonnertz\BBCode\BBCode;

/**
 * @Route("/topic")
 */
class TopicController extends AbstractController
{
    private const POSTS_PER_PAGE = 10;
    private const TOPICS_PER_PAGE = 20;

    /**
     * @Route("/", name="topic_index", methods={"GET"})
     */
    public function index(Request $request, TopicRepository $topicRepo, PaginatorInterface $paginator): Response
    {
        $page = $request->query->get('page') ?? 1;
        $topics = $topicRepo->findAll();
        $topics = $paginator->paginate($topics, $page, self::TOPICS_PER_PAGE);

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    /**
     * @Route("/create", name="topic_create", methods={"GET","POST"})
     */
    public function create(Request $request, CategoryRepository $categoryRepo, TagRepository $tagRepo, Security $security): Response
    {
        $topic = new Topic();
        $post = new Post();
        $form = $this->createForm(TopicPostCreateType::class, [
            'topic' => $topic,
            'post' => $post,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();

            $topic = $form->getData()['topic'];
            $topic->setCreatedAt();
            $topic->setUser($user);
            $topic->setAccepted($user->hasRole('ROLE_ADMIN'));
            
            $post = $form->getData()['post'];
            $post->setCreatedAt();
            $post->setTopic($topic);
            $post->setUser($user);
            $post->setAccepted($user->hasRole('ROLE_ADMIN'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($topic);
            $entityManager->persist($post);
            $entityManager->flush();
            
            $message = ($user->hasRole('ROLE_ADMIN')) 
                    ? 'New Topic was updated successfully.' 
                    : 'New topic was created and is waiting for approval.';
            $this->addFlash('success', $message);

            return $this->redirectToRoute('home');
        }

        return $this->render('topic/new.html.twig', [
            'categories' => $categoryRepo->findAll(),
            'tags' => $tagRepo->findAll(),
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_show", methods={"GET", "POST"})
     */
    public function show(
        $id, 
        Request $request, 
        TopicRepository $topicRepo, 
        Security $security, 
        PaginatorInterface $paginator
    ): Response {
        $topic = $topicRepo->findOneBy([
            'id' => $id,
            'accepted' => true,
        ]);
        
        if (!$topic) {
            throw $this->createNotFoundException();
        }

        $user = $security->getUser();

        $page = $request->query->get('page') ?? 1;
        $post = new Post();

        $form = $this->createForm(PostCreateType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !empty($user) && 
            (!$topic->getClosed() || $user->hasRole('ROLE_ADMIN'))) {

            $post->setAccepted($user->hasRole('ROLE_ADMIN'));
            $post->setCreatedAt();
            $post->setTopic($topic);
            $post->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'Your post have been submitted and is waiting for acceptance.');
        }
        
        $posts = $topic->getPosts();
        $posts = $paginator->paginate($posts, $page, self::POSTS_PER_PAGE);

        $request = null;

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'bbcode' => new BBCode(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="topic_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, TopicRepository $topicRepo): Response
    {
        $topic = $topicRepo->findOneBy([
            'id' => $id,
            'accepted' => true,
        ]);
        
        if (!$topic) {
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('topic_show', [
                'id' => $topic->getId(),
            ]);
        }

        return $this->render('topic/edit.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="topic_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request, TopicRepository $topicRepo): Response
    {
        $topic = $topicRepo->findOneBy([
            'id' => $id,
            'accepted' => true,
        ]);
        
        if (!$topic) {
            throw $this->createNotFoundException();
        }
        
        if ($this->isCsrfTokenValid('delete'.$topic->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index');
    }

    /**
     * @Route("/{id}/accept", name="topic_accept", methods={"POST"})
     */
    public function accept($id, Request $request, TopicRepository $topicRepo, Security $security)
    {
        if (!$this->isCsrfTokenValid('accept'.$id, $request->get('_token'))) {
            return $this->redirect($request->headers->get('referer'));
        }
     
        $topic = $topicRepo->find($id);

        if (!$topic) {
            return $this->redirect($request->headers->get('referer'));
        }

        if ($topic->getAccepted() === false) {
            $topic->setAccepted(true);

        } elseif ($topic->getEditAccepted() === false) {
            $topic->setEditAccepted(true);
            $topic->setContent($topic->getContentToAccept());
            $topic->setContentToAccept(null);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/{id}/remove", name="topic_remove", methods={"DELETE"})
     */
    public function remove($id, Request $request, TopicRepository $topicRepo)
    {
        if (!$this->isCsrfTokenValid('remove'.$id, $request->get('_token'))) {
            return $this->redirect($request->headers->get('referer'));
        }

        $topic = $topicRepo->find($id);

        if (!$topic) {
            return $this->redirect($request->headers->get('referer'));
        }

        $this->getDoctrine()->getManager()->remove($topic)->flush();

        return $this->redirectToRoute('dashboard');
    }
}
