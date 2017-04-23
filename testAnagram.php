<?php
/**
 * Created by PhpStorm.
 * @author tomas
 * @package tr
 * Date: 2017-04-22
 * Time: 18:04
 */

/**
 * isAnagram
 * function for test two strings if they are anagrams
 * @param string $string1
 * @param string $string2
 * @return bool
 */
function isAnagram($string1, $string2) : bool
{
    /**
     * Helper lambda function for sanitize string
     * @param $string
     * @return mixed
     */
    $sanitize = function ($string){
        return str_replace(" ","",strtolower($string));
    };
    // little check if function get required parameters, here should be exception if needed
    if(empty($string1) || empty($string2)) return false;
    // clear strings
    $string1 = $sanitize($string1);
    $string2 = $sanitize($string2);
    //If string length is not equal after sanitization, it's not anagram
    if(strlen($string1) !== strlen($string2)) return false;
    //Make them array
    $chars1 = str_split($string1);
    $chars2 = str_split($string2);
    //Sort them
    sort($chars1);
    sort($chars2);
    // If they are same, it's anagram, yay!
    if($chars1 === $chars2){
        return true;
    }
    return false;
}