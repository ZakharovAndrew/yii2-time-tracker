<?php

use yii\db\Migration;

class m240112_221711_add_column_required_time_tracking_activity_property_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking_activity_property',
                'required', 
                $this->integer()->defaultValue(0)
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking_activity_property', 'required');
    }
}
