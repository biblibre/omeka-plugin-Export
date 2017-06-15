<table>
    <thead>
        <tr>
            <th>Exporter</th>
            <th>Status</th>
            <th>Started</th>
            <th>Completed</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($exports as $export): ?>
            <tr>
                <td><?php echo $export->getExporter()->name; ?></td>
                <td><?php echo $export->status; ?></td>
                <td><?php echo $export->started; ?></td>
                <td><?php echo $export->ended; ?></td>
                <td>
                    <a href="<?php echo url("export/exports/{$export->id}/logs"); ?>"><?php echo __('See logs'); ?></a>
                    <?php if ($export->filename): ?>
                        &#183;
                        <a href="<?php echo WEB_FILES . '/exports/' . $export->filename; ?>">Download file</a>
                    <?php endif; ?>
                    <?php if (in_array($export->status, array('completed', 'error'))): ?>
                        &#183;
                        <a href="<?php echo url("export/exports/{$export->id}/delete"); ?>">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
