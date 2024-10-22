<style>
.vertical-timeline {
    width: 100%;
    position: relative;
    padding: 1.5rem 0 1rem;
}
.timeline-element {
    position: relative;
    padding: 0 0 1rem;
}
.vertical-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 67px;
    height: 100%;
    width: 4px;
    background: #e9ecef;
    border-radius: .25rem;
}
.timeline-content {
    position: relative;
    margin-left: 90px;
    font-size: 11px;
}
.timeline-content .timeline-title {
    font-size: 12px;
    text-transform: uppercase;
    margin: 0 0 .5rem;
    padding: 2px 0 0;
    font-weight: 700;
}
.timeline-content p {
    color: #6c757d;
    margin: 0 0 .5rem;
}
.timeline-content .timeline-date {
    display: block;
    position: absolute;
    left: -90px;
    top: 0;
    padding-right: 10px;
    text-align: right;
    color: #adb5bd;
    font-size: 12px;
    white-space: nowrap;
}
.timeline-content:after {
    content: "";
    display: table;
    clear: both;
}
.timeline-element:after {
    content: "";
    display: table;
    clear: both;
}
.timeline-icon {
    position: absolute;
    top: -4px;
    left: 60px;
}
.badge-dot::before {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    position: absolute;
    left: 50%;
    top: 50%;
    margin: -5px 0 0 -5px;
    background: #fff;
}
.timeline-icon .badge-dot {
    box-shadow: 0 0 0 2px #fff;
}

.badge-dot {
    position: relative;
    font-weight: 700;
    text-transform: uppercase;
    padding: 5px 10px;
    text-indent: -999em;
    padding: 0;
    width: 8px;
    height: 8px;
    border-radius:50%;
    width: 18px;
    height: 18px;
    color: #212529;
    background-color: #ae71ce;
}

.timeline-header {
    display: block;
    background: #f3f3f3;
    padding: 6px 10px;
    border-radius: 8px;
    padding-left: 41px;
}
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
    float: right;
    padding: 2px 4px;
    line-height: 16px;
    font-size: 18px;
    background-color: #4CAF50;
    border: none;
    margin-right:5px;
}
.timeline-date-update {
    color: #cfcfcf;
}

<?php 
$colors = ZakharovAndrew\TimeTracker\models\Activity::find()->where('color is not null')->all();
foreach ($colors as $activity) {
    echo '.activity-'.$activity->id.' { background-color:'.$activity->color.'}';
}
?>
</style>