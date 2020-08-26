<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryRepository $categoryRepo, UserRepository $userRepo)
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepo->rootCategories(),
        ]);
    }

    /**
     * @Route("/categories/{slug}", name="home_category")
     */
    public function byCategory($slug, CategoryRepository $categoryRepo)
    {
        $category = $categoryRepo->findOneBy([
            'slug' => $slug,
        ]);

        return $this->render('home/index.html.twig', [
            'category' => $category,
            'categories' => $category->getSubcategories(),
        ]);
    }
}
