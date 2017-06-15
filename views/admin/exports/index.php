<?php echo head(array('title' => __('Exports'))); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2><?php echo __('Exports'); ?></h2>

    <?php if (!empty($exports)): ?>
        <?php echo $this->partial('exports/exports-table.php', array(
            'exports' => $exports,
        )); ?>

        <?php echo pagination_links(); ?>
    <?php else: ?>
        <p>No exports yet.</p>
    <?php endif; ?>
</div>

<?php echo foot(); ?>
