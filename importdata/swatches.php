<?php
include('common.php');
$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();
$attrArray = array(93,140);
for($q=0;$q<count($attrArray);$q++){
    $sql = "Select option_id FROM eav_attribute_option WHERE attribute_id = '".$attrArray[$q]."' ORDER BY option_id ASC";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key 
    for($i=0;$i<count($result);$i++){
        $sqlov = "Select * FROM eav_attribute_option_value WHERE option_id = '".$result[$i]['option_id']."' ORDER BY option_id DESC LIMIT 1";
        $resultov = $connection->fetchAll($sqlov); // gives associated array, table fields as key 
        for($j=0;$j<count($resultov);$j++){
           $sqlovs = "Select * FROM eav_attribute_option_swatch WHERE option_id = '".$resultov[$j]['option_id']."' and store_id = '".$resultov[$j]['store_id']."'";
           $resultovs = $connection->fetchAll($sqlovs); // gives associated array, table fields as key 
            if(count($resultovs)>0){
                $iupovs = "UPDATE eav_attribute_option_swatch SET value = '".$resultov[$j]['value']."' WHERE swatch_id = '".$resultovs[0]['swatch_id']."'";
            } else {
                $iupovs = "INSERT INTO 
                eav_attribute_option_swatch (option_id,store_id,type,value) 
                VALUES ('".$resultov[$j]['option_id']."','".$resultov[$j]['store_id']."',0,'".$resultov[$j]['value']."')";
            }
            $connection->query($iupovs);
        }
    }
}
exit("All Done");