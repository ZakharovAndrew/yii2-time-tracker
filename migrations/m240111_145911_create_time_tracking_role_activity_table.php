<?php

use yii\db\Migration;

/**
 * Handles the creation of table `time_tracking_role_activity`.
 */
class m240111_145911_create_time_tracking_role_activity_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'time_tracking_role_activity',
            [
                'id' => $this->primaryKey(),
                'role_id' => $this->integer(),
                'activity_id' => $this->integer(),
                'pos' => $this->integer()->defaultValue(0)
            ]
        );
    }

    public function down()
    {
        $this->dropTable('time_tracking_role_activity');
    }
}
