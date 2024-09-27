<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_activity_property`.
 */
class m240112_211511_create_user_activity_property_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'user_activity_property',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'property_id' => $this->integer(),
                'values' => $this->string(500),
            ]
        );
        
        // add foreign key for table `user_settings_config`
        $this->addForeignKey(
            'fk-user-activity-property-property_id',
            'user_activity_property',
            'property_id',
            'time_tracking_activity_property',
            'id',
            'CASCADE'
        );
        
        // creates index for column `user_id`
        $this->createIndex(
            'idx-user-activity-property-user_id',
            'user_activity_property',
            'user_id'
        );
    }

    public function down()
    {
        $this->dropTable('user_activity_property');
    }
}
