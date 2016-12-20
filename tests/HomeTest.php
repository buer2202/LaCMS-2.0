<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeTest extends TestCase
{
    public function testIndex()
    {
        $this->visit('/')
             ->see('container');
    }

    public function testCategory()
    {
        $this->visitRoute('category.index', 6)
             ->see('解决方案');
    }

    public function testDocument()
    {
        $this->visitRoute('document.index', '58525a5651c5a')
             ->see('col-lg-12 text-center info');
    }
}
