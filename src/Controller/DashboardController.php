<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use ChrisKonnertz\BBCode\BBCode;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(PostRepository $postRepo, TopicRepository $topicRepo)
    {
        return $this->render('dashboard/index.html.twig', [
            'postsToAccept' => $postRepo->postsToAccept(),
            'topicsToAccept' => $topicRepo->topicsToAccept(),
            'bbcode' => new BBCode(),
        ]);
    }
}
