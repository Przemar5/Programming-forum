<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\Topic;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Services\DatabaseSearcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ChrisKonnertz\BBCode\BBCode;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(?Request $request, CategoryRepository $categoryRepo, PostRepository $postRepo, TagRepository $tagRepo, TopicRepository $topicRepo, UserRepository $userRepo): Response
    {
    	$phrase = $request->query->get('phrase');
        $phrase = filter_var($phrase, FILTER_SANITIZE_URL);

        $resources = [
            [Category::class,   'categories',   ['name'],       null],
            [Post::class,       'posts',        ['content'],    true],
            [Tag::class,        'tags',         ['name'],       null],
            [Topic::class,      'topics',       ['name'],       true],
            [User::class,       'users',        ['login'],      true],
        ];
        $dbSearch = new DatabaseSearcher($this->getDoctrine());
        $results = $dbSearch->search($phrase, $resources)->getResults();

        return $this->render('search/index.html.twig', [
            'phrase' => $phrase,
            'results' => $results,
            'bbcode' => new BBCode(),
        ]);
    }
}
