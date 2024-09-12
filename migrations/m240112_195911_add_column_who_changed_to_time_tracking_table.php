<?php

use yii\db\Migration;

/**
 * Add column
 */
class m240112_195911_add_column_who_changed_to_time_tracking_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking',
                'who_changed',
                $this->integer()->defaultValue(null)->after('datetime_at')
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking', 'who_changed');
    }
}
