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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EditController extends AbstractController
{
    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"})
     */
    public function edit($id, Request $request): Response
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->findOneById($id);

        $formBuilder = $this->createForm(NewArticleFormType::class, $article);

        $formBuilder->handleRequest($request);
        if ($formBuilder->isSubmitted() && $formBuilder->isValid()) {
            $data = $formBuilder->getData();

            $article->setTitre($data->getTitre());
            $article->setContenu($data->getContenu());
            $article->setAuteur($data->getAuteur());
            //$article->setDate(\DateTime::createFromFormat('d/m/Y H:i:s', date("d/m/Y H:i:s")));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article', array('id' => $id));
        }



        return $this->render('creer.html.twig', ['form' => $formBuilder->createView(), 'isValidate' => 'false']);
    }
}