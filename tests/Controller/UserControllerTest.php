<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 15/02/19
 * Time: 10:29
 */

namespace App\Tests\Controller;


use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class UserControllerTest extends AbstractSetupClass
{
    public function testGetUserPageLogIn()
    {
        $this->createFakeUser('user006');
        $this->createFakeArticle('user006', 'user006 first article');

        $this->logIn('user006');
        $crawler = $this->client->request('GET', '/user');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('user006', preg_replace('/\s+/', '', ($crawler->filter('h2')->text())));

        $this->removeFakeUser('user006');
    }

    public function testGetUserPageLogout()
    {

        $this->createFakeUser('user007');

        $crawler = $this->client->request('GET', '/user');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame('Formulaire d\'authentification', trim($crawler->filter('h2.my-4')->text()));

        $this->removeFakeUser('user007');
    }




    public function testGetChangePasswordLogin()
    {
        $this->createFakeUser('user008');

        $this->logIn('user008');
        $crawler = $this->client->request('GET', '/changepasswd');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Modifiervotremotdepasse', preg_replace('/\s+/', '', ($crawler->filter('h2.my-4')->text())));

        $this->removeFakeUser('user008');
    }

    public function testGetChangePasswordLogout()
    {
        $crawler = $this->client->request('GET', '/changepasswd');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame('Formulaire d\'authentification', trim($crawler->filter('h2.my-4')->text()));
    }

    private function logIn($username)
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $user = $this->getDoctrine->getRepository('App:User')->findOneByUsername($username);


        $token = new PostAuthenticationGuardToken($user, $firewallName, ['ROLE_USER']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}