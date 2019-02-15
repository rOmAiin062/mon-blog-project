<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */


namespace App\Tests\Controller;

use App\Entity\User;
use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class NewControllerTest extends WebTestCase
{
    private $client = null;
    private $getDoctrine = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->getDoctrine = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testGetNewWithUnautenticatedUser()
    {
        $this->client->request('GET', '/new');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $title = $crawler->filter('h2.my-4')->text();
        $this->assertSame('Formulaire d\'authentification', trim($title));
    }

    public function testGetNewWithAuthenticatedUser()
    {
        $this->createFakeUser('fake1');
        $this->logIn('fake1');
        $crawler = $this->client->request('GET', '/new');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $title = $crawler->filter('h2.my-4')->text();
        $this->assertSame('Créer un nouvel article', trim($title));
        $this->removeFakeUser('fake1');
    }

    public function testCreateNewArticleTest()
    {
        $this->createFakeUser('fake2');
        $this->logIn('fake2');
        $crawler = $this->client->request('GET', '/new');

        $form = $crawler->selectButton('new_article_form_enregistrer')->form(
            [
                'new_article_form[titre]' => 'Article de test',
                'new_article_form[contenu]' => 'Ceci est un fake article... Fake news !',
                'new_article_form[auteur]' => 'test'
            ]);


        $this->client->submit($form);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $newArticle = $this->getDoctrine->getRepository('App:Article')->findOneByAuteur('test');
        $this->assertNotNull($newArticle);
        $this->assertSame('test', $newArticle->getAuteur());
        $this->removeFakeUser('fake2');
    }

    private function logIn(string $username)
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

    private function createFakeUser(string $fake){
        $user = new User();
        $user->setUsername($fake);
        $user->setPassword($fake . 'password');
        $user->setRoles(array('ROLE_USER'));
        $this->getDoctrine->persist($user);
        $this->getDoctrine->flush();
    }

    public function removeFakeUser(string $fake){
        $toto = $this->getDoctrine->getRepository('App:User')->findOneByUsername($fake);
        $this->getDoctrine->remove($toto);
        $this->getDoctrine->flush();
    }
}