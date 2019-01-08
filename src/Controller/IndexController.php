<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {

        $articleRepository = $this->getDoctrine()->getRepository(Article::class);

        $articles = $articleRepository->findAll();


        return $this->render('index.html.twig', ['articles' => $articles]);
    }
}