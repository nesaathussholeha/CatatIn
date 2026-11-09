<?php

class PageController
{
    public function index()
    {
        $page_title = 'CatatIn';
        $user_name = 'Username';
        $user_role = 'user';

        ob_start();

        require VIEW_PATH . '/user/home.php';

        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/main.php';
    }
}