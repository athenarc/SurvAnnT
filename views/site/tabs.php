<?php foreach ($tabs as $tab => $url): ?>
    <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
        <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$survey->id : null ?>" >
            <h5 title = "<?= $tab ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> 
                <?= $tab.' '.$url['set'] ?>
            </h5>
        </a>
    </div>
<?php endforeach; ?>

<?php if( isset($status_message) && $status_message != '' ): ?>
    <div class="status-display row" style ="background-color: <?= ( $status < 300 ) ? 'lightgreen' : 'orange' ?>;" > 
        <div class="col-md-12 p-0">
            <?= $status_message ?> <b><a class="link-icon white fa fa-times close-status-message"></a></b>
        </div>
    </div>
<?php endif; ?>