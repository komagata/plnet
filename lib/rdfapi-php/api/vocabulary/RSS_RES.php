<?PHP
// ----------------------------------------------------------------------------------
// RSS Vocabulary (ResResource)
// ----------------------------------------------------------------------------------
// @version                  : $Id: RSS_RES.php,v 1.4 2006/05/15 05:24:37 tgauss Exp $
// Authors                   : Tobias Gau� (tobias.gauss@web.de)
//
// Description               : Wrapper, defining resources for all terms of RSS.
//							   For details about RSS see: http://purl.org/rss/1.0/.
// 							   Using the wrapper allows you to define all aspects of
//                             the vocabulary in one spot, simplifing implementation and
//                             maintainence. 
//
// ----------------------------------------------------------------------------------


class RSS_RES{

	function CHANNEL()
	{
		return  new ResResource(RSS_NS . 'channel');

	}

	function IMAGE()
	{
		return  new ResResource(RSS_NS . 'image');

	}

	function ITEM()
	{
		return  new ResResource(RSS_NS . 'item');

	}

	function TEXTINPUT()
	{
		return  new ResResource(RSS_NS . 'textinput');

	}

	function ITEMS()
	{
		return  new ResResource(RSS_NS . 'items');

	}

	function TITLE()
	{
		return  new ResResource(RSS_NS . 'title');

	}

	function LINK()
	{
		return  new ResResource(RSS_NS . 'link');

	}

	function URL()
	{
		return  new ResResource(RSS_NS . 'url');

	}

	function DESCRIPTION()
	{
		return  new ResResource(RSS_NS . 'description');

	}

	function NAME()
	{
		return  new ResResource(RSS_NS . 'name');
	}
}


?>