<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{id}", requirements={"id"="\d+"}, name="article")
     */
    public function show($id): Response
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->findOneById($id);

        return $this->render('article.html.twig', ['article' => $article]);
    }

    /**
     * @Route("articles/{name}", name="personal_articles")
     */
    public function showArticles($name) : Response
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepository->findBy(['auteur' => $name]);

        return $this->render('index.html.twig', ['articles' => $articles, 'personal' => true]);
    }
}