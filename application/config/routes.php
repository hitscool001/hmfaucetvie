<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['maintenance'] = 'page/maintenance';
$route['payment-proofs'] = 'page/proof';

$route['login'] = 'home/login';
$route['register'] = 'home/register';
$route['forgot-password'] = 'home/forgot_password';

$route['reset-password/(:any)'] = 'auth/reset_password/$1';
$route['logout'] = 'auth/logout';

$route['transfer'] = 'account/transfer';
$route['profile'] = 'account/index';
$route['history'] = 'account/history';
$route['referrals'] = 'account/referrals';

$route['active/(:any)'] = 'active/verify/$1';

$route['page/(:any)'] = 'page/show/$1';

$route['admin'] = 'admin/auth/index';

$route['admin/update/logo'] = 'admin/update/logo';
$route['admin/update/favicon'] = 'admin/update/favicon';
$route['admin/update/(:any)'] = 'admin/update/index/$1';


$route['scripts/faucet.js'] = 'site/faucet';
