<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**


// ------------------------------------------------------------------------

/**
 * CodeIgniter General Helpers
 *
 * @package   CodeIgniter
 * @subpackage  Helpers
 * @category  Helpers
 * @author    Adesh Gupta

 */

// ------------------------------------------------------------------------


if ( ! function_exists('field_value'))
{
  function field_value($result, $value)
  {
    return (isset($result[$value]) ? $result[$value]:'');
  }
}

if ( ! function_exists('set_value_from_session'))
{
  function set_value_from_session($result, $value)
  {
    return (isset($result[$value]) ? $result[$value]:'');
  }
}


function substrtext($str, $allowLen, $addText='...')
{
  if(strlen($str)>$allowLen) {
    return substr($str, 0, $allowLen).$addText;
  } else {
    return $str;
  }
}

function pre($arr,$exit='')
{
  echo '===========Start Array===========';
  echo '<pre>';
  print_r($arr);
  echo '</pre>';
  echo '===========End Array=============';
  if($exit!='')
  {
    exit;
  }
}

if(!function_exists('changeDateFormat'))

{ 

    function changeDateFormat($format = 'd-m-Y', $originalDate)

    {
      date_default_timezone_set('Asia/Kolkata');
      return date($format, strtotime($originalDate));

    }

}

function getPageLimit($per_page)
{
  $cpage = end(explode('/page/',$_SERVER['REQUEST_URI']));
  $limit = '';
  if($cpage>1) {
    $limit = (($cpage*$per_page)-$per_page).','.$per_page;
  } else {
    $limit ='0,'.$per_page;
  }

  return $limit;
}

function ratingHtml($type='', $pre_fix='')
{
  $html = '<ul class="star-rating">';

  if($type=='show' or $type=='')
  {
    $html .= '<li class="current-rating" id="current-rating'.$pre_fix.'"><!-- will show current rating --></li>';
  }
    if($type=='submit' or $type=='')
  {
    $html .= '<span id="ratelinks'.$pre_fix.'">';
    $html .= '<li><a href="javascript:void(0)" title="1 of 5" class="one-star">1</a></li>';
    $html .= '<li><a href="javascript:void(0)" title="2 of 5" class="two-stars">2</a></li>';
    $html .= '<li><a href="javascript:void(0)" title="3 of 5" class="three-stars">3</a></li>';
    $html .= '<li><a href="javascript:void(0)" title="4 of 5" class="four-stars">4</a></li>';
    $html .= '<li><a href="javascript:void(0)" title="5 of 5" class="five-stars">5</a></li>';
    $html .= '</span>';
  }

    $html .= '</ul>';

  $html .= '<div id="rating_msg'.$pre_fix.'"></div><div class="cl"></div>';

  return $html;
}

function shareBox($fshare='', $tweet='')
{
  $html = '';

  if($tweet)
  {
    $html .= '<span class="fr" style="padding:0px;padding-left:10px;">';
    $html .= '<a href="https://twitter.com/share" class="twitter-share-button" >Tweet</a>';
    $html .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
    $html .= '</span>';

  }
  if($fshare)
  {
    $html .= '<span class="fr" style="padding:0px;">';
    $html .= '<a name="fb_share" type="button_count" share_url="'.current_url().'"></a>';
    $html .= '<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>';
    $html .= '</span>';
  }


  return $html;

}

function url_to_title($url)
{
  $arrReplace = array('-');
  $arrReplaceWith = array(' ');
  return str_replace($arrReplace, $arrReplaceWith, $url);
}

function address_format_1($name, $email, $add, $city, $state, $zip, $country, $phone, $br = '<br>')
{
  $html = '';
  $html .= ($name!=''?$name . $br:'');
  $html .= ($email!=''?$email . $br:'');
  $html .= ($add!=''?$add . $br:'');
  $html .= ($city!=''?$city:'') . ($state!=''?', '.$state:'') . ($country!=''?', '.$country:'').$br;
  $html .= ($zip!=''?$zip . $br:'');
  $html .= ($phone!=''?'Phone: '.$phone:'');

  return $html;
}

