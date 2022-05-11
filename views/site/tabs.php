<?php foreach ($tabs as $tab => $url): ?>
    <div class = "tab col-md" style = "border-bottom: <?= ( $tab == $message ) ? 'none !important;' : '' ?>">
        <a class = "<?= ( ! $url['enabled'] ) ? 'url-disabled' : '' ?> " href = "<?= ($url['enabled']) ? $url['link'].$surveyid : null ?>" >
            <h5 title = "<?= $tab ?>" style = "opacity: <?= ( $url['enabled'] ) ? '1' : '' ?>"> 
                <?= $tab.' '.$url['set'] ?>
            </h5>
        </a>
    </div>
<?php endforeach; ?>