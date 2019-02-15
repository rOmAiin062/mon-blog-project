<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 03/01/19
 * Time: 12:52
 */


namespace App\Tests\Controller;

use App\Entity\Article;
use App\Entity\User;
use DateTime;
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

    public function testDeleteWithUnauthenticatedUser()
    {
        $crawler = $this->client->request('DELETE', '/delete/1');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $title = $crawler->filter('h2.my-4')->text();
        $this->assertSame('Formulaire d\'authentification', trim($title));
    }

    public function testDeleteWithAuthenticatedUser()
    {
        $this->createFakeUser('toto');
        $this->logIn('toto');

        $this->createFakeArticle('toto', 'testDeleteWithAuthenticatedUser');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('testDeleteWithAuthenticatedUser');
        $crawler = $this->client->request('DELETE', '/delete/'.$article->getId());

        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->assertNull($this->getDoctrine->getRepository('App:Article')->findOneByTitre(['titre' => 'testDeleteWithAuthenticatedUser']));
        $this->removeFakeUser('toto');
    }

    public function testDeleteWithNotAllowedUser()
    {
        $this->createFakeUser('john');
        $this->createFakeUser('doe');
        $this->logIn('john');

        $this->createFakeArticle('john', 'testDeleteWithNotAllowedUser');
        $this->createFakeArticle('doe', 'DoeArticletestDeleteWithNotAllowedUser');
        $article = $this->getDoctrine->getRepository('App:Article')->findOneByTitre('DoeArticletestDeleteWithNotAllowedUser');
        $crawler = $this->client->request('DELETE', '/delete/'.$article->getId());

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        $this->removeFakeUser('john');
        $this->removeFakeUser('doe');

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

    private function createFakeArticle(string $fakeAuteur, string $fakeTitre){
        $article = new Article();
        $article->setTitre($fakeTitre);
        $article->setContenu('fakeContenu');
        $article->setDate(new DateTime('2019-01-01'));
        $article->setAuteur($fakeAuteur);
        $this->getDoctrine->persist($article);
        $this->getDoctrine->flush();
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