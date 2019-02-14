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

class EditControllerTest extends WebTestCase
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

    public function testGetEditLogIn()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/edit/' . self::$idArticle);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Modifierl\'article', preg_replace('/\s+/', '', ($crawler->filter('h2.my-4')->text())));
    }

    public function testEditFormLogIn()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/edit/' . self::$idArticle);

        $buttonCrawlerNode = $crawler->selectButton('new_article_form_enregistrer');
        $form = $buttonCrawlerNode->form([
            'new_article_form[titre]' => 'TestEdit',
            'new_article_form[contenu]' => 'ContenuTestEdit'
        ]);
        $crawler = $this->client->submit($form);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        $this->assertSame('TestEdit', preg_replace('/\s+/', '', ($crawler->filter('h3.card-title')->text())));
        $this->assertSame('ContenuTestEdit', preg_replace('/\s+/', '', ($crawler->filter('p.card-text')->text())));



    }


    public function testGetEditLogout()
    {
        $crawler = $this->client->request('GET', '/edit/' . self::$idArticle);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame('Formulaire d\'authentification', trim($crawler->filter('h2.my-4')->text()));

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