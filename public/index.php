
<?php

session_start();

require_once __DIR__ . '/../config/app.php';

require_once ROOT_PATH . '/app/Controllers/PageController.php';
require_once ROOT_PATH . '/app/Controllers/MasterDataController.php';

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {

    case 'master-data':

        $controller = new MasterDataController();
        $controller->index();

        break;

    default:

        $controller = new PageController();
        $controller->index();

        break;
}
