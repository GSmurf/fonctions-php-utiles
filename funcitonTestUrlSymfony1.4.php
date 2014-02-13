<?php

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$tabToChech = array(
    '/testRouting/index' =>     array('testRouting',        'index'),
    '/mouai' =>                 array('testRouting',        'mouai'),
    '/test.html' =>             array('testRouting',        'index'),
    '/route/1' =>               array('testRouting',        'route'),
    '/route' =>                 array('testRouting',        'route'),
);

foreach ($tabToChech as $route => $value) {
    $browser->
      get($route)->

      with('request')->begin()->
        isParameter('module', $value[0])->
        isParameter('action', $value[1])->
      end()->

      with('response')->begin()->
        isStatusCode(200)->/*
        checkElement('body', '!/This is a temporary page/')->*/
      end();
    
}
