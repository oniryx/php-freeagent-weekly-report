<?php 

require __DIR__.'/../config.php';
require __DIR__.'/../vendor/autoload.php';

$app = new \Slim\Slim(array(
  'templates.path'  => __DIR__.'/../templates',
));
$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => '1 day',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'fa_session',
    'secret' => 'LFh9lBg2X_hLRou9hRawjQEZL2obzoNh9ivzsTHlegEw',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));

// OAUTH CLIENT
const API_ROOT                = 'https://api.freeagent.com/v2/';
const AUTHORIZATION_ENDPOINT  = 'https://api.freeagent.com/v2/approve_app';
const TOKEN_ENDPOINT          = 'https://api.freeagent.com/v2/token_endpoint';

$client = new OAuth2\Client(CLIENT_ID, CLIENT_SECRET);
$client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);
$client->setCurlOption(CURLOPT_USERAGENT, 'PHP Script');

$app->client = $client;

// CACHE 
define('CACHE_FILENAME', __DIR__.'/../cache.data');
$cache = null;
if(file_exists(CACHE_FILENAME)) {
  $cache = unserialize(file_get_contents(CACHE_FILENAME));
}
$app->cache = $cache;

$app->get('/', function() use ($app) {
  $auth_url = $app->client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI);
  $app->response()->redirect($auth_url);
});

$app->get('/dashboard', function() use ($app)  {
  if(!isset($_SESSION['access_token'])) {
    $code = $app->request()->get('code');
    if(is_null($code)) {
      $auth_url = $app->client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI);
      $app->response()->redirect($auth_url);
    }
    $params = array('code' => $code, 'redirect_uri' => REDIRECT_URI, 'user-agent' => 'PHP Script');
    $response = $app->client->getAccessToken(TOKEN_ENDPOINT, 'authorization_code', $params);
    $_SESSION['access_token'] = $response['result']['access_token'];
  }
  $app->client->setAccessToken($_SESSION['access_token']);

  // LOAD & CACHE
  if(is_null($app->cache) || $app->request()->get('reload')) {
    // LOAD PROJECTS
    $app->cache['projects'] = array();
    $response = $app->client->fetch(API_ROOT.'projects');
    foreach($response['result']['projects'] as $p) {
      $project_id = basename($p['url']);
      $app->cache['projects'][ $project_id ] = $p;
    }
    // LOAD TASKS
    $app->cache['tasks'] = array();
    foreach($app->cache['projects'] as $p_id=>$p) {
      $response = $app->client->fetch(API_ROOT.'tasks?project='.$p_id);
      foreach($response['result']['tasks'] as $t) {
        $task_id = basename($t['url']);
        $app->cache['tasks'][ $task_id ] = $t;
      }
    }
    // LOAD CONTACTS
    $app->cache['contacts'] = array();
    $response = $app->client->fetch('https://api.freeagent.com/v2/contacts');
    foreach($response['result']['contacts'] as $c) {
      $contact_id = basename($c['url']);
      $app->cache['contacts'][ $contact_id ] = $c;
    }

    file_put_contents(CACHE_FILENAME, serialize($app->cache));
  }

  $date_from  = $app->request()->get('date_from');
  $date_from  = is_null($date_from) ? date('Y-m-d', strtotime('this week monday')) : $date_from;
  $date_to    = $app->request()->get('date_to');
  $date_to    = is_null($date_to) ? date('Y-m-d', strtotime('this week sunday')) : $date_to;
  $response   = $app->client->fetch(API_ROOT.'timeslips?from_date='.$date_from.'&to_date='.$date_to);
  $timeslips  = $response['result']['timeslips'];
  $total      = array('b'=>0, 't'=>0);
  $hours      = array();
  foreach($timeslips as $t) {
    $hours[ basename($t['project']) ] = array('b' => 0, 't' => 0);
  }
  foreach($timeslips as $t) {
    if($app->cache['tasks'][ basename($t['task']) ]['is_billable']) {
      $hours[ basename($t['project']) ][ 'b' ] += $t['hours'];
      $total['b']+= $t['hours'];
    }
    $hours[ basename($t['project']) ][ 't' ] += $t['hours'];
    $total['t']+= $t['hours'];
  }

  $app->render('dashboard.php', array(
    'date_from' => $date_from,
    'date_to'   => $date_to,
    'hours'     => $hours,
    'total'     => $total,
    'projects'  => $app->cache['projects'],
    'contacts'  => $app->cache['contacts'],
  ));

});

$app->run();
