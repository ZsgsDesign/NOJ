<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulltextIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `problem` ADD FULLTEXT(`title`) WITH PARSER ngram');
        DB::statement('ALTER TABLE `group` ADD FULLTEXT(`name`) WITH PARSER ngram');
        DB::statement('ALTER TABLE `contest` ADD FULLTEXT(`name`) WITH PARSER ngram');
        DB::statement('ALTER TABLE `users` ADD FULLTEXT(`name`) WITH PARSER ngram');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `problem` DROP INDEX `title`');
        DB::statement('ALTER TABLE `group` DROP INDEX `name`');
        DB::statement('ALTER TABLE `contest` DROP INDEX `name`');
        DB::statement('ALTER TABLE `users` DROP INDEX `name`');
    }
}
