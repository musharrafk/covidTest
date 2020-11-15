<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['default_controller'] = "home";
$route['404_override'] = '';
$route['admin'] = 'admin/login';
$route['change_pasword'] = 'login/change_password';

$route['default_controller'] = "login";
$route['404_override'] = 'my404';
$route['employee-details/(:any)'] = "employee/empDetails/$1";
$route['editcategory/(:any)'] = "category/add_edit_page/$1";
$route['editpolicy/(:any)'] = "policy/add_edit_page/$1";
$route['delete-category/(:any)'] = "category/delete_rec/$1";
$route['delete-policy/(:any)'] = "policy/delete_rec/$1";
$route['editemployee/(:any)'] = "employee/add_edit_page/$1";
$route['viewPolicy/(:any)'] = "dashboard/viewPolicy/$1";
$route['company-policy'] = "dashboard/companypolicy";
$route['editEducation/(:any)'] = "employee/edit_education/$1";
$route['generalMaster'] = "assets";
$route['attendance'] = 'attendance/leaveGroup';
$route['myattendance'] = 'dashboard/attendance';
$route['profile/(:any)'] = 'dashboard/index/$1';
$route['hiring/approve/(:any)'] ='general/approve/$1';
$route['hiring/reject/(:any)'] ='general/reject/$1';
$route['home'] = 'dashboard/index';
$route['dashboard'] ='dashboard/profile';
$route['candidateBackout/backout/(:any)'] ='candidateBackout/backout/$1';
//12-jan-17
$route['company-handbook'] = "dashboard/companyHandbook";
//12-jan-17

//$route['candidate/(:any)'] ='candidate/index/$1';

/* End of file routes.php */
/* Location: ./application/config/routes.php */