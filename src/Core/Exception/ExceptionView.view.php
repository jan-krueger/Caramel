<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name ?> - <?= $message ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        pre { font-family: 'Fira Code', monospace; }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Main Container -->
    <div class="flex h-screen">

        <!-- Sidebar for stack trace -->
        <div class="w-1/4 bg-gray-50 p-4 border-r border-gray-300">
            <div class="mb-6">
                <button class="text-red-500 font-semibold">STACK</button>
                <button class="ml-4 text-gray-600">CONTEXT</button>
                <button class="ml-4 text-gray-600">SHARE</button>
            </div>

            <!-- Stack trace -->
            <div>
                <?php foreach($traces as $i => $trace) { ?>
                <?php foreach($trace as $j => $entry) { ?>
                    <?php if($j === 0) { ?>
                <div code-target="<?= $entry['key'] ?>" class="bg-gray-100 p-2 mb-2 text-sm border-l-4 border-red-500">
                    <?= $entry['file_clean'] ?>:<?= $entry['line'] ?> <br>
                    <span class="text-gray-500"><?= $entry['function'] ?></span>
                </div>
                <?php } else { ?>
                    <div code-target="<?= $entry['key'] ?>" class="text-sm text-gray-600 mt-2"><?= $entry['file_clean'] ?>:<?= $entry['line'] ?></div>
                <?php } ?>
                <?php } ?>
                <?php } ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-6">

            <!-- Error Header -->
            <div class="flex items-center bg-red-100 p-4 rounded-md mb-6">
                <div class="flex-1">
                    <h1 class="text-xl font-semibold text-red-600"><?= $name ?></h1>
                    <p class="text-gray-600"><?= $message ?></p>
                </div>
                <div class="text-sm text-gray-500">PHP <?= PHP_VERSION ?> @ 8.79.0</div>
            </div>

            <!-- Code Block -->
            <?php foreach($files as $id => $file) { ?>
                <div id="<?= $id ?>" class="code-snippet hidden bg-white rounded-lg border border-gray-300 overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <span class="text-gray-500"><a href="vscode://file/<?= $file['file'] ?>"><?= $file['file_clean'] ?>:<?= $file['line'] ?></a></span>
                </div>
                <pre class="p-4 text-sm bg-gray-100 whitespace-pre-wrap">
<?php foreach($file['code'] as $i => $line) { ?>
<?php if($i === $file['line']) { ?>
<span class="bg-red-100 text-red-600"><span class="text-gray-500"><?= $i ?></span>  <?= $line ?></span>
<?php } else { ?>
<span><span class="text-gray-500"><?= $i ?></span>  <?= $line ?></span>
<?php } ?>
<?php } ?>
    </pre>
            </div>
            <?php } ?>
        </div>
    </div>

    <script>
        const code_snippets = document.querySelectorAll('.code-snippet');
        document.querySelector('.hidden').classList.remove('hidden');
        document.querySelectorAll('[code-target]').forEach(element => {
            element.addEventListener('click', () => {
                code_snippets.forEach(snippet => snippet.classList.add('hidden'));
                document.getElementById(element.getAttribute('code-target')).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
