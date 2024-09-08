<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                        <a href="<?= route('home'); ?>" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium" aria-current="page">Home</a>
                        <a href="/about" class="bg-gray-900 text-white hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">About</a>
                        <a href="<?= route('posts.index'); ?>" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Posts</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <?php if(user()) { ?>
                    <span>Welcome <?= user()->username ?></span>
                    <?php } else { ?>
                    <a href="/login" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium" aria-current="page">Login</a>
                    <a href="/register" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium" aria-current="page">Register</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</nav>