<?php

use Phinx\Migration\AbstractMigration;

class Category extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
     $this->execute("
            CREATE TABLE `category` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255)  NOT NULL,
                `createdTime` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}