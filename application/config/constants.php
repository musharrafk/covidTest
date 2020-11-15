<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
/* if($_SERVER['HTTP_HOST'] == "localhost") {
	define('BASE_DIR','peopleconnect/');
	define('FILEPATH','D:/xampp/htdocs/peopleconnect/uploads/reports/');
	define('TICFILEPATH','D:/xampp/htdocs/peopleconnect/uploads/ticcard/');
} else {
	define('BASE_DIR','/');
	define('FILEPATH','/home/peopleconnect/public_html/uploads/reports/');
	define('TICFILEPATH','/home/peopleconnect/public_html/uploads/candidateDocument/ticcard/');
	define('ROOTPATH','/home/peopleconnect/public_html/');
} */
ini_set('memory_limit', '1024M');
date_default_timezone_set("Asia/Kolkata");
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
define('MODE', 'live');
define('BACKOFFICE_EMPTYPE_ID' , '1');
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/////////////// DIR
define('ADMIN_DIR','admin/');
define('SITE_IMAGEURL_ADMIN','images/admin_images/');
define('INC_PATH', FCPATH);
define('CAT_IMG','uploads/cat');

define('IMAGE_DIR','images/'); 
define("SITE_IMAGEURL", "images/");
define("UPLOADS_URL", "uploads/");
define('UPLOADS', FCPATH.'uploads/');
// define("UPLOADS_URL", IMAGEPATHTEST."uploads/");
// define('UPLOADS', IMAGEPATHTEST.'uploads/');
	// EMP pic path and url
define('EMPLOYEEPIC_URL', UPLOADS_URL.'empImage/');
define('EMPLOYEEPIC', UPLOADS.'empImage/');

define('IDPROFF_URL', UPLOADS_URL.'empidproff/');
define('IDPROFF', UPLOADS.'empidproff/');

define('ADDRESSPROFF',UPLOADS.'empaddressproff/');
define('ADDRESSPROFF_URL',UPLOADS_URL.'empaddressproff/');

define('OFFERLETTER', UPLOADS.'offerletter/');
define('OFFERLETTER_URL', UPLOADS_URL.'offerletter/');
define('DOWNLOADRESUME',UPLOADS.'download/');
define('DOWNLOADRESUME_URL',UPLOADS_URL.'download/');

define('DOWNLOADSALARYSLIP',UPLOADS.'download_salaryslip/');
define('DOWNLOADSALARYSLIP_URL',UPLOADS_URL.'download_salaryslip/');
define('CANDIDATEDOCUMENT', UPLOADS.'candidateDocument/');
define('CANDIDATEDOCUMENT_URL', UPLOADS_URL.'candidateDocument/');

define('CANDIDATEIMAGE', UPLOADS.'candidateDocument/empImage/');
define('CANDIDATETEMPIMAGE', UPLOADS.'candidateDocument/temp/');
define('CANDIDATEIMAGE_URL', UPLOADS_URL.'candidateDocument/empImage/');

/*define('APPOINTMENTLETTERPDF', UPLOADS.'appointmentpdf/');
define('APPOINTMENTLETTERPDF_URL', UPLOADS_URL.'appointmentpdf/');*/
define('APPOINTMENTLETTER',UPLOADS.'/empAppointmentLetter/');
define('APPOINTMENTLETTER_URL',UPLOADS_URL.'/empAppointmentLetter/');

define('PHOTOID',UPLOADS.'/candidateDocument/photoIdproof/');
define('PHOTOID_URL',UPLOADS_URL.'candidateDocument/photoIdproof/');

define('ADDRESSPROOF',UPLOADS.'/candidateDocument/addressProof/');
define('ADDRESSPROOF_URL',UPLOADS_URL.'candidateDocument/addressProof/');

define('EDUCATIONCERTIFICATE',UPLOADS.'/educationcertificate/');
define('EDUCATIONCERTIFICATE_URL',UPLOADS_URL.'educationcertificate/');

