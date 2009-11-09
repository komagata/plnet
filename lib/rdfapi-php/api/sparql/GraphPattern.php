<?php
// ---------------------------------------------
// class: GraphPattern
// ---------------------------------------------

/**
* A graph pattern which consists of triple patterns, optional
* or union graph patterns and filters.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
*
* @package sparql
*/
 
Class GraphPattern extends Object{

	/**
	* Graphname. 0 if its in the default graph.
	*/
	private $graphname;

	/**
	* @var array The TriplePattern.
	*/
	private $triplePattern;

	/**
	* @var array A List of Constraints.
	*/
	private $constraint = array();

	/**
	* @var int Pointer to optional patterns.
	*/
	private $optional;

	/**
	* @var int Pointer to union patterns.
	*/
	private $union;

	/**
	* @var boolean TRUE if the pattern is open- FALSE if closed.
	*/
	public $open;

	/**
	* @var boolean TRUE if the GraphPattern is a construct pattern.
	*/
	public $isConstructPattern;


	/**
	* @var int The GraphPatterns id.
	*/
	public $patternId;


	/**
	* Constructor
	*/
	public function GraphPattern(){
		$this->open               = true ;
		$this->isConstructPattern = false;
		$this->constraint         = false;
		$this->triplePattern      = false;
	}

	/**
	* Returns the graphname.
	*
	* @return String
	*/
	public function getGraphname(){
		return $this->graphname;
	}

	/**
	* Returns the triple pattern of the graph pattern.
	*
	* @return Array
	*/
	public function getTriplePattern(){
		return $this->triplePattern;
	}

	/**
	* Returns a constraint if there is one false if not.
	*
	* @return Constraint
	*/
	public function getConstraint(){
		return $this->constraint;
	}

	/**
	* Returns a pointer to an optional graph pattern.
	*
	* @return integer
	*/
	public function getOptional(){
		return $this->optional;
	}

	/**
	* Returns a pointer to a union graph pattern.
	*
	* @return integer
	*/
	public function getUnion(){
		return $this->union;
	}

	/**
	* Sets the graphname.
	*
	* @param  String $name
	* @return void
	*/
	public function setGraphname($name){
		$this->graphname = $name;
	}
	/**
	* Adds a List of QueryTriples to the GraphPattern.
	*
	* @param  array $trpP
	* @return void
	*/
	public function addTriplePattern($trpP){
		$this->triplePattern = $trpP;
	}

	/**
	* Adds a Constraint to the GraphPattern.
	*
	* @param  Constraint $cons
	* @return void
	*/
	public function addConstraint($cons){
		$this->constraint[] = $cons;
	}
	/**
	* Adds a pointer to an optional graphPattern.
	*
	* @param  integer $pattern
	* @return void
	*/
	public function addOptional($pattern){
		$this->optional = &$pattern;
	}

	/**
	* Adds a pointer to a union graphPattern.
	*
	* @param  integer $pattern
	* @return void
	*/
	public function addUnion($pattern){
		$this->union = &$pattern;
	}


	/**
	* Sets the GraphPatterns Id.
	*
	* @param  integer $id
	* @return void
	*/
	public function setId($id){
		$this->patternId = $id;
	}

	/**
	* Returns the GraphPatterns id.
	*
	* @return integer
	*/
	public function getId(){
		return $this->patternId;
	}

}
// end class: GraphPattern.php
?>
