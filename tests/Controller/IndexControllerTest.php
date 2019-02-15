<?php
/**
 * Created by IntelliJ IDEA.
 * User: romain
 * Date: 08/01/19
 * Time: 17:35
 */

namespace App\Tests\Controller;

use  \Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends AbstractSetupClass
{

    /** @test */
    public function testIndexPage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}