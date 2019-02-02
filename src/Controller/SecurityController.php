<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\NewUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        dump($error);
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/signup", name="app_signup")
     */
    public function signup(Request $request) : Response
    {
        $user = new User();
        $formBuilder = $this->createForm(NewUserFormType::class, $user);

        $formBuilder->handleRequest($request);
        if ($formBuilder->isSubmitted() && $formBuilder->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            // Handle error here : Duplicated username

            $encoded = password_hash($user->getPassword(), PASSWORD_ARGON2I);
            $user->setPassword($encoded);

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->render('security/signup.html.twig', ['isValidate' => 'true', 'username' => $user->getUsername()]);
        }

        return $this->render('security/signup.html.twig', ['form' => $formBuilder->createView(), 'isValidate' => 'false']);
    }
}
