<?php
namespace Nexgento\Streettext\Plugin\Checkout\Block\Checkout\AttributeMerger;

class Plugin
{
  public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
  {
  	
  	// echo "<pre>";print_r($result);die();
    if (array_key_exists('street', $result)) {
      $result['street']['children'][0]['placeholder'] = __('Flat /House');
      $result['street']['children'][1]['placeholder'] = __('Street');
    }
    if (array_key_exists('city', $result)) {
      $result['city']['placeholder'] = __('City');
    }
    if (array_key_exists('postcode', $result)) {
      $result['postcode']['placeholder'] = __('Zip Code');
    }
    if (array_key_exists('telephone', $result)) {
      $result['telephone']['placeholder'] = __('Phone');
    }
    if (array_key_exists('firstname', $result)) {
      $result['firstname']['placeholder'] = __('Name');
    }
    if (array_key_exists('lastname', $result)) {
      $result['lastname']['placeholder'] = __('Last Name');
    }
    // if (array_key_exists('email', $result)) {
    //   $result['email']['placeholder'] = __('Email');
    // }

    return $result;
  }
}