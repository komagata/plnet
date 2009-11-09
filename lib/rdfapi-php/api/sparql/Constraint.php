<?php
// ---------------------------------------------
// class: Constraint.php
// ---------------------------------------------


/**
* Represents a constraint. A value constraint is a boolean- valued expression
* of variables and RDF Terms.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
*
* @package sparql
*/
Class Constraint extends Object{

	/**
	* @var string The expression string.
	*/
	private $expression;

	/**
	* @var boolean True if it is an outer filter, false if not.
	*/
	private $outer;

	/**
	* Adds an expression string.
	*
	* @param  String $exp the expression String
	* @return void
	*/
	public function addExpression($exp){
		$this->expression = $exp;
	}

	/**
	* Returns the expression string.
	*
	* @return String  the expression String
	*/
	public function getExpression(){
		return $this->expression;
	}


	/**
	* Sets the filter type to outer or inner filter.
	* True for outer false for inner.
	*
	* @param  boolean $boolean
	* @return void
	*/
	public function setOuterFilter($boolean){
		$this->outer = $boolean;
	}

	/**
	* Returns true if this constraint is an outer filter- false if not.
	*
	* @return boolean
	*/
	public function isOuterFilter(){
		return $this->outer;
	}

}
// end class: Constraint.php
?>
