<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 15/02/19
 * Time: 12:04
 */

namespace App\Tests\Controller;


use App\Entity\Article;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractSetupClass extends WebTestCase
{
    protected $client = null;
    protected $getDoctrine = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->getDoctrine = $this->client->getContainer()->get('doctrine')->getManager();

    }

    protected function createFakeArticle($fakeAuteur, $fakeTitre){
        $article = new Article();
        $article->setTitre($fakeTitre);
        $article->setContenu('fakeContenu');
        $article->setDate(new DateTime('2019-01-01'));
        $article->setAuteur($fakeAuteur);
        $this->getDoctrine->persist($article);
        $this->getDoctrine->flush();
    }

    protected function createFakeUser($fake){
        $user = new User();
        $user->setUsername($fake);
        $user->setPassword($fake . 'password');
        $user->setRoles(array('ROLE_USER'));
        $this->getDoctrine->persist($user);
        $this->getDoctrine->flush();
    }

    protected function removeFakeUser($username){
        $user = $this->getDoctrine->getRepository('App:User')->findOneByUsername($username);
        $this->getDoctrine->remove($user);
        $this->getDoctrine->flush();
    }

    protected function removeFakeArticle($idArticle)
    {

            $article = $this->getDoctrine->getRepository('App:Article')->findOneById($idArticle);
            $this->getDoctrine->remove($article);
            $this->getDoctrine->flush();

    }
}