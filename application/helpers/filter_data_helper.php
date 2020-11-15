<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * push notification helper
 * send push notification users
 * 
 * @author		
 * @link	
 */


if ( ! function_exists('filter_values'))
{
	function filter_values($string)
	{
		return preg_replace('/[^A-Za-z0-9\-@. \/]/', '', $string);
    } 
}

if ( ! function_exists('filter_numeric'))
{
	function filter_numeric($string)
	{		
	    return preg_replace("/[^0-9]/", "", $string);
    } 
}


if ( ! function_exists('filter_characters'))
{
	function filter_characters($string)
	{		
	    return preg_replace("/[^A-Za-z]/",'',$string);
    } 
}

if ( ! function_exists('filter_mixed'))
{
	function filter_mixed($string)
	{		
	    return preg_replace("/[^A-Za-z.,-_]/",'',$string);
    } 
}

if ( ! function_exists('filter_date'))
{
	function filter_date($string)
	{
		return preg_replace('/[^A-Za-z0-9\-@. ,\/]/', '', $string);
    } 
}

if ( ! function_exists('filter_ids'))
{
	function filter_ids($string)
	{
		return preg_replace('/[^A-Za-z0-9\-_@. ,\/]/', '', $string);
    } 
}

if ( ! function_exists('filter_empid'))
{
	function filter_empid($string)
	{
		return preg_replace('/[^A-Za-z0-9]/', '', $string);
    } 
}


