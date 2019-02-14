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

class ArticleControllerTest extends WebTestCase
{
    private $client = null;
    private $getDoctrine = null;
    private static $idArticle = 1;
    private static $username = 'test';
    public function setUp()
    {
        $this->client = static::createClient();
        $this->getDoctrine = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetArticleLogin()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/article/' . self::$idArticle);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Article'.self::$idArticle.'ModifierSupprimer', preg_replace('/\s+/', '', ($crawler->filter('h2.my-4')->text())));
    }

    public function testGetArticleLogout()
    {
        $crawler = $this->client->request('GET', '/article/' . self::$idArticle);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Article ' . self::$idArticle, trim($crawler->filter('h2.my-4')->text()));
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $user = $this->getDoctrine->getRepository('App:User')->findOneByUsername(self::$username);


        $token = new PostAuthenticationGuardToken($user, $firewallName, ['ROLE_USER']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}