function date_format_1($date)
{
  return date('M d, Y', strtotime($date) );
}

function date_time_format_1($time)
{
  return date('M d, Y g:i A', $time);
}

function date_time_format_2($date)
{
  return date('M d, Y g:i A', strtotime($date));
}
// ------------------------------------------------------------------------

/**
 * CodeIgniter General Helpers
 *
 * @package   CodeIgniter
 * @subpackage  Helpers
 * @category  Helpers
 * @input     Full path of the file
 * @author    Kalicharan

 */

function downloadFile( $fullPath )
{

  // Must be fresh start
  if( headers_sent() )
    die('Headers Sent');

  // Required for some browsers
  if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');

  // File Exists?
  if( file_exists($fullPath) ){

    // Parse Info / Get Extension
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);

    // Determine Content Type
    switch ($ext) {
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      default: $ctype="application/force-download";
    }

    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); // required for certain browsers
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fsize);
    ob_clean();
    flush();
    readfile( $fullPath );

  } else {
    die('File Not Found');
  }

}


function createUserPassword($passLength,$user_name)
{
    $charStr    = "abcdefghijklmnopqrstuvwxyz";
    $DigitStr   = "0123456789";
    $strPass    = "";
    $digitPass  = "";

    for($i=0;  $i<floor($passLength/2); $i++){
    $randStrIndex  = rand(0,25);
    $strPass .=  substr($charStr, $randStrIndex, 1);

    $randDigitIndex  = rand(0,9);
    $digitPass .=  substr($DigitStr, $randDigitIndex, 1);
    }

    $userPass = str_shuffle($strPass.$digitPass);

    if($userPass==$user_name){
    createUserPassword($passLength,$user_name);
    }else{
    return $userPass;
    }
}

function ActivationCode()
{
    $Num=9*rand(11,19);

    $Code=$Num;
    $year = date("Y");
    $month = date("m");
    $day = date("d");

    if(strlen($month) >1)
    {
      $Code.="0".$month;
    }
    if(strlen($day)==1)
    {
      $Code.="0".$day;
    }
    else
    {
      $Code.=$day;
    }

    $Code.=$year;
    $hour=date("H");
    $min=date("i");
    $sec=date("s");
    if(strlen($hour)==1)
    {
      $Code.="0".$hour;
    }
    else
    {
      $Code.=$hour;
    }

    if(strlen($min)==1)
    {
      $Code.="0".$min;
    }
    else
    {
      $Code.=$min;
    }

    if(strlen($sec)==1)
    {
      $Code.="0".$sec;
    }
    else
    {
      $Code.=$sec;
    }

    return $Code;
}

function user_picture($user_id,$w,$h)
{
  $CI =& get_instance();
  $dataArr = $CI->user_model->getUserImage($user_id);

  $pic = $dataArr[0]['user_profile_picture'];

  if(trim($dataArr[0]['fb_id'])<>"") {
    return img( array( 'src'=>'https://graph.facebook.com/'.$dataArr[0]['fb_id'].'/picture' ) );
  } else if(is_file(UPLOADS.'/userpic/'.$pic) && file_exists(UPLOADS.'/userpic/'.$pic)){
    return '<img src="'.UPLOADS_URL.'userpic/'.$pic.'" width="'.$w.'" height="'.$h.'">';
  } else {
    return '<img src="'.UPLOADS_URL.'userpic/no-image.gif" width="'.$w.'" height="'.$h.'">';
  }

}

function getListingCommentCount($list_id)
{
  $CI =& get_instance();
  return $CI->listing_model->commentCount($list_id);
}
function getArticleCommentCount($article_id)
{
  $CI =& get_instance();
  return $CI->article_model->commentCount($article_id);

}


function getListingRate($listing_id)
{
   $CI =& get_instance();
   return $CI->listing_model->get_rate($listing_id);
}

function getArticleRate($article_id)
{
   $CI =& get_instance();
   return $CI->article_model->get_rate($article_id);
}

