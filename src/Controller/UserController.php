<?php

namespace App\Controller;

use App\Entity\Article;
use function PHPSTORM_META\elementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        $currentUser = $this->getUser();
        $nb_article = 0;

        if ($currentUser) {
            $articleRepo = $this->getDoctrine()->getRepository(Article::class);
            $articles = $articleRepo->findBy(['auteur' => $currentUser->getUsername()]);
            if ($articles != null)
                $nb_article = count($articles);
        }

        return $this->render('user/index.html.twig', [
            'user' => $currentUser, 'nb_article' => $nb_article, 'info' => '']);
    }
}
