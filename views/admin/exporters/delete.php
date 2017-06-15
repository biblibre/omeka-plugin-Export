<?php echo head(array('title' => __('Delete exporter'))); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2><?php echo __('Delete exporter (%s)', $exporter->name); ?></h2>
    <p><?php echo __('Are you sure you want to delete this exporter ?'); ?></p>
    <?php echo $form; ?>
</div>

<?php echo foot(); ?>
