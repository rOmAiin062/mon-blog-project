<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */

namespace App\Controller;

use App\Entity\Article;
use App\Form\NewArticleFormType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NewController extends AbstractController
{
    /**
     * @Route("/new")
     */
    public function new(Request $request): Response
    {

        $article = new Article();
        $ifUser = false;
        $currentUser = $this->getUser();

        if ($currentUser) {
            $article->setAuteur($currentUser->getUsername());
            $ifUser = true;
        }
        $formBuilder = $this->createForm(NewArticleFormType::class, $article, ['ifUser' => $ifUser]);
        $formBuilder->handleRequest($request);

        if ($formBuilder->isSubmitted() && $formBuilder->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $date = date("d/m/Y H:i:s");
            $article->setDate(\DateTime::createFromFormat('d/m/Y H:i:s', $date));

            $entityManager->persist($article);

            $entityManager->flush();

            return $this->render('creer.html.twig', ['isValidate' => 'true', 'id' => $article->getId()]);

        }

        return $this->render('creer.html.twig', ['form' => $formBuilder->createView(), 'isValidate' => 'false', 'createNew' => 'true']);
    }
}