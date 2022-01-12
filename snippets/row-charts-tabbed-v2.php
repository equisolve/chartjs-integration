<?php
// This row needs to be set to Dynamic
global $page;
$page->value('charts_v2', true);
$cache_key = 'charts_v2_' . $id. $page_table_name;
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
<div class="module module-charts-v2 module-charts-tabbed <?= h($classes); ?>" data-row="<?= $id; ?>">
    <div class="container">
        <?php if ($title) : ?>
        <h2><?= h($title); ?></h2>
        <?php endif;?>
        <div class="content-box tabs-box">
            <div class="tab-header">
                <ul class="nav nav-tabs" role="tablist">
                    <?php foreach ($charts as $i => $chart) : ?>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link<?= $i == 0 ? ' active' : ''; ?>"
                            aria-controls="tab<?= h($chart->id()); ?>"
                            aria-selected="<?= $i == 0 ? 'true' : 'false'; ?>"
                            type="button"
                            role="tab"
                            data-toggle="tab"
                            data-target="#tab<?= h($chart->id()); ?>"
                        ><?= h($chart->tab_title()) ? : h($chart->title()); ?></button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="tab-content">
                <?php foreach ($charts as $i => $chart) : ?>
                <div role="tabpanel" class="tab-pane fade <?= $i == 0 ? 'show active' : '';  ?><?= $chart->chart_subtable() ? ' has-children' : ''; ?>" id="tab<?= h($chart->id()); ?>">
                    <?php   
                    if ($chart->chart_subtable()) :
                        snippet('row-tabbed-charts-v2-sub-tab', array(
                            'chart_sub_table_name' => $chart->chart_subtable(),
                            'id' => $id
                        ));
                    ?>
                    <?php else : ?>
                    <div class="row">
                        <div class="col-lg-4">
                            <?php if ($chart->title()) : ?>
                            <h3><?= h($chart->title()); ?></h3>
                            <?php endif; ?>
                            <?php if ($chart->subtitle()) : ?>
                            <h4 class="subtitle"><?= h($chart->subtitle()); ?></h4>
                            <?php endif; ?>
                            <div class="description">
                                <?= $chart->description(); // HTML ?>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="chart-wrapper">
                                <canvas id="chart-<?= h($chart->id()); ?>"></canvas>
                            </div>
                            <div class="spreadsheet-wrapper <?= !$chart->debug() ? 'sr-only' : ''; ?>"  aria-hidden="true">
                                <div id="chart-spreadsheet-<?= h($chart->id()); ?>"></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
$charts_data = [];
foreach ($charts as $chart) {
    if (!$chart->chart_subtable()) {
        $charts_data[] = $chart->prep_data();
    }
}
UtilSystem::start_javascript();
?>
<script id="charts-data-<?= $id; ?>" type="application/json">
    <?= json_encode(array('all_data' => $charts_data)); ?>
</script>