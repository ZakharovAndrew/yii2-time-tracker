<style>
.table-bordered {
    box-shadow: none;
    margin-bottom: 0;
}

.btn-edit-activity, .btn-delete-activity {
    display:none;
    font-size: 12px;
    padding: 3px 7px;
    margin-bottom: 5px;
}
.timeline-element:hover .btn-edit-activity, .timeline-element:hover .btn-delete-activity {
    display:inline-block;
}
.btn-add-activity {
    float: left;
    padding: 2px 5px;
    line-height: 16px;
    font-size: 18px;
    background-color: #4CAF50;
    border: none;
    margin-right:9px;
}
.timeline-date-update {
    color: #cfcfcf;
}
.work_time {
    /*font-weight: normal;
    font-size: 12px;*/
    color: #4caf50;
    padding-left:10px;
    float:right;
}
.break_time {
    padding-left:5px;
    color: #ff9800;
    float:right;
}
<?php 
$colors = ZakharovAndrew\TimeTracker\models\Activity::find()->where('color is not null')->all();
foreach ($colors as $activity) {
    echo '.activity-'.$activity->id.' { background-color:'.$activity->color.'}';
}
?>
</style>