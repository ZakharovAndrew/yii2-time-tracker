<?php

use yii\db\Migration;

/**
 * Add column
 */
class m240112_165911_add_column_color_to_time_tracking_activity_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking_activity',
                'color', 
                $this->string(100)->defaultValue(null)
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking_activity', 'color');
    }
}
