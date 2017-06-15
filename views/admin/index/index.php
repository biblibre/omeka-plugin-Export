<?php
    echo head(array('title' => __('Export')));
?>
<div id="primary">
    <?php echo flash(); ?>

    <h2><?php echo __('Available exporters'); ?></h2>
    <a class="button small green" href="<?php echo url('export/exporters/add'); ?>"><?php echo __('Add an exporter'); ?></a>
    <?php if (!empty($exporters)): ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Writer</th>
                    <th colspan="2"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($exporters as $exporter): ?>
                    <tr>
                        <td><a href="<?php echo url("export/exporters/{$exporter->id}/start"); ?>"><?php echo $exporter->name; ?></a></td>
                        <td>
                            <?php $writer = $exporter->getWriter(); ?>
                            <?php echo $writer->getLabel(); ?>
                            <?php if ($writer instanceof Export_Configurable): ?>
                                (<a href="<?php echo url("export/exporters/{$exporter->id}/configure-writer"); ?>">Configure</a>)
                            <?php endif; ?>
                        </td>
                        <td><a href="<?php echo url("export/exporters/{$exporter->id}/edit"); ?>">Edit</a></td>
                        <td><a href="<?php echo url("export/exporters/{$exporter->id}/delete"); ?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>There is no exporters yet. <a href="<?php echo url('export/exporters/add'); ?>">Create a new exporter</a></p>
    <?php endif; ?>

    <?php if (!empty($exports)): ?>
        <h2><?php echo __('Last exports'); ?></h2>

        <a href="<?php echo url('export/exports'); ?>"><?php echo __('See all exports'); ?></a>

        <?php echo $this->partial('exports/exports-table.php', array(
            'exports' => $exports,
        )); ?>
    <?php endif; ?>
</div>
<?php
    echo foot();
?>
