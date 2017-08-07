<?php
namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ArticleController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testCalcTVA1()
    {
        $controller = new ArticleController();
        $number = 1.0;
        $result = $controller->calcTVA1($number);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(1.17, $result);
    }

    public function testCalcTVA2()
    {
        $controller = new ArticleController();
        $number = 1.0;
        $result = $controller->calcTVA2($number);

        // assert that your calculator added the numbers correctly!
        $this->assertEquals(1.03, $result);
    }
}