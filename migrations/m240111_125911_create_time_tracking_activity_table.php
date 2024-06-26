<?php

use yii\db\Migration;

/**
 * Handles the creation of table `time_tracking_activity`.
 */
class m240111_125911_create_time_tracking_activity_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'time_tracking_activity',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(100)->defaultValue(null)
            ]
        );
    }

    public function down()
    {
        $this->dropTable('time_tracking_activity');
    }
}