define('ADHARCARD',UPLOADS.'/candidateDocument/adharCard/');
define('ADHARCARD_URL',UPLOADS_URL.'candidateDocument/adharCard/');

define('EXPERIENCECERTIFICATE',UPLOADS.'/candidateDocument/experienceCertificates/');
define('EXPERIENCECERTIFICATE_URL',UPLOADS_URL.'candidateDocument/experienceCertificates/');

define('EXPERIENCECERTIFICATE_EMP',UPLOADS.'/experience/');
define('EXPERIENCECERTIFICATE_EMP_URL',UPLOADS_URL.'/experience/');

define('PAN',UPLOADS.'/candidateDocument/panCard/');
define('PAN_URL',UPLOADS_URL.'candidateDocument/panCard/');

define('PAYSLIP',UPLOADS.'/candidateDocument/payslip/');
define('PAYSLIP_URL',UPLOADS_URL.'candidateDocument/payslip/');

define('QUALIFICATION',UPLOADS.'/candidateDocument/qualificationsCertificates/');
define('QUALIFICATION_URL',UPLOADS_URL.'candidateDocument/qualificationsCertificates/');


define('RELIEVINGLETTER',UPLOADS.'/candidateDocument/relievingLetter/');
define('RELIEVINGLETTER_URL',UPLOADS_URL.'candidateDocument/relievingLetter/');


define('RELIEVINGLETTER_EMP',UPLOADS.'/emprelieving/');
define('RELIEVINGLETTER_EMP_URL',UPLOADS_URL.'emprelieving/');

define('CANCELCHEQUE',UPLOADS.'/empcancelcheque/');
define('CANCELCHEQUE_URL',UPLOADS_URL.'/empcancelcheque/');



define('CANDIDATERESUME',UPLOADS.'candidateDocument/resume/');
define('CANDIDATERESUME_URL',UPLOADS_URL.'candidateDocument/resume/');

define('CANDIDATECHECK',UPLOADS.'candidateDocument/cancelCheck/');
define('CANDIDATECHECK_URL',UPLOADS_URL.'candidateDocument/cancelCheck/');

define('CSVFILE',UPLOADS.'attendanceCsv/');
define('CSVFILE_URL',UPLOADS_URL.'attendanceCsv/');

define('REPORTS',UPLOADS.'reports/');
define('REPORTS_URL',UPLOADS_URL.'reports/');

define('SALARYSLIP_EMP',UPLOADS.'empsalaryslip/');
define('SALARYSLIP_EMP_URL',UPLOADS_URL.'empsalaryslip/');

define('PASSPORT_CAN', UPLOADS.'candidateDocument/passport/');
define('PASSPORT_URL_CAN', UPLOADS_URL.'candidateDocument/passport/');

define('PASSPORT', UPLOADS.'/empPassport/');
define('PASSPORT_URL', UPLOADS_URL.'/empPassport/');


define('PANCARD', UPLOADS.'empPan/');
define('PANCARD_URL', UPLOADS_URL.'empPan/');
define('RATIONCARD', UPLOADS.'empRationcard/');
define('RATIONCARD_URL', UPLOADS_URL.'empRationcard/');
define('DRIVINGLICENCE', UPLOADS.'empDrivingLicence/');
define('DRIVINGLICENCE_URL', UPLOADS_URL.'empDrivingLicence/');
define('ADHAAR',UPLOADS.'empAdhaar/');
define('ADHAAR_URL',UPLOADS_URL.'empAdhaar/');
define('VOTERID',UPLOADS.'empVoterid/');
define('VOTERID_URL',UPLOADS_URL.'empVoterid/');
define('RESUME',UPLOADS.'empResume/');
define('RESUME_URL',UPLOADS_URL.'empResume/');

/*define('ESIPHOTOGRAPH',UPLOADS.'candidateDocument/esifamilyPhotograph/');
define('ESIPHOTOGRAPH_URL',UPLOADS_URL.'candidateDocument/esifamilyPhotograph/');*/

