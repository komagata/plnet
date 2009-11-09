<?php
// ---------------------------------------------
// class: Query
// ---------------------------------------------

/**
* The Class Query represents a SPARQL query.
*
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
*
* @package sparql
*/
 
Class Query extends Object {

	/**
	* @var string The BASE part of the SPARQL query.
	*/
	private $base;

	/**
	* @var array Array that vontains used prefixes and namespaces.
	*/
	public $prefixes = array();

	/**
	* @var array List of result variables.
	*/
	private $resultVars = array();

	/**
	* The result form of the query.
	*/
	private $resultForm;

	/**
	* Contains the result part of the SPARQL query.
	*/
	private $resultPart;

	/**
	* @var array Contains the FROM part of the SPARQL query.
	*/
	private $fromPart = array();

	/**
	* @var array Contains the FROM NAMED part of the SPARQL query.
	*/
	private $fromNamedPart = array();

	/**
	* @var array Optional solution modifier of the query.
	*/
	private $solutionModifier = array();

	/**
	* @var int Blanknode counter.
	*/
	private $bnodeCounter;

	/**
	* @var int GraphPattern counter.
	*/
	public $graphPatternCounter;

	/**
	* @var array List of all vars used in the query.
	*/
	public $usedVars;

	/**
	* If the query type is CONSTRUCT this variable contains the
	* CONSTRUCT graph pattern.
	*/
	private $constructPattern;

	/**
	* @var boolean TRUE if the query is empty FALSE if not.
	*/
	public $isEmpty;


	/**
	* Constructor
	*/
	public function Query(){
		$this->resultForm = false;
		$this->solutionModifier['order by'] = 0;
		$this->solutionModifier['limit']    = 0;
		$this->solutionModifier['offset']   = 0;
		$this->bnodeCounter = 0;
		$this->graphPatternCounter = 0;

	}

	/*
	* Returns the BASE part of the query.
	*
	* @return String
	*/
	public function getBase(){
		return $this->base;
	}

	/*
	* Returns the prefix map of the query.
	*
	* @return Array
	*/
	public function getPrefixes(){
		return $this->prefixes;
	}

	/*
	* Returns a list containing the result vars.
	*
	* @return Array
	*/
	public function getResultVars(){
		return $this->resultVars;
	}

	/*
	* Returns a list containing the result vars.
	*
	* @return Array
	*/
	public function getResultForm(){
		return $this->resultForm;
	}
	/*
	* Returns a list containing the graph patterns of the query.
	*
	* @return Array
	*/
	public function getResultPart(){
		return $this->resultPart;
	}

	/*
	* Returns the FROM clause of the query.
	*
	* @return String
	*/
	public function getFromPart(){
		return $this->fromPart;
	}

	/*
	* Returns the FROM NAMED clause of the query.
	*
	* @return Array
	*/
	public function getFromNamedPart(){
		return $this->fromNamedPart;
	}

	/**
	* Returns an unused Bnode label.
	*
	* @return String
	*/
	public function getBlanknodeLabel(){
		return "_:bN".$this->bnodeCounter++;
	}


	/**
	* Sets the base part.
	*
	* @param String $base
	* @return void
	*/
	public function setBase($base){
		$this->base = $base;
	}


	/**
	* Adds a prefix to the list of prefixes.
	*
	* @param  String $prefix
	* @param  String $label
	* @return void
	*/
	public function addPrefix($prefix, $label){
		$this->prefixes[$prefix]= $label;
	}

	/**
	* Adds a variable to the list of result variables.
	*
	* @param  String $var
	* @return void
	*/
	public function addVariable($var){
		$this->resultVars[]= $var;
	}


	/**
	* Sets the result form.
	*
	* @param  String $form
	* @return void
	*/
	public function setResultForm($form){
		$this->resultForm = $form;
	}

	/**
	* Adds a graph pattern to the result part.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	public function addGraphPattern($pattern){
		$pattern->setId($this->graphPatternCounter);
		$this->resultPart[] = $pattern;
		$this->graphPatternCounter++;
	}

	/**
	* Adds a construct graph pattern to the query.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	public function addConstructGraphPattern($pattern){
		$this->constructPattern = $pattern;
	}


	/**
	* Adds a graphuri to the from part.
	*
	* @param  String $graphURI
	* @return void
	*/
	public function addFrom($graphURI){
		$this->fromPart = $graphURI;
	}

	/**
	* Adds a graphuri to the from named part.
	*
	* @param  String $graphURI
	* @return void
	*/
	public function addFromNamed($graphURI){
		$this->fromNamedPart[] = $graphURI;
	}

	/**
	* Sets a solution modifier.
	*
	* @param  String $name
	* @param  Value  $value
	* @return void
	*/
	public function setSolutionModifier($name, $value){
		$this->solutionModifier[$name] = $value;
	}


	/**
	* Generates a new GraphPattern. If it is a CONSTRUCT graph pattern
	* $constr has to set to TRUE FALSE if not.
	*
	* @param  boolean $constr
	* @return GraphPattern
	*/
	public function getNewPattern($constr = false){
		$pattern = new GraphPattern();
		if($constr)
		$this->addConstructGraphPattern($pattern);
		else
		$this->addGraphPattern($pattern);
		return $pattern;
	}

	/**
	* Adds a new variable to the variable list.
	*
	* @param  String $var
	* @return void
	*/
	public function addVar($var){
		$this->usedVars[$var]=true;
	}

	/**
	* Returns a list with all used variables.
	*
	* @return Array
	*/
	public function getAllVars(){
		return array_keys($this->usedVars);
	}
	
	/**
	* Gets the solution modifiers of the query.
	* $solutionModifier['order by'] = value
	*                  ['limit']    = vlaue
	*                  ['offset']   = value
	*
	*
	* @return Array
	*/
	public function getSolutionModifier(){
		return $this->solutionModifier;
	}


	/**
	* Returns the constcutGraphPattern of the query if there is one.
	*
	* @return GraphPattern
	*/
	public function getConstructPattern(){
		return $this->constructPattern;
	}

}
// end class: Query.php

?>
