<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */


namespace App\Tests\Controller;

use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class ArticleControllerTest extends AbstractSetupClass
{

    public function testGetArticleLogin()
    {
        $this->createFakeUser('user001');
        $this->createFakeArticle('user001', 'user001 first article');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('user001 first article');


        $this->logIn('user001');
        $crawler = $this->client->request('GET', '/article/' . $article->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Article'.$article->getId().'ModifierSupprimer', preg_replace('/\s+/', '', ($crawler->filter('h2.my-4')->text())));

        $this->removeFakeUser('user001');
        $this->removeFakeArticle($article->getId());
    }

    public function testGetArticleLogout()
    {
        $this->createFakeUser('user002');
        $this->createFakeArticle('user001', 'user002 first article');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('user002 first article');

        $crawler = $this->client->request('GET', '/article/' . $article->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Article ' . $article->getId(), trim($crawler->filter('h2.my-4')->text()));

        $this->removeFakeUser('user002');
        $this->removeFakeArticle($article->getId());

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