define('ESIPHOTOGRAPH',UPLOADS.'/empEsi/');
define('ESIPHOTOGRAPH_URL',UPLOADS_URL.'/empEsi/');


define('TIC',UPLOADS.'candidateDocument/ticcard/');
define('TIC_URL',UPLOADS_URL.'candidateDocument/ticcard/');

/*define('APPOINTMENTLETTER',UPLOADS.'candidateDocument/appointmentLetter/');
define('APPOINTMENTLETTER_URL',UPLOADS_URL.'candidateDocument/appointmentLetter/');*/


define('APPOINTMENTLETTER',UPLOADS.'/empAppointmentLetter/');
define('APPOINTMENTLETTER_URL',UPLOADS_URL.'/empAppointmentLetter/');



define('EMPLOYEEPIC_THUMB', UPLOADS.'empImage/');
define('SALARYSLIP',UPLOADS.'salarySlip/');
define('SALARYSLIP_URL',UPLOADS_URL.'salarySlip/');

define('FORM16',UPLOADS.'form16/');
define('FORM16_URL',UPLOADS_URL.'form16/');


define('FORM',UPLOADS.'formdownload/');
define('FORM_URL',UPLOADS_URL.'formdownload/');

define('CONFIRMATIONLETTER', UPLOADS.'confirmationLetter/');
define('CONFIRMATIONLETTER_URL', UPLOADS_URL.'confirmationLetter/');


 
define('DATABANK_RESUME_URL', '../databank/uploads/candidateDocument/');

define('TABLE_INCREMENT','tbl_increment');
//08-March-18
//16-march-18
define('TABLEGRADE','tbl_grade');
//16-march-18
//17-march-18
define('TABLE_DESIG_FUNCTION','tbl_mst_designation_function');
//17-march-18


define("TABLE_FORM", "tbl_form" );
////////////////// VAR
define('SITE_NAME', 'ARK INFO SOLUTIONS PVT LTD');
define('COPYRIGHT_TEXT','Copyright &copy; '.date('Y').' ARK INFO SOLUTIONS PVT LTD. All rights reserved.');


//////////////// Tables
define('TABLE_EMP', 'tbl_emp_master');
define('TABLE_MASTER_ASSET', 'tbl_mst_asset');
define('TABLE_EMP_ASSET', 'tbl_emp_asset');
define('TABLE_ACTIVITY', 'tbl_activity');
define('TABLE_EMP_LOG', 'tbl_login_log');

define('TABLE_SKU_MASTER', 'tbl_sku_master');
define('TABLE_STOCK', 'tbl_stock');
define('TABLE_STOCK_CATEGORY', 'tbl_stock_category');
define('TABLE_OUTLET', 'tbl_outlet');
define('TABLE_CITY_MASTER', 'tbl_mst_city');
//define('TABLE_CITY_MASTER', 'tbl_mst_branch');
define('TABLE_BRANCH_MASTER', 'tbl_mst_branch');

define('TABLE_STATE_MASTER', 'tbl_mst_state');
define('TABLE_REGION_MASTER', 'tbl_region');
define('TABLE_DESIGNATION_MASTER', 'tbl_mst_designation');
define('TABLE_SALE', 'tbl_sale');
define('TABLE_REGION_STATE', 'tbl_region_state');
//13-sep
define('TABLE_RESIGN_REASON_MASTER', 'tbl_resignreason');

