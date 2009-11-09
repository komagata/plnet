<?PHP
// ----------------------------------------------------------------------------------
// RSS Vocabulary (Resource)
// ----------------------------------------------------------------------------------
// @version                  : $Id: RSS_C.php,v 1.4 2006/05/15 05:24:37 tgauss Exp $
// Authors                   : Tobias Gau� (tobias.gauss@web.de)
//
// Description               : Wrapper, defining resources for all terms of RSS.
//							   For details about RSS see: http://purl.org/rss/1.0/.
// 							   Using the wrapper allows you to define all aspects of
//                             the vocabulary in one spot, simplifing implementation and
//                             maintainence. 
//
// ----------------------------------------------------------------------------------



class RSS{

	function CHANNEL()
	{
		return  new Resource(RSS_NS . 'channel');

	}

	function IMAGE()
	{
		return  new Resource(RSS_NS . 'image');

	}

	function ITEM()
	{
		return  new Resource(RSS_NS . 'item');

	}

	function TEXTINPUT()
	{
		return  new Resource(RSS_NS . 'textinput');

	}

	function ITEMS()
	{
		return  new Resource(RSS_NS . 'items');

	}

	function TITLE()
	{
		return  new Resource(RSS_NS . 'title');

	}

	function LINK()
	{
		return  new Resource(RSS_NS . 'link');

	}

	function URL()
	{
		return  new Resource(RSS_NS . 'url');

	}

	function DESCRIPTION()
	{
		return  new Resource(RSS_NS . 'description');

	}

	function NAME()
	{
		return  new Resource(RSS_NS . 'name');
	}
}



?>