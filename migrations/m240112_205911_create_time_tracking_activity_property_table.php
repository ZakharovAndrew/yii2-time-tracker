<?php

use yii\db\Migration;

/**
 * Handles the creation of table `time_tracking_activity_property`.
 */
class m240112_205911_create_time_tracking_activity_property_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'time_tracking_activity_property',
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(200)->defaultValue(null),
                'type' => $this->integer()->defaultValue(0),
                'pos' => $this->integer()->defaultValue(0),
                'values' => $this->text()
            ]
        );
    }

    public function down()
    {
        $this->dropTable('time_tracking_activity_property');
    }
}