function checkSelectMultiple($check1, $check2) {
  if(in_array($check1,$check2)) {
    return "SELECTED";
  }
  return "";
}####end of checkSelectMultiple function


function form_fckeditor_1($data = '', $value = '', $extra = '')
{
    $CI =& get_instance();
   /* echo "<script type='text/javascript' src='plugins/ckeditor/ckeditor.js'></script>
                                    <textarea cols='100' id='cdesc' name='description' rows='10'></textarea>
                                    <script type='text/javascript'>
                                CKEDITOR.replace( 'cdesc',
                                    {
                                        toolbar :
                                        [
                                                
                                                ['Source','Bold','Italic','Underline'],
                        ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
                                                ['NumberedList','BulletedList','-','Outdent','Indent'],
                                                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                                                ['BidiLtr', 'BidiRtl'],
                                                ['Link','Unlink','Anchor','Image'],
                                                '/',
                                                ['Styles','Format','Font','FontSize'],
                                                ['TextColor','BGColor'],
                                                ['Maximize', 'ShowBlocks','-','About']
                                        ]
                                    });
                                
                                </script>     ";*/


       return  '<script type="text/javascript" src="'.base_url().'plugins/ckeditor/ckeditor.js"></script>' .
     '<script type="text/javascript">CKEDITOR.replace("ddd");</script>';

}

function form_fckeditor($data = '', $value = '', $extra = '')
{
    $CI =& get_instance();

    $fckeditor_basepath = 'application/plugins/fckeditor/';
   // echo getcwd().$fckeditor_basepath;exit;
   //echo INC_PATH . $fckeditor_basepath. 'fckeditor.php';
    require_once( INC_PATH . $fckeditor_basepath. 'fckeditor.php' );
    
    $instanceName = ( is_array($data) && isset($data['name'])  ) ? $data['name'] : $data;
    $fckeditor = new FCKeditor($instanceName);
    
    if( $fckeditor->IsCompatible() )
    {
        $fckeditor->Value = html_entity_decode($value);
        $fckeditor->BasePath = $fckeditor_basepath;
       // if( $fckeditor_toolbarset = 'Default';//$CI->config->item('Default'))
                //$fckeditor->ToolbarSet = 'Default';//$fckeditor_toolbarset;
        
        if( is_array($data) )
        {
            if( isset($data['value']) )
                $fckeditor->Value = html_entity_decode($data['value']);
            if( isset($data['basepath']) )
                $fckeditor->BasePath = $data['basepath'];
            if( isset($data['toolbarset']) )
                $fckeditor->ToolbarSet = $data['toolbarset'];
            if( isset($data['width']) )
                $fckeditor->Width = $data['width'];
            if( isset($data['height']) )
                $fckeditor->Height = $data['height'];
      if( isset($data['Class']) )
                $fckeditor->Class = $data['Class'];
        }
       
        
        return $fckeditor->CreateHtml();
    }
    else
    {
        return form_textarea( $data, $value, $extra );
    }
}

function script_tag($src)
{
  return '<script language="javascript" src="'.$src.'"></script>';
}
function link_tag($src)
{
  return '<link rel="stylesheet" type="text/css" href="'.$src.'" />';
}

function qualificationHelper()
{
  $qualification = array('10th Pass', '12th Pass', 'Pursuing Graduation', 'Graduate', 'Pursuing Post Graduation', 'Post Graduate', 'Diploma' , 'Doctorate or higher');
  return $qualification;
}
function get_oppositefinancial_years($start_year=false)
{
  
  $end_year=(date('m')>3 ? ( $start_year-5) : $start_year-5);
  
  for($i=$end_year;$i<=$start_year;$i++)
  {

   $return[] = $i.'-'.(substr($i+1 , 2 ));
    
  }
    // pr($return); die;
  /* $return['Total']='Total';*/
  return $return; 
}

/* End of file xml_helper.php */
/* Location: ./system/helpers/xml_helper.php */