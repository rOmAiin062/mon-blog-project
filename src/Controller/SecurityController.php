<?php

namespace App\Controller;


use App\Entity\Article;
use App\Entity\User;
use App\Entity\ChangePassword;
use App\Form\ChangePasswordFormType;
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

            // Handle same username exception
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
     * @Route("/changepasswd", name="change_passwd")
     */
    public function changepassword(Request $request)
    {
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordFormType::class, $changePasswordModel);

        $currentUser = $this->getUser();

        $articleRepo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $articleRepo->findBy(['auteur' => $currentUser->getUsername()]);
        if ($articles != null)
            $nb_article = count($articles);
        else
            $nb_article = 0;


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $encoded = password_hash($changePasswordModel->getNewPassword(), PASSWORD_ARGON2I);
            $currentUser->setPassword($encoded);

            $entityManager->persist($currentUser);

            $entityManager->flush();

            return $this->render('user/index.html.twig', ['info' => 'Votre mot de passe à été mis à jour',
                'user' => $currentUser, 'nb_article' => $nb_article]);
        }

        return $this->render('security/changepasswd.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(){

    }
}
