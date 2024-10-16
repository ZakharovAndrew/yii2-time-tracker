<?php

use yii\db\Migration;

class m240112_221611_add_column_params_to_time_tracking_activity_property_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'time_tracking_activity_property',
                'params', 
                $this->text()->defaultValue(null)
            );
    }

    public function down()
    {
        $this->dropColumn('time_tracking_activity_property', 'params');
    }
}
