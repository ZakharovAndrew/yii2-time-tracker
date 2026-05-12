<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `time_tracking_activity_property`.
 */
class m240112_221912_add_values_function_column_to_time_tracking_activity_property_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('time_tracking_activity_property', 'values_function', $this->string(255)->null()->after('values'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('time_tracking_activity_property', 'values_function');
    }
}