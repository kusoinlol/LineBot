<?php
try {
    $config = new Phalcon\Config\Adapter\Yaml(APP_PATH . "config/config.yml");
} catch (Exception $e) {
    echo $e->getMessage();
}

return $config;
?>