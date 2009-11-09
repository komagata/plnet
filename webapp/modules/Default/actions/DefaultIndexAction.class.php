<?php
class DefaultIndexAction extends Action {
	function execute(& $controller, & $request, & $user) {
		return VIEW_INDEX;
	}
}