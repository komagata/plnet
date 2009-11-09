<?

// ----------------------------------------------------------------------------------
// Class: ClientQuery
// ----------------------------------------------------------------------------------

/**
* ClientQuery Object to run a SPARQL Query against a SPARQL server.
*
* @version  $Id: ClientQuery.php,v 1.1 2006/06/21 09:04:37 tgauss Exp $
* @author   Tobias Gau� <tobias.gauss@web.de>
*
* @package sparql
*/

class ClientQuery extends Object {

	var $default  = array();
	var $named    = array();
	var $prefixes = array();
	var $query;

	/**
	* Adds a default graph to the query object.
	*
	* @param String  $default  default graph name
	*/
	function addDefaultGraph($default){
		if(!in_array($named,$this->default))
		$this->default[] = $default;
	}
	/**
	* Adds a named graph to the query object.
	*
	* @param String  $default  named graph name
	*/
	function addNamedGraph($named){
		if(!in_array($named,$this->named))
		$this->named[] = $named;
	}
	/**
	* Adds the SPARQL query string to the query object.
	*
	* @param String  $query the query string
	*/
	function query($query){
		$this->query = $query;
	}

}




?>