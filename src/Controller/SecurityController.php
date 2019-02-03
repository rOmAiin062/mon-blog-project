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

            // Handle same usernamae exception
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            $userCheck = $userRepo->findOneBy(['username' => $user->getUsername()]);
            if ($userCheck)
                return $this->render('security/signup.html.twig',
                    ['form' => $formBuilder->createView(), 'isValidate' => 'false', 'error' => 'The username is already used.']);

            $entityManager = $this->getDoctrine()->getManager();

            // Encrypt password
            $encoded = password_hash($user->getPassword(), PASSWORD_ARGON2I);
            $user->setPassword($encoded);

            // Set user role
            $user->setRoles(array('ROLE_USER'));

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->render('security/signup.html.twig', ['isValidate' => 'true', 'username' => $user->getUsername(), 'error' => '']);
        }

        return $this->render('security/signup.html.twig', ['form' => $formBuilder->createView(), 'isValidate' => 'false', 'error' => '']);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(){

    }
}
