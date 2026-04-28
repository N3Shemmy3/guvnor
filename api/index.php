<?php
require_once __DIR__ . '/include/config.php';

respond(['success' => getDbConnection() == null, 'message' => 'Oii Guvnor! n/ Where do you think you\'re going?'], 404);
