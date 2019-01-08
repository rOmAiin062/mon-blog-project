<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */


namespace App\Tests\Controller;

use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewControllerTest extends WebTestCase
{
    public function newTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/new');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function createNewArticleTest()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new');

        $buttonCrawlerNode = $crawler->selectButton('submit');

        $form = $buttonCrawlerNode->form(array(
            'new_article_form[titre]' => 'Article de test',
            'new_article_form[contenu]' => 'Ceci est un fake article... Fake news !',
            'new_article_form[auteur]' => 'Romain'
        ));

        $crawler = $client->submit($form);
    }

}