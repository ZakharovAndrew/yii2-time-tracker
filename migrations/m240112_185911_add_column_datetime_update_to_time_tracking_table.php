<?php

use yii\db\Migration;

/**
 * Add column
 */
class m240112_185911_add_column_datetime_update_to_time_tracking_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking',
                'datetime_update',
                $this->timestamp()->defaultValue(null)->after('datetime_at')
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking', 'datetime_update');
    }
}
