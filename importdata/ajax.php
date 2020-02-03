<?php
//header('Content-Type: application/json');
$id         = $_REQUEST['id'];
$ptype      = $_REQUEST['ptype'];
$fileName   = 'ProductAPI.json';
$str        = file_get_contents($fileName);
//echo $str;
$json = json_decode($str, true);
//$error = json_last_error();
// print_r($error);
// print_r($json);
//exit("test");
//echo '<pre>';print_r($json);
//exit("No Data");
$count      = count($json);
//echo '<br><br><br><br>';
//echo $json;
//$json       = json_decode($data);

//exit("No Data");

if($count==0){
    echo '111111';
    exit;
}
include('common.php');
if(!is_nan($id) && $count>0 && $id<$count){
    $sku = '';
    for($i=$id;$i<$count;$i++){
        //if($json[$i]['CATEGORIES']=='T-Shirt'){
            if($json[$i]['PRODUCT_TYPE']=='SIMPLE' && $ptype=='SIMPLE'){
                $sku = importProduct::simpleProduct($json[$i]);
                $m = $i+1;
                echo $sku.'@@'.$m;
                exit;
            }
            else if($json[$i]['PRODUCT_TYPE']=='configurable' && $ptype=='CONFIG'){
                    $sku = importProduct::configProduct($json[$i]);
                    $m = $i+1;
                    echo $sku.'@@'.$m;
                    exit;
                }
        //}
    }
} else {
    importProduct::updateSwatches();
    echo '';
}
exit;
class importProduct{
//Function for Configurable Product
public static function configProduct($product){
    include('common.php');
    if($product['SKU']!=''){
        if(!self::isProductExist($product['SKU'])) {
            self::addLogData('Starting to add Configurable Products with sku -'.$product['SKU']);
            $validate = self::checkFieldValidation($product);
            if($validate==0){
                return $product['SKU'];
            }
            self::addConfigurableProduct($product);
            self::setImagetoBase();
            self::addLogData('Ending to add Configurable Products with sku -'.$product['SKU']);
            return $product['SKU'];
        } else {
            self::addLogData('Starting to updating Configurable Products with sku -'.$product['SKU']);
            $validate = self::checkFieldValidation($product);
            if($validate==0){
                return $product['SKU'];
            }
            self::updateConfigurableProduct($product);
            self::setImagetoBase();
            self::addLogData('Ending to add Configurable Products with sku -'.$product['SKU']);
            return $product['SKU'];
        }
    } else {
        self::addLogData('Sku Does not exist for Configurable Products.');
    }
    return;
}
//Function for Simple Product
public static function simpleProduct($product){
    include('common.php');
    if($product['SKU']){
        if(!self::isProductExist($product['SKU'])) {
            self::addLogData('Starting to add Simple Products with sku -'.$product['SKU']);
            $validate = self::checkFieldValidation($product);
            if($validate==0){
                return $product['SKU'];
            }
            self::addSimpleProduct($product);
            self::addLogData('Ending to add Simple Products with sku -'.$product['SKU']);
            self::setImagetoBase();
            return $product['SKU'];
        } else {
            self::addLogData('Starting to update Simple Products with sku -'.$product['SKU']);
            $validate = self::checkFieldValidation($product);
            if($validate==0){
                return $product['SKU'];
            }
            self::updateSimpleProduct($product);
            self::setImagetoBase();
            self::addLogData('Ending to update Simple Products with sku -'.$product['SKU']);
            return $product['SKU'];
        }
    } else {
        self::addLogData('Sku Does not exist for Simple Product Products.');
    }
}
//Function to Insert Configurable Products
public static function addConfigurableProduct($product){
    include('common.php');
    $productFactory = $objectManager->create('Magento\Catalog\Model\ProductFactory');
    $_product = $productFactory->create();
    //$r = rand(21,500);
    $_product->setName($product['NAME']);
    $_product->setCreatedAt(strtotime('now'));
    if($product['WEIGHT'])
        $_product->setWeight($product['WEIGHT']);
    else
        $_product->setWeight(1);
    $_product->setTypeId('configurable');
    $_product->setAttributeSetId(4);
    $_product->setSku($product['SKU']);
    $_product->setUrlKey($product['NAME']);
    $_product->setWebsiteIds(array(1));
    $_product->setStoreId(1);
    $_product->setVisibility(4);
    $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    $_product->setTaxClassId(0);
    $_product->setPrice($product['PRICE']);

    if($product['SPECIAL_PRICE'])
        $_product->setSpecialPrice($product['SPECIAL_PRICE']);
    if($product['SPECIAL_PRICE_FROM_DATE'])
        $_product->setSpecialFromDate($product['SPECIAL_PRICE_FROM_DATE']);
    if($product['SPECIAL_PRICE_TO_DATE'])
        $_product->setSpecialToDate($product['SPECIAL_PRICE_TO_DATE']);
    if($product['META_TITLE'])
        $_product->setMetaTitle($product['META_TITLE']);

    if($product['META_KEYWORDS'])
        $_product->setMetaKeyword($product['META_KEYWORDS']);

    if($product['META_DESCRIPTION'])
        $_product->setMetaDescription($product['META_DESCRIPTION']);

    if($product['DESCRIPTION'])
        $_product->setDescription($product['DESCRIPTION']);

    if($product['SHORT_DESCRIPTION'])
        $_product->setShortDescription($product['SHORT_DESCRIPTION']);

    $catKey = $product['CATEGORIES'];

    $catData = explode(",",$catKey);
    $CatId = array(2);
    for($j=0;$j<count($catData);$j++){
        $cid = self::getMyCategoryId($catData[$j]);
        if($cid!=''){
            $CatId[] = $cid;
        }
    }
    $_product->setCategoryIds($CatId);
    $size = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'size');
    $color = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'color');
    $fit = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'fit');
    $brand = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'brand');
    $ocassion = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'ocassion');
    $manufacturer = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'manufacturer');
    if($size!=''){
        $sizeValueId = self::getAttributeValueId($size,140);//Size
        $_product->setSize($sizeValueId);
    }
    if($color!=''){
        $colorValueId = self::getAttributeValueId($color,93);//Size
        $_product->setColor($colorValueId);
    }
    if($fit!=''){
        $fitValueId = self::getAttributeValueId($fit,93);//Size
        $_product->setFit($fitValueId);
    }
    if($brand!=''){
        $brandValueId = self::getAttributeValueId($brand,93);//Size
        $_product->setBrand($brandValueId);
    }
    if($ocassion!=''){
        $ocassionValueId = self::getAttributeValueId($ocassion,93);//Size
        $_product->setOcassion($ocassionValueId);
    }
    if($manufacturer!=''){
        $manufacturerValueId = self::getAttributeValueId($manufacturer,83);//Size
        $_product->setManufacturer($manufacturerValueId);
    }
    $_product->setStockData(array(
    'use_config_manage_stock' => 0, //'Use config settings' checkbox
    'manage_stock' => 1, //manage stock
    'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
    'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
    'is_in_stock' => 1, //Stock Availability
    'qty' => $product['QTY'] //qty
    )
    );
    try {
        $_product->save();

    } catch (CouldNotSaveException $e) {
        self::addLogData($e);
    } catch (InputException $e) {
        self::addLogData($e);
    } catch (StateException $e) {
        self::addLogData($e);
    } catch (LocalizedException $e) {
        self::addLogData($e);
    }
    $get_product_id = $_product->getId();
    $attributeModel = $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
    $position = 0;
    $attributes = array(140, 93); // Super Attribute Ids Used To Create Configurable Product
    $associatedProductIds = array();
    if($product['ASSOCIATED_SKUS']){
        $assP = explode(",",$product['ASSOCIATED_SKUS']);
        for($m=0;$m<count($assP);$m++){
            $pData = self::getProductData($assP[$m]);
            if(count($pData)>0 && $pData[0]['entity_id']!='' && !in_array($pData[0]['entity_id'],$associatedProductIds)){
                $associatedProductIds[] = $pData[0]['entity_id'];
            }
        }
    }
    foreach ($attributes as $attributeId) {
        $data = array('attribute_id' => $attributeId, 'product_id' => $get_product_id, 'position' => $position);
        $position++;
        $attributeModel->setData($data)->save();
    }
    $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $_product);
    $_product->setAssociatedProductIds($associatedProductIds);// Setting Associated Products
    $_product->setCanSaveConfigurableAttributes(true);
    $_product->save();
    if($product['BASE_IMAGE']!=''){
        $images = explode(",",$product['BASE_IMAGE']);

        if(count($images)>0){
            self::saveImagesForProduct($images,$get_product_id);
        }
    }
    return;
}
//Function to Update Configurable Products
public static function updateConfigurableProduct($product){
    include('common.php');
    self::addLogData("Now we are inside updateConfigurableProduct");
    $productId = self::getProductData($product['SKU']);
    ////////////////////////////////////////////////////////
    $productData = self::getProductData($product['SKU']);
    $pid = $productData[0]['entity_id'];
    self::addLogData("Loaded Product id ".$pid);
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory');
    $_product = $productFactory->create()->load($pid);
    self::addLogData("Loaded Product");
    $_product->setAttributeSetId(4);
    $_product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
    ////////////////////////////////////////////////////////
    /*
    $attributeModel = $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute');
    $position = 0;
    $attributes = array(140, 93); // Super Attribute Ids Used To Create Configurable Product
    $associatedProductIds = array();
    if($product['ASSOCIATED_SKUS']){
    $assP = explode(",",$product['ASSOCIATED_SKUS']);
    for($m=0;$m<count($assP);$m++){
    $pData = self::getProductData($assP[$m]);
    if(count($pData)>0 && $pData[0]['entity_id']!='' && !in_array($pData[0]['entity_id'],$associatedProductIds)){
    $associatedProductIds[] = $pData[0]['entity_id'];
    }
    }
    }
    foreach ($attributes as $attributeId) {
    $data = array('attribute_id' => $attributeId, 'product_id' => $pid, 'position' => $position);
    $position++;
    $attributeModel->setData($data)->save();
    }
    $objectManager->create('Magento\ConfigurableProduct\Model\Product\Type\Configurable')->setUsedProductAttributeIds($attributes, $_product);
    $_product->setAssociatedProductIds($associatedProductIds);// Setting Associated Products
    $_product->setCanSaveConfigurableAttributes(true);
    */
    self::addLogData("Updated Name");
    if($product['NAME']!='')
        $_product->setName($product['NAME']);
        self::addLogData("Updated Weight");
    if($product['WEIGHT'])
        $_product->setWeight($product['WEIGHT']);
    self::addLogData("Updated Price");
    if($product['PRICE']!='')
        $_product->setPrice($product['PRICE']);
    self::addLogData("Updated Special Price");
    if($product['SPECIAL_PRICE'])
        $_product->setSpecialPrice($product['SPECIAL_PRICE']);
    self::addLogData("Updated Special Price Date From");
    if($product['SPECIAL_PRICE_FROM_DATE'])
        $_product->setSpecialFromDate($product['SPECIAL_PRICE_FROM_DATE']);
    self::addLogData("Updated Special Price Date Too");
    if($product['SPECIAL_PRICE_TO_DATE'])
        $_product->setSpecialToDate($product['SPECIAL_PRICE_TO_DATE']);
    self::addLogData("Updated Meta Title");
    if($product['META_TITLE'])
        $_product->setMetaTitle($product['META_TITLE']);
    self::addLogData("Updated Meta Keywords");
    if($product['META_KEYWORDS'])
        $_product->setMetaKeyword($product['META_KEYWORDS']);
    self::addLogData("Updated Meta Desc");
    if($product['META_DESCRIPTION'])
        $_product->setMetaDescription($product['META_DESCRIPTION']);
    self::addLogData("Updated DESC");
    if($product['DESCRIPTION'])
        $_product->setDescription($product['DESCRIPTION']);
    else if($product['META_DESCRIPTION'])
        $_product->setDescription($product['META_DESCRIPTION']);
    self::addLogData("Updated Short Desc");
    if($product['SHORT_DESCRIPTION'])
        $_product->setShortDescription($product['SHORT_DESCRIPTION']);
    self::addLogData("Updated Data");
    //echo '<pre>';print_r($product);exit;
    if($product['CATEGORIES']!=''){
        $catKey = $product['CATEGORIES'];

        $catData = explode(",",$catKey);
        $CatId = array(2);
        for($j=0;$j<count($catData);$j++){
            $cid = self::getMyCategoryId($catData[$j]);
            if($cid!=''){
                $CatId[] = $cid;
            }
        }
        $_product->setCategoryIds($CatId);
    }
    self::addLogData("Updated Category");

    $size = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'size');
    $color = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'color');
    $fit = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'fit');
    $brand = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'brand');
    $ocassion = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'ocassion');
    $manufacturer = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'manufacturer');
    if($size!=''){
    $sizeValueId = self::getAttributeValueId($size,140);//Size
    $_product->setSize($sizeValueId);
    }
    if($color!=''){
    $colorValueId = self::getAttributeValueId($color,93);//Color
    $_product->setColor($colorValueId);
    }
    if($fit!=''){
    $fitValueId = self::getAttributeValueId($fit,93);//Fit
    $_product->setFit($fitValueId);
    }
    if($brand!=''){
    $brandValueId = self::getAttributeValueId($brand,93);//Brand
    $_product->setBrand($brandValueId);
    }
    if($ocassion!=''){
    $ocassionValueId = self::getAttributeValueId($ocassion,93);//Ocassion
    $_product->setOcassion($ocassionValueId);
    }
    if($manufacturer!=''){
        $manufacturerValueId = self::getAttributeValueId($manufacturer,83);//Manufacturer
        $_product->setManufacturer($manufacturerValueId);
    }

    if($product['QTY']!=''){
        $_product->setStockData(array(
        'use_config_manage_stock' => 0, //'Use config settings' checkbox
        'manage_stock' => 1, //manage stock
        'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
        'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
        'is_in_stock' => 1, //Stock Availability
        'qty' => $product['QTY'] //qty
        ));
    }
    self::addLogData("Updated Quantity");
    try {
        self::addLogData("Started Saved Basic Details");
        $_product->save();
        self::addLogData("Ended Saved Basic Details");
    } catch (Exception $e) {
        self::addLogData('Error in Saving Basic Details-'.$e->getMessage());
    }

    if($product['BASE_IMAGE']!=''){
        self::addLogData("Has Images in csv");
        $images = explode(",",$product['BASE_IMAGE']);
        if(count($images)>0){
            self::addLogData("Images count are ".count($images));
            self::saveImagesForProduct($images,$pid);
        }
    } else {
        self::addLogData("No Images in csv");
    }
    self::addLogData("Updated Images");
    return;
}
//Function to Insert Simple Product
public static function addSimpleProduct($product){
    include('common.php');
    $productFactory = $objectManager->create('Magento\Catalog\Model\ProductFactory');
    $_product = $productFactory->create();
    //$r = rand(21,500);
    $_product->setName($product['NAME']);
    $_product->setCreatedAt(strtotime('now'));
    if($product['WEIGHT'])
        $_product->setWeight($product['WEIGHT']);
    else
        $_product->setWeight(1);
    $_product->setTypeId('simple');
    $_product->setAttributeSetId(4);
    $_product->setSku($product['SKU']);
    $_product->setUrlKey($product['NAME']);
    $_product->setWebsiteIds(array(1));
    $_product->setStoreId(1);
    $_product->setVisibility(1);
    $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    $_product->setTaxClassId(0);
    $_product->setPrice($product['PRICE']);

    if($product['SPECIAL_PRICE'])
        $_product->setSpecialPrice($product['SPECIAL_PRICE']);
    if($product['SPECIAL_PRICE_FROM_DATE'])
        $_product->setSpecialFromDate($product['SPECIAL_PRICE_FROM_DATE']);
    if($product['SPECIAL_PRICE_TO_DATE'])
        $_product->setSpecialToDate($product['SPECIAL_PRICE_TO_DATE']);
    if($product['META_TITLE'])
        $_product->setMetaTitle($product['META_TITLE']);
    if($product['META_KEYWORDS'])
        $_product->setMetaKeyword($product['META_KEYWORDS']);

    if($product['META_DESCRIPTION'])
        $_product->setMetaDescription($product['META_DESCRIPTION']);
    if($product['DESCRIPTION'])
        $_product->setDescription($product['DESCRIPTION']);
    if($product['SHORT_DESCRIPTION'])
        $_product->setShortDescription($product['SHORT_DESCRIPTION']);
    $catKey = $product['CATEGORIES'];

    $catData = explode(",",$catKey);
    $CatId = array(2);
    for($j=0;$j<count($catData);$j++){
        $cid = self::getMyCategoryId($catData[$j]);
        if($cid!=''){
            $CatId[] = $cid;
        }
    }
    $_product->setCategoryIds($CatId);
    $size = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'size');
    $color = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'color');
    $fit = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'fit');
    $brand = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'brand');
    $ocassion = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'ocassion');
    $manufacturer = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'manufacturer');
    if($size!=''){
        $sizeValueId = self::getAttributeValueId($size,140);//Size
        $_product->setSize($sizeValueId);
    }
    if($color!=''){
        $colorValueId = self::getAttributeValueId($color,93);//Size
        $_product->setColor($colorValueId);
    }
    if($fit!=''){
        $fitValueId = self::getAttributeValueId($fit,93);//Size
        $_product->setFit($fitValueId);
    }
    if($brand!=''){
        $brandValueId = self::getAttributeValueId($brand,93);//Size
        $_product->setBrand($brandValueId);
    }
    if($manufacturer!=''){
        $manufacturerValueId = self::getAttributeValueId($manufacturer,83);//Size
        $_product->setManufacturer($manufacturerValueId);
    }
    if($ocassion!=''){
        $ocassionValueId = self::getAttributeValueId($ocassion,93);//Size
        $_product->setOcassion($ocassionValueId);
    }
    $_product->setStockData(array(
    'use_config_manage_stock' => 0, //'Use config settings' checkbox
    'manage_stock' => 1, //manage stock
    'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
    'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
    'is_in_stock' => 1, //Stock Availability
    'qty' => $product['QTY'] //qty
    )
    );
    try {
        $_product->save();
    } catch (CouldNotSaveException $e) {
        self::addLogData($e);
    } catch (InputException $e) {
        self::addLogData($e);
    } catch (StateException $e) {
        self::addLogData($e);
    } catch (LocalizedException $e) {
        self::addLogData($e);
    }
    if($product['BASE_IMAGE']!=''){
        $images = explode(",",$product['BASE_IMAGE']);
        $get_product_id = $_product->getId();
        if(count($images)>0){
            self::saveImagesForProduct($images,$get_product_id);
        }
    }
    return;
}
//Function to Update Simple Product
public static function updateSimpleProduct($product){
    include('common.php');
    ////////////////////////////////////////
    $productData = self::getProductData($product['SKU']);
    $pid = $productData[0]['entity_id'];
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory');
    $_product = $productFactory->create()->load($pid);
    $_product->setAttributeSetId(4);
    ////////////////////////////////////////
    //echo 'Id--'.$pid;
    //echo '<br>';

    if($product['NAME']!='')
        $_product->setName($product['NAME']);
    if($product['WEIGHT'])
        $_product->setWeight($product['WEIGHT']);
    if($product['PRICE']!='')
        $_product->setPrice($product['PRICE']);
    if($product['SPECIAL_PRICE'])
        $_product->setSpecialPrice($product['SPECIAL_PRICE']);
    if($product['SPECIAL_PRICE_FROM_DATE'])
        $_product->setSpecialFromDate($product['SPECIAL_PRICE_FROM_DATE']);
    if($product['SPECIAL_PRICE_TO_DATE'])
        $_product->setSpecialToDate($product['SPECIAL_PRICE_TO_DATE']);
    if($product['META_TITLE'])
        $_product->setMetaTitle($product['META_TITLE']);
    if($product['META_KEYWORDS'])
        $_product->setMetaKeyword($product['META_KEYWORDS']);
    if($product['META_DESCRIPTION'])
        $_product->setMetaDescription($product['META_DESCRIPTION']);
    if($product['DESCRIPTION'])
        $_product->setDescription($product['DESCRIPTION']);
    if($product['SHORT_DESCRIPTION'])
        $_product->setShortDescription($product['SHORT_DESCRIPTION']);
    if($product['CATEGORIES']!=''){
        $catKey = $product['CATEGORIES'];
        $catData = explode(",",$catKey);
        $CatId = array(2);
        for($j=0;$j<count($catData);$j++){
            $cid = self::getMyCategoryId($catData[$j]);
            if($cid!=''){
                $CatId[] = $cid;
            }
        }
        $_product->setCategoryIds($CatId);
    }
    $size = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'size');
    $color = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'color');
    $fit = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'fit');
    $brand = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'brand');
    $ocassion = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'ocassion');
    $manufacturer = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'manufacturer');
    if($size!=''){
        $sizeValueId = self::getAttributeValueId($size,140);//Size
        $_product->setSize($sizeValueId);
    }
    if($color!=''){
        $colorValueId = self::getAttributeValueId($color,93);//Size
        $_product->setColor($colorValueId);
    }
    if($fit!=''){
        $fitValueId = self::getAttributeValueId($fit,93);//Size
        $_product->setFit($fitValueId);
    }
    if($brand!=''){
        $brandValueId = self::getAttributeValueId($brand,93);//Size
        $_product->setBrand($brandValueId);
    }
    if($ocassion!=''){
        $ocassionValueId = self::getAttributeValueId($ocassion,93);//Size
        $_product->setOcassion($ocassionValueId);
    }
    if($manufacturer!=''){
        $manufacturerValueId = self::getAttributeValueId($manufacturer,83);//Size
        $_product->setManufacturer($manufacturerValueId);
    }
    if($product['QTY']!=''){
        $_product->setStockData(array(
        'use_config_manage_stock' => 0, //'Use config settings' checkbox
        'manage_stock' => 1, //manage stock
        'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
        'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
        'is_in_stock' => 1, //Stock Availability
        'qty' => $product['QTY'] //qty
        ));
    }
    try {
        $_product->save();
    } catch (CouldNotSaveException $e) {
        self::addLogData($e);
    } catch (InputException $e) {
        self::addLogData($e);
    } catch (StateException $e) {
        self::addLogData($e);
    } catch (LocalizedException $e) {
        self::addLogData($e);
    }
    if($product['BASE_IMAGE']!=''){
        $images = explode(",",$product['BASE_IMAGE']);
        if(count($images)>0){
            self::saveImagesForProduct($images,$pid);
        }
    }
    return;
}
//Function to set image as base image
public static function setImagetoBase(){
    include('../app/bootstrap.php');
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $sql = "UPDATE catalog_product_entity_media_gallery AS mg, catalog_product_entity_media_gallery_value AS mgv, catalog_product_entity_varchar AS ev SET ev.value = mg.value
    WHERE mg.value_id = mgv.value_id AND mgv.entity_id = ev.entity_id AND ev.attribute_id IN (87, 88, 89) AND mgv.position = 1";
    $connection->query($sql);
    return;
}
//Function to set all the Custom Options of Color and Size to Swatches
public static function updateSwatches(){
    include('../app/bootstrap.php');
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
}
//Function return value from string
public static function getValueFromString($data,$attribute){
    if($data!=''){
        $attributeData = explode(",",str_replace('"',"",$data));
        if(count($attributeData)>0){
            for($q=0;$q<count($attributeData);$q++){
                $attributeValue = explode('=',$attributeData[$q]);
                if($attributeValue[0]==$attribute){
                    return str_replace('="','',$attributeValue[1]);
                }
            }
        }
    }
    return;
}
//Function to insert images for the products
public static function saveImagesForProduct($images,$pid){
    include("common.php");
    //UPDATE catalog_product_entity_media_gallery AS mg, catalog_product_entity_media_gallery_value AS mgv, catalog_product_entity_varchar AS ev SET ev.value = mg.value
    //WHERE mg.value_id = mgv.value_id AND mgv.entity_id = ev.entity_id AND ev.attribute_id IN (87, 88, 89) AND mgv.position = 1
    self::addLogData('Image Uploading Function Started saveImagesForProduct.');
    //echo 'Insert Image for '.$pid;
    //echo '<pre>';
    //print_r($images);
    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($pid);
    $productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
    $productRepository->save($product);
    self::addLogData('Image Uploading Started.');
    $dir = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
    if(count($images)>0){
        $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
        foreach ($existingMediaGalleryEntries as $key => $entry) {
            unset($existingMediaGalleryEntries[$key]);
        }
        $product->setMediaGalleryEntries($existingMediaGalleryEntries);
        $productRepository->save($product);
    }
    for ( $i=0; $i<(count($images)); $i++) {
        $image_directory = $dir->getPath('media').'/importimages/'.basename(trim(strtolower($images[$i])));
        self::addLogData('Start Uploading Image-'.$image_directory);
        //echo 'Img-dir--'.$image_directory;
        //echo '<br>';
        if (file_exists($image_directory) && getimagesize($image_directory)) {
            self::addLogData('Image Exist-'.$image_directory);
            //echo 'Exist';
            //echo '<br>';
            try {
                if($i==0){
                    $product
                     ->setMediaGallery(array('images'=>  array(),'values'=>  array())) 
                    ->addImageToMediaGallery($image_directory, ['image', 'small_image', 'thumbnail','swatch_image'], false, false);
                    $productRepository->save($product);
                }
                else {
                    $product
                    ->setMediaGallery(array('images'=>  array(),'values'=>  array())) 
                    ->addImageToMediaGallery($image_directory,[], false, false);
                    $product->save();
                }
                self::addLogData('Image Added');
            } catch (Exception $e) {
                self::addLogData('Image Upload Error-'.$e->getMessage());
            }
            //echo 'Psave';
            //echo '<br>';
            $product->save();
        } else  {
            self::addLogData('Image Not Exist-'.$image_directory);
        }
        self::addLogData('End Uploading Image-'.$image_directory);
    }
    self::addLogData('Image Uploading Ended.');
    return;
}
//Function to return Option value
public static function getAttributeValueId($value,$attributeId){
    include('../app/bootstrap.php');
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $sql = "Select option_id FROM eav_attribute_option_value WHERE value = '".$value."' AND store_id = 1";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    if(count($result)>0)
        return $result[0]['option_id'];
    return self::insertCustomOption($value,$attributeId);
}
//Function to Insert Custom option for attribute
public static function insertCustomOption($value,$attributeId){
    include('../app/bootstrap.php');
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $sql = "Select sort_order FROM eav_attribute_option WHERE attribute_id = '".$attributeId."' ORDER BY sort_order DESC LIMIT 1";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    $sort_order = 0;
    if(count($result)>0){
        $sort_order = $result[0]['sort_order'];
        $sort_order++;
    }
    $sql = "Insert Into eav_attribute_option (attribute_id, sort_order) Values ('".$attributeId."','".$sort_order."')";
    $connection->query($sql);

    $sql = "Select * FROM eav_attribute_option WHERE attribute_id = '".$attributeId."' ORDER BY option_id DESC LIMIT 1";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    $optionId = $result[0]['option_id'];

    $sql = "Insert Into eav_attribute_option_value (option_id,store_id,value) Values ('".$optionId."',0,'".$value."'),('".$optionId."',1,'".$value."')";
    $connection->query($sql);
    return $optionId;

}
//Function to check if Category Exist
public static function getMyCategoryId($slug){
    include('../app/bootstrap.php');
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $sql = "Select * FROM catalog_category_entity_varchar WHERE attribute_id = '45' AND value = '".$slug."'";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    if(count($result)>0){
        return $result[0]['entity_id'];
    }
    return;
}
//Function to check product exist or not
public static function isProductExist($sku){
    include('common.php');
    $tableName = $resource->getTableName('catalog_product_entity'); //gives table name with prefix
    $sql = "Select * FROM " . $tableName." WHERE sku = '".$sku."'";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    if(count($result)>0)
        return true;
    return false;
}
//Function to get product id
public static function getProductData($sku){
    include('common.php');
    $tableName = $resource->getTableName('catalog_product_entity'); //gives table name with prefix
    $sql = "Select * FROM " . $tableName." WHERE sku = '".$sku."'";
    $result = $connection->fetchAll($sql); // gives associated array, table fields as key
    if(count($result)>0)
        return $result;
    return false;
}
//Function to Validate Fields
public static function checkFieldValidation($product){
    include('common.php');
    $error = 1;
    if($product['NAME']==''){
        self::addLogData('Product Name was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    if($product['WEIGHT']=='' || !is_numeric($product['WEIGHT'])){
        self::addLogData('Product Weight was not available or was not in numeric format for sku - '.$product['SKU']);
        $error = 0;
    }
    if($product['PRICE']==''){
        self::addLogData('Product Price was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    if($product['DESCRIPTION']==''){
        self::addLogData('Product Description was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    if($product['SHORT_DESCRIPTION']==''){
        self::addLogData('Product Short Description was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    $size = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'size');
    $color = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'color');
    $fit = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'fit');
    $brand = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'brand');
    $ocassion = self::getValueFromString($product['ADDITIONAL_ATTRIBUTES'],'ocassion');
    if($size==''){
        self::addLogData('Product Size was not available or was not in numeric format for sku - '.$product['SKU']);
        $error = 0;
    }
    if($color=='' || is_numeric($color)){
        self::addLogData('Product Color was not available or format was not proper for sku - '.$product['SKU']);
        $error = 0;
    }
    if($fit==''){
        self::addLogData('Product Fit was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    if($brand==''){
        self::addLogData('Product Brand was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    if($ocassion==''){
        self::addLogData('Product Ocassion was not available for sku - '.$product['SKU']);
        $error = 0;
    }
    return $error;
}
//Function to add Log
public static function addLogData($data){
    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product-'.date('Y-m-d').'.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    $logger->info($data); // Simple Text Log
}
}
