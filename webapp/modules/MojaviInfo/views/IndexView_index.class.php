<?php
//
// MojaviInfo - Program that displays information on Mojavi2.
//
// Copyright (C) 2005 Masaki Komagata <komagata@p0t.jp> 
//     All rights reserved.
//     This is free software with ABSOLUTELY NO WARRANTY.
//
// You can redistribute it and/or modify it under the terms of 
// the GNU General Public License version 2.
//
class IndexView extends View {
	function execute(&$controller, &$request, &$user) {
		$renderer = new Renderer();
		$renderer->setTemplate("Index.php");
		$renderer->setAttribute("configs", $request->getAttribute("configs"));
		$renderer->setAttribute("modules", $request->getAttribute("modules"));
		$renderer->setAttribute("globalFilterList", $request->getAttribute("globalFilterList"));
		$renderer->setAttribute("authorizationHandler", $request->getAttribute("authorizationHandler"));
		$renderer->setAttribute("user", $request->getAttribute("user"));
		$renderer->setAttribute("userContainer", $request->getAttribute("userContainer"));
		$renderer->setAttribute("sessionHandler", $request->getAttribute("sessionHandler"));
		$renderer->setAttribute("loggers", $request->getAttribute("loggers"));
		return $renderer;
	}
}
?>