define('TABLE_ADDRESS', 'tbl_emp_address');
define('TABLE_DOCUMENT', 'tbl_emp_document');
define('TABLE_CANDIDATE_EDUCATION','tbl_candidate_education');
define('TABLE_CANDIDATE_TRAINING','tbl_candidate_training');
define('TABLE_EMP_TRAINING','tbl_employee_training');
define('TABLE_CANDIDATE_PROFESSIONAL_CNCT','tbl_candidate_prof_cntct');
define('TABLE_EMP_PROFESSIONAL_CNCT','tbl_employee_prof_cntct');
define('TABLE_CANDIDATE_PERSONAL_CNCT','tbl_candidate_pers_cntct');
define('TABLE_EMP_PERSONAL_CNCT','tbl_employee_pers_cntct');
define('TABLE_EDUCATION','tbl_emp_education');
define('TABLE_EXP', 'tbl_emp_exp');
define('TABLE_CANDIDATE_EXP','tbl_candidate_exp');
define('TABLE_FAMILY', 'tbl_emp_family');
define('TABLE_CANDIDATE_FAMILY', 'tbl_candidate_family');
define('TABLE_GROWTH', 'tbl_emp_growth');
define('TABLE_LANGUAGE', 'tbl_emp_language');
define('TABLE_MST_LANGUAGE','tbl_mst_language');
define('TABLE_PERSONAL_DETAILS', 'tbl_emp_personal');
define('TABLE_ROLE', 'tbl_emp_role');
define('TABLE_SERVICE', 'tbl_emp_service');
define('TABLE_COUNTRY', 'tbl_mst_country');
define('TABLE_STATE', 'tbl_mst_state');
define('TABLE_CITY', 'tbl_mst_city');
//define('TABLE_CITY', 'tbl_mst_branch');
define('TABLE_DEPT','tbl_mst_dept');
define('TABLE_BANK', 'tbl_emp_bank');
define('TABLE_BLOODGROUP', 'tbl_mst_blood_group');
define('TABLE_MASTERST_LANGUAGE','tbl_mst_language');
define('TABLE_EMPTYPE','tbl_mst_emptype');
define('TABLE_MASTER_DESIGNATION', 'tbl_mst_designation');
define('TABLE_CATEGORY', 'tbl_mst_category');
define('TABLE_POLICY', 'tbl_policy');
define('TABLE_MODULE','tbl_mst_module');
define('TABLE_LEFT','tbl_user_left');
define('TABLE_LEFTV2','tbl_user_left_v2');
define('TABLE_COMPANY','tbl_company');
define('TABLE_LEAVEGROUP','tbl_mst_leavegroup');
define('TABLE_RULE','tbl_rule');
define('TABLE_LEAVETYPE','tbl_mst_leavetype');
define('TABLE_LOCATION','tbl_mst_location');
define('TABLE_SHIFT','tbl_mst_shift');
define('TABLE_HOLIDAYS','tbl_mst_holiday');
define('TABLE_ATTENDANCE_TEMP','tbl_attendance_temp');
define('TABLE_ATTENDANCE_LOG','tbl_attendance_log');
define('TABLE_ATTENDANCE','tbl_emp_attendance');
define('TBL_EMP_PERSONAL','tbl_emp_personal');
define('TABLE_COMPANY_SETTING','tbl_company_setting');
define('TABLE_MASTER_MODULE','tbl_module');
define('TABLE_HOLIDAY','tbl_mst_holiday');
define('TABLE_EMP_LEAVE','tbl_emp_leave_history');
define('TABLE_OUTLETS','tbl_outlet');
define('TABLE_OUTLETSCATEGORY','tbl_outlet_category');
define('TABLE_SALARYSLIP','tbl_emp_salary_slip');
define('TABLE_SALARYSTRUCTURE','tbl_salary_details');
define('TABLE_REGION_HOLIDAY_MASTER', 'tbl_mst_holiday_region');
define("TABLE_MONTHLY_SALARY",'tbl_monthly_salary');
define("TABLE_RENT_API",'tbl_rent_api');
define("TABLE_INVESTMENT_DECLARE",'tbl_investment_declaration_api');


