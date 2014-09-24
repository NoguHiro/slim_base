<?php
require_once './vendor/Slim/Slim.php';
require_once './vendor/Idiorm/idiorm.php';
require_once './vendor/Session/session.php';
require_once './config/Con.php';
require_once './config/database.php';
require_once './library/Auth.php';

try {
    \Slim\Slim::registerAutoloader();
    Session::load();
    Auth::check();
    DatabaseConfig::load(Con::get('database'));
    $app = new \Slim\Slim();
    $app->config(array(
        'templates.path' => Con::get('template.path')
    ));
} catch (Exception $e) {
    echo $e->getMessage();
    unset($app);
    Session::close();
    exit();
}

# root
$app->get('/', function() use($app) {
    Auth::redirect_not_logged($app);
    $api = new MetroApi();
    $railways = $api->get_cache_railways_to_stations();
    $app->render('share/_header.php');
    $app->render('entry.php', array('image' => Con::image('test.jpg')));
    $app->render('json/_railways.php', array('railways' => $railways));
    $app->render('share/_footer.php');
});

# GET /login
$app->get('/login', function() use($app) {
    Auth::logged(function() use($app) {
        $app->redirect(Con::get('app_url'));
    }, function() use($app) {
        $app->render('login.php', array('title' => '秘密のページ'));
    });
});

# POST /login
$app->post('/login', function() use($app) {
    Auth::login(function() use($app) {
        Session::set(
            Con::get('admin.session_name'),
            Con::get('admin.secure_key')
        );
        $app->redirect(Con::get('app_url'));
    }, function($errors) use($app) {
        $app->flash('error', implode('', $errors));
        $app->redirect(Con::get('app_url') . '/login');
    });
});


# GET /logout
$app->get('/logout', function() use($app) {
    Auth::logout();
    $app->redirect(Con::get('app_url') . '/login');
});

function now()
{
    return date('Y-m-d h:i:s');
}

$app->run();
