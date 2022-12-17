<?php
require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
//routing twig

$page = 'home';
if (isset($_GET['p'])) {
    $page = $_GET['p'];
}

function getAllPosts()
{
    $pdo = new PDO('mysql:dbname=twigdb;host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    $posts = $pdo->query('SELECT * FROM posts');
    return $posts;
}

//render template

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => false
    // 'cache' => __DIR__ . '/tmp'
    // l'enregistre dans un fichier temporaire, sinon false pour pas avoir de soucis pendant la prod
]);
$twig->addFunction(new Twig\TwigFunction('markdown', function ($value) {
    return \Michelf\MarkdownExtra::defaultTransform($value);
}, ['is_safe' => ['html']]));

$twig->addGlobal('page_actuelle', $page);

switch ($page) {
    case 'contact':
        echo $twig->render('contact.twig');
        break;
    case 'home':
        echo $twig->render('home.twig', ['posts' => getAllPosts()]);
        break;
    default:
        header('HTTP/1.0 404 not found');
        echo $twig->render('404.twig');
        break;
}

/*si je call pas la page twig direct, call avec props possible :
// if ($page === 'home') {
    // echo $twig->render('home.twig');
    // echo $twig->render('home.twig', ['person' => [
    //     "name" => "MAAAAARC",
    //     "surname" => "Airlines"
    // ]]);
// }
*/