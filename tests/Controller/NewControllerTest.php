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

class NewControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testSecuredHello()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/new');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Créer un nouvel article', trim($crawler->filter('h2.my-4')->text()));
    }

//    /** @test */
//    public function createNewArticleTest()
//    {
//
//        $this->logIn();
//        $crawler = $this->client->request('GET', '/new');
//
//        $buttonCrawlerNode = $crawler->selectButton('submit');
//
//        $form = $buttonCrawlerNode->form(
//            ['new_article_form[titre]' => 'Article de test'],
//            ['new_article_form[contenu]' => 'Ceci est un fake article... Fake news !'],
//            ['new_article_form[auteur]' => 'Romain']
//        );
//
//        $crawler = $this->client->submit($form);
//        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
//    }


    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $getDoctrine = $this->client->getContainer()->get('doctrine')->getManager();
        $user = $getDoctrine->getRepository('App:User')->findOneByUsername('test');


        $token = new PostAuthenticationGuardToken($user, $firewallName, ['ROLE_USER']);
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}