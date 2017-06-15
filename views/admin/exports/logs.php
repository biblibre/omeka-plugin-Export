<?php echo head(array('title' => __('Export logs'))); ?>

<?php
$severityNames = array(
    __('Emergency'),
    __('Alert'),
    __('Critical'),
    __('Error'),
    __('Warning'),
    __('Notice'),
    __('Info'),
    __('Debug'),
);
?>

<div id="primary">
    <?php echo flash(); ?>
    <h2><?php echo __('Export logs'); ?></h2>

    <form>
        <label>
            Show only messages whose severity is higher than or equal to
            <select name="severity">
                <?php foreach ($severityNames as $i => $name): ?>
                    <?php $selected = ($i === $severity) ? 'selected' : ''; ?>
                    <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Show</button>
    </form>

    <?php if (!empty($logs)): ?>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Message</th>
                    <th>Severity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log->added; ?></td>
                        <td><?php echo $log->getMessage(); ?></td>
                        <td>
                            <?php
                            if (array_key_exists($log->severity, $severityNames)) {
                                echo $severityNames[$log->severity];
                            } else {
                                echo $log->severity;
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo pagination_links(); ?>
    <?php else: ?>
        <p>No logs yet. <a href="">Refresh page</a></p>
    <?php endif; ?>
</div>

<?php echo foot(); ?>
