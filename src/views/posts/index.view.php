<?php require(base_path('src/views/partials/head.php')); ?>
<?php require(base_path('src/views/partials/nav.php')); ?>
<?php require(base_path('src/views/partials/header.php')); ?>

    <ul>
        <?php foreach($posts as $post) { ?>
        <li> <a href="<?= route('post.show', ['id' => $post->id]); ?>"><?= $post->title ?></a> </li>
        <?php } ?>
    </ul>
    
    <a href="/posts/create">Create Post</a>


<?php require(base_path('src/views/partials/footer.php')); ?>
