<?php
class DefaultIndexView extends View {
	function execute(&$controller, &$request, &$user) {
		$renderer =& RendererUtils::getSmartyRenderer($controller, $request, $user);
        $renderer->setTemplate("Index.html");
		return $renderer;
	}
}