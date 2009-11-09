<?php 
// ---------------------------------------------
// Class: QueryTriple
// ---------------------------------------------

/**
* Represents a query triple.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	$Id$
*
* @package sparql
*/
Class QueryTriple extends Object{

	/**
	* The QueryTriples Subject.
	*/
	private $subject;

	/**
	* The QueryTriples Predicate.
	*/
	private $predicate;

	/**
	* The QueryTriples Object.
	*/
	private $object;


	/**
	* Constructor
	*/
	public function QueryTriple($sub,$pred,$ob){
		$this->subject   = $sub;
		$this->predicate = $pred;
		$this->object    = $ob;
	}

	/**
	* Returns the Triples Subject.
	*
	* @return Node
	*/
	public function getSubject(){
		return $this->subject;
	}

	/**
	* Returns the Triples Predicate.
	*
	* @return Node
	*/
	public function getPredicate(){
		return $this->predicate;
	}

	/**
	* Returns the Triples Object.
	*
	* @return Node
	*/
	public function getObject(){
		return $this->object;
	}

}

// end class: QueryTriple.php
?>
