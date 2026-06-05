<?php

use yii\helpers\Url;
use ZakharovAndrew\TimeTracker\Module;

$this->title = Module::t('Working Hours Heatmap');
$this->params['breadcrumbs'][] = $this->title;

$startDate = $startDate ?? date('Y-m-d', strtotime('-1 week'));
$stopDate = $stopDate ?? date('Y-m-d');
?>
<div class="time-tracker-heatmap">
    <h1><?= Module::t('Working Hours Heatmap') ?></h1>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label"><?= Module::t('Start date') ?></label>
            <input type="date" id="start-date" class="form-control" value="<?= $startDate ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label"><?= Module::t('End date') ?></label>
            <input type="date" id="stop-date" class="form-control" value="<?= $stopDate ?>">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button id="update-chart" class="btn btn-primary">
                <?= Module::t('Update') ?>
            </button>
        </div>
    </div>

    <div class="chart-container" style="position: relative; min-height: 420px; overflow-x: auto;">
        <canvas id="heatmapChart"></canvas>
    </div>

    <div class="mt-2 text-muted small">
        <span class="badge bg-light text-dark border" style="background: rgba(255,99,132,0.1) !important;">⬤</span> 0 min
        <span class="ms-2 badge bg-light text-dark border" style="background: rgba(255,99,132,0.5) !important;">⬤</span> ~30 min
        <span class="ms-2 badge bg-light text-dark border" style="background: rgba(255,99,132,0.9) !important;">⬤</span> 45–60 min
    </div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js', [
    'position' => \yii\web\View::POS_END,
]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@2.0.1/dist/chartjs-chart-matrix.min.js', [
    'position' => \yii\web\View::POS_END,
    'depends' => ['\yii\web\JqueryAsset'],
]);

$updateUrl = Url::to(['/timetracker/dashboard/heatmap']);

$this->registerJs(<<<JS
(function() {
    const ctx = document.getElementById('heatmapChart').getContext('2d');
    let chart = null;

    function loadData() {
        const startDate = document.getElementById('start-date').value;
        const stopDate = document.getElementById('stop-date').value;

        fetch('{$updateUrl}?start_date=' + startDate + '&stop_date=' + stopDate, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) { renderChart(data); })
        .catch(function(err) { console.error('Heatmap data error:', err); });
    }

    function renderChart(data) {
        if (chart) { chart.destroy(); chart = null; }

        var container = document.getElementById('heatmapChart').parentElement;
        var emptyMsg = container.querySelector('.text-muted');
        if (emptyMsg) emptyMsg.remove();

        if (!data || data.length === 0) {
            container.innerHTML = '<p class="text-muted mt-3"><?= Module::t('No data for the selected period') ?></p>';
            return;
        }

        var users = [];
        var seen = {};
        data.forEach(function(d) {
            if (!seen[d.y]) { seen[d.y] = true; users.push(d.y); }
        });

        chart = new Chart(ctx, {
            type: 'matrix',
            data: {
                datasets: [{
                    label: 'Minutes',
                    data: data,
                    backgroundColor: function(ctx) {
                        var v = ctx.dataset.data[ctx.dataIndex].v;
                        var alpha = Math.min(v / 60, 1);
                        return 'rgba(255, 99, 132, ' + alpha + ')';
                    },
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    width: function(ctx) {
                        var a = ctx.chart.chartArea;
                        if (!a) return 30;
                        return Math.max(20, (a.right - a.left) / 24 - 2);
                    },
                    height: function(ctx) {
                        var a = ctx.chart.chartArea;
                        if (!a) return 20;
                        return Math.max(15, (a.bottom - a.top) / users.length - 2);
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: function() { return ''; },
                            label: function(ctx) {
                                var d = ctx.dataset.data[ctx.dataIndex];
                                return d.y + ' — ' + d.x + ':00 — ' + d.v + ' min';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        offset: false,
                        min: -0.5,
                        max: 23.5,
                        ticks: { stepSize: 1, callback: function(v) { return v + ':00'; } },
                        grid: { display: false },
                        title: { display: true, text: '<?= Module::t('Hour') ?>' }
                    },
                    y: {
                        type: 'category',
                        labels: users,
                        offset: true,
                        reverse: true,
                        grid: { display: false }
                    }
                }
            }
        });
    }

    loadData();

    document.getElementById('update-chart').addEventListener('click', function(e) {
        e.preventDefault();
        loadData();
    });
})();
JS
, \yii\web\View::POS_END);
