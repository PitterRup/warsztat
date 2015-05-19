<?php
header('Content-type: text/html; charset=utf8');

define('PATH', '../');

require_once PATH."library/cms/Debug.php";
require_once PATH."library/cms/Errors.php";
require_once PATH."library/cms/Library.php";
require_once PATH."library/cms/Application.php";
require_once PATH."library/cms/Loader.php";
require_once PATH."library/cms/GeneralModelsController.php";


$application = new Application; 

// ładowanie plików klas 
$application->setLoader();

// $stoper = new Stoper;
// $stoper->start();

// start sesji
$application->sessionStart();

// włączenie śledzenia trasy skryptu
// Debug::on($on=true);

// raportowanie błędów
$application->errorReporting(1);
// wczytanie layoutu w przypadku błędów (domyślnie true)
Errors::$showLayout = false;

// załadowanie obiektów biblioteki
Debug::checkPoint("<b>Załadowanie obiektów biblioteki</b>");
$application->loadLibrary();

// wyłapanie wyjątków dla plików
// Debug::checkPoint("<i>Utworzenie obiektu wyłapania wyjątków</i>");
// $exceptions = new ExceptionsLibrary;
// $exceptions->check();

// przygotowanie aplikacji
Debug::checkPoint("Inicjacja Aplikacji");
$application->init();

// start aplikacji
Debug::checkPoint("<b>Start Aplikacji</b>");
$application->run();

// if(!$application->_request->isAjaxRequest() && $application->_getParam('type')!='noContent') echo $application->processTime = $stoper->stop();

?>