<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 03/01/19
 * Time: 12:52
 */

namespace App\Controller;


use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteController extends AbstractController
{
    /**
     * @Route(
     *     "/delete/{id}",
     *     name="delete_article",
     *     methods={"DELETE"},
     *     requirements={"id"="\d+"}
     * )
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete($id)
    {
        $articleRepository = $this->getDoctrine()->getRepository(Article::class);
        $article = $articleRepository->findOneById($id);
        $ifUser = false;
        $currentUser = $this->getUser();

        if (!$currentUser || $article->getAuteur() != $currentUser->getUsername()) {
            return new JsonResponse(null, 403);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($article);
        $entityManager->flush();

        return new JsonResponse(
            $this->container->get('router')->getContext()->getBaseUrl(),
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}