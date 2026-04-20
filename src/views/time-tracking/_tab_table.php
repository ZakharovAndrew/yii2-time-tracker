<?php

use ZakharovAndrew\TimeTracker\models\Activity;

/**
 * @var array $table - two-dimensional array of data to display in the table
 * @var string $caption - optional table caption
 * @var array $tableOptions - HTML attributes for the table (class, id, etc.)
 */

$tableOptions = $tableOptions ?? ['class' => 'table'];
$caption = $caption ?? '';
?>
<table <?= \yii\helpers\Html::renderTagAttributes($tableOptions) ?>>
<?php if (isset($table) && is_array($table) && !empty($table)): ?>
    <?php if ($caption): ?>
        <caption><?= htmlspecialchars($caption) ?></caption>
    <?php endif; ?>
            
    <thead>
        <tr>
            <?php 
            $header = array_keys($table[0]);
            foreach ($header as $col): 
            ?>
                <th><?= htmlspecialchars($col) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($table  as $row) {?>
        <tr>
            <?php foreach($row as $col_name => $col) {?>
            <td><?= $col ?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php else: ?>
    <div class="alert alert-info">Нет данных для отображения</div>
<?php endif; ?>