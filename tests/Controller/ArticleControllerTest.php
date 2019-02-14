<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 02/01/19
 * Time: 16:50
 */


namespace App\Tests\Controller;

use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    /** @test */
    public function showTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/article/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}