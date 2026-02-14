<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <noscript>Your browser does not support JavaScript!</noscript>

    <?php if(!empty($entry['css'])): ?>
        <?php foreach($entry['css'] as $css): ?>
            <link rel="stylesheet" href="/dist/<?= htmlspecialchars($css) ?>" />
        <?php endforeach ?>
    <?php endif ?>
</head>
<body>
    <div id="app"></div>
    <?php if(!empty($entry['file'])): ?>
        <script src="/dist/<?= htmlspecialchars($entry['file']) ?>"></script>
    <?php endif ?>
</body>
</html>
