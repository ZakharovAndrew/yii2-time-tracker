<?php

use yii\db\Migration;

class m240112_221511_add_column_activity_id_to_user_activity_property_table extends Migration
{
    public function up()
    {
        $this->addColumn(
                'user_activity_property',
                'activity_id', 
                $this->integer()->defaultValue(null)
            );
    }

    public function down()
    {
        $this->dropColumn('user_activity_property', 'activity_id');
    }
}
