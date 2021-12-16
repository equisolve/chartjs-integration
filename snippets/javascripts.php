<?php
// Place this somewhere in your javascripts snippet to load the appropiate JS when needed
?>

<?php if ($page->value('charts_v2')): ?>
<script src="<?= h($company->asset_url('/files/theme/js/aside/spreadsheetjs/_js/all.js')); ?>"></script>
<script src="<?= h($company->asset_url('/files/theme/js/aside/chartjs/_js/all.js')); ?>"></script>
<?php endif; ?>