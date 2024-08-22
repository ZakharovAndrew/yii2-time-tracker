<?php

use yii\db\Migration;

/**
 * Add column
 */
class m240112_175911_add_column_hint_to_time_tracking_activity_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking_activity',
                'hint', 
                $this->text()->defaultValue(null)
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking_activity', 'hint');
    }
}
