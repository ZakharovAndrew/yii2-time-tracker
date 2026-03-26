<?php

use yii\db\Migration;

/**
 * Handles the creation of table `time_tracking`.
 */
class m240111_115911_create_time_tracking_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'time_tracking',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'activity_id' => $this->integer(),
                'datetime_at' => $this->timestamp()->defaultValue( new \yii\db\Expression('CURRENT_TIMESTAMP') ),
                'comment' => $this->string(500)->defaultValue(null)
            ]
        );
        
        // creates index for column `user_id`
        $this->createIndex(
            'idx-time-tracking-user-id',
            'time_tracking',
            'user_id'
        );
    }

    public function down()
    {
        $this->dropTable('time_tracking');
    }
}
