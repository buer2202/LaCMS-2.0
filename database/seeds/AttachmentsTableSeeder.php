<?php

use Illuminate\Database\Seeder;

use App\Attachment;

class AttachmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Attachment::class)->create(['id' => 1]);
        factory(Attachment::class)->create(['id' => 2, 'uri' => '/images/temp2.jpg']);
    }
}
