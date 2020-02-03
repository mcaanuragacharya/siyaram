<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$id         = $_REQUEST['id'];
$fileName   = 'categories.JSON'; 
$str        = file_get_contents($fileName);
$json       = json_decode($str,true);
$count      = count($json);
include('common.php');
if($count>0 && $id<$count){
    $sku = ''; 
    for($i=$id;$i<$count;$i++){
        $catName = importCategory::category($json[$i]);
        $m = $i+1;
        echo $catName.'@@'.$m;
        exit;

    }
} else {
    echo '';
}
exit;
class importCategory{
    public static function category($category){
        self::addLogData('Inside Category Function');
        include('common.php');
        if($category['name']!=''){
            $categoryId = self::isCategoryExist($category['name']);
            if(!$categoryId) {
                self::addLogData('Starting to Category -'.$category['name']);
                self::addCategory($category);
                self::addLogData('Ending to add category -'.$category['name']);
                return $category['name'];
            } else {
                self::addLogData('Starting to updating category -'.$category['name']);
                self::updateCategory($category,$categoryId);
                self::addLogData('Ending to update category -'.$category['name']);
                return $category['name'];
            }
        } else {
            self::addLogData('Category Name is not available in json file.');    
        }
        return;
    }
    //Function for Simple Product
    public static function updateCategory($data,$categoryId){
        include('common.php');       
        $categoryNameData = explode("/",$data['name']);
        $catName = $categoryNameData[count($categoryNameData)-1];
        self::addLogData('Category Update Started for '.$catName);    
        $category = $objectManager->create('Magento\Catalog\Model\CategoryFactory')->create()->setStoreId(2)->load($categoryId);
        $url = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $storeManager->getStore()->getStoreId();
        $store = $storeManager->getStore();
        $cleanurl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags(strtolower($catName)))))));
        if($data['is_active']!=''){
            $enabled = ($data['is_active']  == 'yes' ) ? 0 : 1;    
            $category->setIsActive( $enabled );
        }
        if($data['meta_description']!='') {
            $category->setData('description', strip_tags($data['meta_description']));
            $category->setMetaDescription(trim(strip_tags($data['meta_description'])));
        }
        if($data['meta_title'])
            $category->setMetaTitle(trim($data['meta_title']));
        if($data['meta_keywords'])
            $category->setMetaKeyword(trim($data['meta_keywords']));
        $category->setName(ucfirst($catName));
        $category->setUrlKey($cleanurl);
        $category->setStoreId($storeId);
        $parentId = $store->getRootCategoryId(); 
        $parentCategoryId = self::getParentCategorys($data,$categoryId);
        $rootCat = $objectManager->get('Magento\Catalog\Model\Category')->load($parentId);
        $category->setPath($rootCat->getPath().'/'.$parentCategoryId);
        try {
            $category->save();
            self::addLogData('Category Added with ID'.$category->getId());
        } catch (CouldNotSaveException $e) {
            self::addLogData($e);    
        } catch (InputException $e) {
            self::addLogData($e);    
        } catch (StateException $e) {
            self::addLogData($e);    
        } catch (LocalizedException $e) {
            self::addLogData($e);    
        }
        self::addLogData('Category Update Ended for '.$catName);    
    }
    public static function getParentCategorys($data,$categoryId){
        $parentCategoryId = '';
        $categoryNameData = explode("/",$data['name']);
        for($i=0;$i<count($categoryNameData);$i++){
            if($categoryNameData[$i] == $categoryNameData[count($categoryNameData)-1]){
                return $parentCategoryId;
            } else {
                $catId = self::isCategoryExist($categoryNameData[$i]);
                $parentCategoryId = $parentCategoryId.$catId.'/';    
            }
        }
    }
    //Function to Update Category
    public static function addCategory($category){
        include('common.php');
        self::addLogData('Import of Category Name with '.$category['name'].' Started');
        $categoryNameData = explode("/",$category['name']);
        $categoryIds = array();
        for($i=0;$i<count($categoryNameData);$i++){
            $data = array();
            if($i==count($categoryNameData)-1)
                $data = $category;
            self::addProductCategory($categoryNameData[$i],$categoryNameData,$data);
        }
        self::addLogData('Category imported successfully');
        return;
    }
    public static function addProductCategory($categoryName,$categoryNameData,$data){   
        $parentCategoryId = '';
        for($i=0;$i<count($categoryNameData);$i++){
            if($categoryNameData[$i]==$categoryName){
                $catId = self::isCategoryExist($categoryNameData[$i]);
                if($catId==''){
                    $catId = self::InsertCategory($categoryName,substr($parentCategoryId,0,-1),$data);
                }
                return $catId;
            } else {
                $catId = self::isCategoryExist($categoryNameData[$i]);
                $parentCategoryId = $parentCategoryId.$catId.'/';    
            }
        }    
    }
    //Function to get All the category ids
    public static function InsertCategory($categoryName,$parentCategoryId,$data){
        include('common.php');
        $url = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $storeManager->getStore()->getStoreId();
        $store = $storeManager->getStore();
        $cleanurl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags(strtolower($categoryName)))))));
        $categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');
        $category = $categoryFactory->create();
        $enabled = 0;
        if(count($data)>0){
            if($data['is_active']!='')
                $enabled = ($data['is_active']  == 'yes' ) ? 0 : 1;    
            if($data['meta_description']!='') {
                $category->setData('description', strip_tags($data['meta_description']));
                $category->setMetaDescription(trim(strip_tags($data['meta_description'])));
            }
            if($data['meta_title'])
                $category->setMetaTitle(trim($data['meta_title']));
            if($data['meta_keywords'])
                $category->setMetaKeyword(trim($data['meta_keywords']));

        }
        $category->setName(ucfirst($categoryName));
        $category->setIsActive( $enabled );
        $category->setUrlKey($cleanurl);
        if($parentCategoryId) {
            $lastParentCategory = explode("/",$parentCategoryId);
            $category->setParentId(6);
        }                       
        $category->setStoreId($storeId);
        $parentId = $store->getRootCategoryId();
        $rootCat = $objectManager->get('Magento\Catalog\Model\Category')->load($parentId);
        $category->setPath($rootCat->getPath().'/'.$parentCategoryId);
        try { 
            $category->save();
            self::addLogData('Category Added with ID'.$category->getId());
        } catch (CouldNotSaveException $e) {
            self::addLogData($e);    
        } catch (InputException $e) {
            self::addLogData($e);    
        } catch (StateException $e) {
            self::addLogData($e);    
        } catch (LocalizedException $e) {
            self::addLogData($e);    
        }
        return;
    }
    //Function to check if Category Exist
    public static function isCategoryExist($slug){
        $catName = explode("/",$slug);
        include('../app/bootstrap.php');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "Select * FROM catalog_category_entity_varchar WHERE attribute_id = '45' AND value = '".$catName[count($catName)-1]."'";
        $result = $connection->fetchAll($sql); // gives associated array, table fields as key 
        if(count($result)>0){
            return $result[0]['entity_id'];
        }
        return;
    }
    //Function to add Log
    public static function addLogData($data){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.'category-'.date('Y-m-d').'.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data); // Simple Text Log
    }
}