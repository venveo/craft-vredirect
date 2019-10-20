<?php

/**
 *
 * @author    dolphiq
 * @copyright Copyright (c) 2017 dolphiq
 * @link      https://dolphiq.nl/
 */

namespace venveo\redirect\migrations;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\elements\User;
use craft\helpers\StringHelper;
use craft\mail\Mailer;
use craft\mail\transportadapters\Php;
use craft\models\Info;
use craft\models\Site;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTables();

        echo " done\n";
    }

    public function safeDown()
    {
        $this->dropTableIfExists('{{%venveo_redirects}}');
        $this->dropTableIfExists('{{%venveo_redirects_catch_all_urls}}');
        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        // new table!!

        $this->createTable('{{%venveo_redirects}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string('8')->null()->defaultValue('static')->notNull(),
            'sourceUrl' => $this->string(),
            'destinationUrl' => $this->string(),
            'statusCode' => $this->string(),
            'hitCount' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'hitAt' => $this->dateTime(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateDeleted' => $this->dateTime()->null(),
            'uid' => $this->uid()
        ]);

        if (!$this->db->tableExists('{{%venveo_redirects_catch_all_urls}}')) {

            $this->createTable(
                '{{%venveo_redirects_catch_all_urls}}',
                [
                    'id' => $this->primaryKey(),
                    'uri' => $this->string(255)->notNull()->defaultValue(''),
                    // 'firstHitAt' => $this->dateTime()->notNull(),
                    // 'lastHitAt' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'hitCount' => $this->integer()->unsigned()->notNull()->defaultValue(0),
                    'ignored' => $this->boolean()->notNull()->defaultValue(false),
                    'referrer' => $this->string(2000)->null(),
                ]
            );
        }

        $this->addForeignKey(null, '{{%venveo_redirects}}', ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
        $this->createIndex($this->db->getIndexName('{{%venveo_redirects}}', 'type'), '{{%venveo_redirects}}', 'type');
    }
}
