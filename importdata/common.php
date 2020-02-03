<?php
    use \Magento\Framework\App\Bootstrap;

    include('../app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $state = $objectManager->get('Magento\Framework\App\State');
    $state->setAreaCode('frontend');
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
?>