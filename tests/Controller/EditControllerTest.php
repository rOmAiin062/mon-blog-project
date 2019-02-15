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

class EditControllerTest extends AbstractSetupClass
{


    public function testGetEditLogIn()
    {
        $this->createFakeUser('user003');
        $this->createFakeArticle('user003', 'user003 first article');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('user003 first article');


        $this->logIn('user003');
        $crawler = $this->client->request('GET', '/edit/' . $article->getId());

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame('Modifierl\'article', preg_replace('/\s+/', '', ($crawler->filter('h2.my-4')->text())));

        $this->removeFakeUser('user003');
        $this->removeFakeArticle($article->getId());
    }

    public function testEditFormLogIn()
    {
        $this->createFakeUser('user004');
        $this->createFakeArticle('user004', 'user004 first article');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('user004 first article');


        $this->logIn('user004');
        $crawler = $this->client->request('GET', '/edit/' . $article->getId());

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

        $this->removeFakeUser('user004');
        $this->removeFakeArticle($article->getId());

    }


    public function testGetEditLogout()
    {
        $this->createFakeUser('user005');
        $this->createFakeArticle('user005', 'user005 first article');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('user005 first article');

        $crawler = $this->client->request('GET', '/edit/' . $article->getId());

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame('Formulaire d\'authentification', trim($crawler->filter('h2.my-4')->text()));


        $this->removeFakeUser('user005');
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