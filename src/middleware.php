<?php

$app->add(function($request, $response, $next) {
	$uri = $request->getUri();
  if(preg_match('/~.*\/(.*)/', $uri, $newuri) != 0){
    $request = $request->withUri($uri->withPath('/' . $newuri[1]));
  }
  $response = $next($request, $response);

	return $response;
});