define('TABLE_CANDIDATE','tbl_candidate');
define('TABLECLIENTS','tbl_client');
define('TABLEPROJECT','tbl_project');
define('TABLE_GUEST_MASTER','tbl_guest_master');
define('TABLE_REGION','tbl_region');
define('TABLE_MPR','tbl_manpowerrequisition');
define('TABLE_SALARYBREAKUP','tbl_salarybreakup');
define('TABLE_REGULARIZATION','tbl_regularization');
//4-sep-18
define('TABLE_REGULARIZATION_DETAILS','tbl_regularization_details');
//4-sep-18
define('TABLE_REFERAL','tbl_referal');
define('TABLE_SALES_TARGET', 'tbl_salestarget');
define('TABLE_CANDIDATE_BACKUP','tbl_candidate_backup');
define('TABLE_WEEKOFF','tbl_weekoff');
define('TABLE_ADDWEEKOFF','tbl_emp_weekoff');
define('TABLE_LEAVE_BALANCE','tbl_emp_leave_balance');
define("TABLE_CONTENT",'tbl_mailbody');
define("TABLE_REGION_HOLIDAY_CLIENTS_MASTER",'tbl_mst_holiday_region_client');
define("TABLE_LEAVEGROUPLOG",'tbl_leavegrouplog');
define("TABLE_WAGES",'tbl_minimumwages');
define("TABLE_WAGES_HISTORY",'tbl_minimumwages_history');
define("TABLE_TIC",'tbl_tic');
define("TABLE_FORM16",'tbl_form16');
define("TABLE_ROWDATA",'rowdata');

define('TABLE_TRAINING_MASTER', 'tbl_training_master');
define('TABLE_TRAINING_REG', 'tbl_training_registration');
define('TABLE_EMP_TRAINING_REG', 'tbl_emp_training_registration');

//14-june-19
define('TABLE_BRAND_SUPPORT_TEAM', 'brand_support_team');
define('TABLE_BRAND_REQUEST_TYPE', 'brand_request_type');
define('TABLE_MST_BRAND_TICKET', 'brand_ticked_records');
define('TABLE_MST_BRAND_TICKET_TRANS', 'brand_transaction_tkrecords');
define('TABLE_MST_BRAND_PARTNER_TABLE', 'brand_partner_table');

//14-june-19
define('TABLE_HELPDESK_SUPPORT_TEAM', 'helpdesk_support_team');
define('TABLE_HELPDESK_REQUEST_TYPE', 'helpdesk_request_type');
define('TABLE_MST_HELPDESK_TICKET', 'helpdesk_ticked_records');
define('TABLE_MST_HELPDESK_TICKET_TRANS', 'helpdesk_transaction_tkrecords');
define('TABLE_MST_HELPDESK_PARTNER_TABLE', 'helpdesk_partner_table'); 

//27-Feb-18
define('TABLE_RECRUITMENT','recruit_job_allocation');
define('TABLE_EMP_BRAND','tbl_emp_brand');

//27-Feb-18

define("SITENAME", "ARK INFO SOLUTIONS PVT LTD");
define('SQL_DATETIME','%d-%m-%y %H:%i');
define("COPYRIGHT_TEXT1", "Copyright &copy;  ".date('Y')." ". SITE_NAME . ".</strong> All rights reserved.");

define("ADMIN_NAME", "ARK INFO SOLUTIONS PVT LTD");
define("ADMIN_EMAIL", "semwal.manish1@gmail.com");
define("EMAILTO", "semwal.manish1@gmail.com");

//8-sep-17
define("UPLOADS_URL_NEW", IMAGEPATHTEST."uploads/");
define('UPLOADS_NEW', IMAGEPATHTEST.'uploads/');
//8-sep-17
//8-sep-17
define('CANDIDATEDOCUMENT_NEW', UPLOADS_NEW);
define('CANDIDATEDOCUMENT_URL_NEW', UPLOADS_URL_NEW);
define('ADHARCARD_NEW',UPLOADS_NEW.'adharCard/');
define('ADHARCARD_URL_NEW',UPLOADS_URL_NEW.'adharCard/');
define('MRF_PAN',UPLOADS_NEW.'panCard/');
define('MRF_PAN_URL','panCard/');
define('MRF_ADHARCARD_URL','latestSalarySlip/');
define('MRF_RESUME_URL','resume/');

