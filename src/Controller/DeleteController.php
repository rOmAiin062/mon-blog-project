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

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($article);
        $entityManager->flush();

        return new JsonResponse(
            $this->container->get('router')->getContext()->getBaseUrl(),
            JsonResponse::HTTP_NO_CONTENT
        );
    }
}