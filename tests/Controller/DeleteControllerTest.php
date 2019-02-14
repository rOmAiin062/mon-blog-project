<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 03/01/19
 * Time: 12:52
 */


namespace App\Tests\Controller;

use App\Entity\Article;
use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class DeleteControllerTest extends WebTestCase
{

    private $client = null;
    private $getDoctrine = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->getDoctrine = $this->client->getContainer()->get('doctrine')->getManager();


    }

    public function testDeleteWithNoLogIn()
    {
        $crawler = $this->client->request('DELETE', '/article/3');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Article 3', trim($crawler->filter('h2.my-4')->text()));
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $user = $this->getDoctrine->getRepository('App:User')->findOneByUsername('test');


        $token = new PostAuthenticationGuardToken($user, $firewallName, ['ROLE_USER']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}