<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends TestCase
{
    public function testLogin()
    {
        $this->visit('/login')
             ->type('admin', 'name')
             ->type('admin', 'password')
             ->press('submit')
             ->seePageIs('/admin/category');
    }

    public function testAdminPage()
    {
        $this->actingAs(app(App\User::class)->first())
             ->visitRoute('admin.category.index')
             ->see('栏目管理')
             ->visitRoute('admin.document.index')
             ->see('文档管理')
             ->visitRoute('admin.attachment.index')
             ->see('附件清理')
             ->visitRoute('admin.administrator.index')
             ->see('系统管理员');
    }
}
