<?php
// This row needs to be set to Dynamic
global $page;
$page->value('charts_v2', true);
$cache_key = 'charts_v2_' . $id . $page_table_name;
$charts = InsiteModel::cache_get($cache_key);
if (!$charts) {
    // Define our charts array
    $charts = array();
    if ($chart_records = $page->page_type_table_records($page_table_name)) {
        foreach ($chart_records as $record) {
            if (!$record->disabled()) {
                $charts[] = new ChartV2($record);
            }
        }
    }
    InsiteModel::cache_set($cache_key, $charts);
}

// If there are no charts, return nothing
if (!$charts) {
    return;
}
?>
<div class="module module-charts-v2 <?= h($classes); ?>" data-row="<?= $id; ?>" <?= background($background_image, true); // HTML ?>>
    <div class="container">
    <?php foreach ($charts as $chart) : ?>
        <div class="chart-info">
        <?php if ($chart->title()) : ?>
            <h3><?= h($chart->title()); ?></h3>
        <?php
            endif;
            if ($chart->subtitle()) :
        ?>
            <h4 class="subtitle"><?= h($chart->subtitle()); ?></h4>
        <?php endif; ?>
            <div class="description">
                <?= $chart->description(); // HTML ?>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="chart-<?= h($chart->id()); ?>"></canvas>
        </div>
        <div class="spreadsheet-wrapper <?= !$chart->debug() ? 'sr-only' : ''; ?>"  aria-hidden="true">
            <div id="chart-spreadsheet-<?= h($chart->id()); ?>"></div>
        </div>
    <?endforeach; ?>
    </div>
</div>

<?php
$charts_data = [];
foreach ($charts as $chart) {
    $charts_data[] = $chart->prep_data();
}
UtilSystem::start_javascript();
?>
<script id="charts-data-<?= $id; ?>" type="application/json">
    <?= json_encode(array('all_data' => $charts_data)); ?>
</script>
<?php UtilSystem::end_javascript(); ?>