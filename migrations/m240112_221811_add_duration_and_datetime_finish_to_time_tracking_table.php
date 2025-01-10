<?php

use yii\db\Migration;

/**
 * Handles adding duration and datetime_finish to table `time_tracking`.
 */
class m240112_221811_add_duration_and_datetime_finish_to_time_tracking_table extends Migration
{
    public function up()
    {
        // Add the duration column (e.g., in seconds)
        $this->addColumn('time_tracking', 'duration', $this->integer()->defaultValue(null));
        
        // Add the completion_date column with timestamp type
        $this->addColumn('time_tracking', 'datetime_finish', $this->timestamp()->defaultValue(null));
    }

    public function down()
    {
        // Remove the duration column
        $this->dropColumn('time_tracking', 'duration');
        
        // Remove the completion_date column
        $this->dropColumn('time_tracking', 'datetime_finish');
    }
}