define("TABLE_USER", "tbl_mst_users");

define("TABLE_TEST", "tbl_test_details");

define('TABLE_HANDBOOK', 'tbl_handbook');
define('TABLE_KRA', 'tbl_kra_mst');
define('TABLE_KRA_EMP', 'tbl_kra_emp');
define('TABLE_KRA_ATTRIBUTES', 'tbl_kra_attributes');
//12-jan-17

//13-dec-17
define('REGIONAL_HR_HEAD_ROLE_ID', 4);
define('HR_HEAD_ROLE_ID', 3);
define('TABLE_PREV_SAL', 'tbl_candidate_prev_sal');
define('TABLE_ADDITIONAL', 'tbl_candidate_additional_det');
define('TABLE_EMP_ADDITIONAL', 'tbl_employee_additional_det');
 
// 16-jan-2018
$financialYearStartFromDate = (date('m') > 3 ? date('Y-'.'04-01') : (date('Y')-1).'-04-01' );
$financialYearEndsOnDate = (date('m') < 4 ? date('Y-'.'03-31') : (date('Y')+1).'-03-31' );
define('financialYearStartFromDate' , $financialYearStartFromDate);
define('financialYearEndsOnDate'  , $financialYearEndsOnDate);
if (date('m') <= 3) {//Upto March 
    $financial_year = (date('Y')-1) . '-' . date('y');
} else {//After March 
    $financial_year = date('Y') . '-' . (date('y') + 1);
}
define('FINANCIAL_YEAR'  , $financial_year);
define('HR_DESG' , 17);
define('PM_DESG' , 19);

// 13-nov-2018
define('TABLE_MRF', 'job_mrf');
define('TABLE_IAF', 'candidate_iaf_form');
define('TABLE_CAND_ROUND', 'candidate_selection_round');

define('REPLACEMENT_EMP', 1);
define('NEW_EMP', 2);
define('INTERN', 3);
define('CONTRACTUAL', 4);
define('RETAILER', 5);



define('TABLE_CAND_MRF', 'candidate_mrf_details');
define('CANDCV',UPLOADS.'interview/resume');
define('UPLOADPC',UPLOADS.'policy');
define('UPLOADPCLOGO',UPLOADS.'policy-logo');
define('TABLE_FLEXI','flexi_attributes');
define('TABLE_FLEXI_EMP_BUDGET','flexi_emp_monthly_budget');
define('TABLE_EMP_FLEXI_BUDGET','employee_flex_budget');
define('TABLE_FLEXI_INITIATE','flexi_initiate');
define('TABLE_EMP_CLAIM_BUDGET','emp_claim_flexi_budget');
define('TABLE_EMP_CLAIM_FLEX_ATTR_BUDGET','emp_claim_flex_attr_amt');
define('TABLE_ANNOUNCEMENT','announcement');
define('TABLE_GET_OUT_JAIL','get_out_jail');
define('HELPDESK',UPLOADS.'helpdesk');
define('HELPDESKSUPPORT',UPLOADS.'helpdesksupport');
define('TABLE_CAND_POLICY','tbl_candidate_policy');


define('CONVEYANCEREPORTPDF', UPLOADS.'conveyancereport/');
define('TABLE_ANNOUNCEMENT_COMMENTS','announcement_comments');
// general awareness
define('UPLOADGA',UPLOADS.'general_awareness_pdf');
define('UPLOADGALOGO',UPLOADS.'general_awareness_logo');

define('TABLE_DIVISION','tbl_mst_division');
define('TABLE_BRAND','tbl_mst_brand');
define('TABLE_GRADE','mst_grade');


/* End of file constants.php */
/* Location: ./application/config/constants.php */
