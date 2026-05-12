<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%time_tracking_approval}}`.
 */
class m240112_221911_create_time_tracking_approval_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%time_tracking_approval}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('User ID whose day is being approved'),
            'approval_date' => $this->date()->notNull()->comment('Date for which activity is approved'),
            'approver_id' => $this->integer()->notNull()->comment('User ID who approved the day'),
            'approved_at' => $this->timestamp()->defaultValue(new \yii\db\Expression('CURRENT_TIMESTAMP'))->comment('Date and time when approval was made'),
        ]);

        // Add unique constraint to ensure only one approval per user per day
        $this->createIndex(
            'idx-time_tracking_approval-unique_user_date',
            '{{%time_tracking_approval}}',
            ['user_id', 'approval_date'],
            true
        );

        // Add index for faster queries by approver
        $this->createIndex(
            'idx-time_tracking_approval-approver_id',
            '{{%time_tracking_approval}}',
            'approver_id'
        );

        // Add index for faster queries by approval date
        $this->createIndex(
            'idx-time_tracking_approval-approval_date',
            '{{%time_tracking_approval}}',
            'approval_date'
        );

        // Add foreign key for user_id if users table exists
        if ($this->db->getTableSchema('{{%users}}', true) !== null) {
            $this->addForeignKey(
                'fk-time_tracking_approval-user_id',
                '{{%time_tracking_approval}}',
                'user_id',
                '{{%users}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        // Add foreign key for approver_id if users table exists
        if ($this->db->getTableSchema('{{%users}}', true) !== null) {
            $this->addForeignKey(
                'fk-time_tracking_approval-approver_id',
                '{{%time_tracking_approval}}',
                'approver_id',
                '{{%users}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys if they exist
        if ($this->db->getTableSchema('{{%users}}', true) !== null) {
            $this->dropForeignKey('fk-time_tracking_approval-approver_id', '{{%time_tracking_approval}}');
            $this->dropForeignKey('fk-time_tracking_approval-user_id', '{{%time_tracking_approval}}');
        }

        // Drop indexes
        $this->dropIndex('idx-time_tracking_approval-approval_date', '{{%time_tracking_approval}}');
        $this->dropIndex('idx-time_tracking_approval-approver_id', '{{%time_tracking_approval}}');
        $this->dropIndex('idx-time_tracking_approval-unique_user_date', '{{%time_tracking_approval}}');
        
        // Drop table
        $this->dropTable('{{%time_tracking_approval}}');
    }
}