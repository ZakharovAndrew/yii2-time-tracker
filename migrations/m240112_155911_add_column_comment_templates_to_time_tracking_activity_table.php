<?php

use yii\db\Migration;

/**
 * Add column
 */
class m240112_155911_add_column_comment_templates_to_time_tracking_activity_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking_activity',
                'comment_templates', 
                $this->text()->null()->after('name')
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking_activity', 'comment_template');
    }
}
