<?php
// ---------------------------------------------
// Class: SparqlParser
// ---------------------------------------------

/**
* Parses a SPARQL Query string and returns a Query Object.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
*
* @package sparql
*/
Class SparqlParser extends Object{
 
	/**
	* @var Query The query Object
	*/
	private $query;

	/**
	* @var string The Querystring
	*/
	private $querystring;

	/**
	* @var array The tokenized Query
	*/
	private $tokens = array();

	/**
	* @var int Last parsed graphPattern
	*/
	private $tmp;

	/**
	* @var array Operators introduced by sparql
	*/
	private $sops = array('regex','bound','isuri','isblank','isliteral','str','lang','datatype','langmatches');



	/**
	* Constructor of SparqlParser
	*/
	public function SparqlParser(){
		$this->query          = new Query();
		$this->querystring    = null;
		// add the default prefixes defined in constants.php
		global $default_prefixes;
		$this->query->prefixes = $default_prefixes;
	}

	/**
	* Main function of SparqlParser. Parses a query string.
	*
	* @param  String $queryString The SPARQL query 
	* @return Query  The query object
	* @throws SparqlParserException
	*/
	public function parse($queryString = false){
		try{
			if($queryString){
				$uncommentedQuery = $this->uncomment($queryString);
				$this->tokenize($uncommentedQuery);
				$this->querystring = $uncommentedQuery;
				$this->parseQuery();
			}else{
				throw new SparqlParserException("Querystring is empty.",null,key($this->tokens));
				$this->query->isEmpty = true;
			}
			return $this->query;
		}catch(SparqlParserException $e){
			$this->error($e);
			return false;
		}
	}


	/**
	* Tokenizes the querystring.
	*
	* @param  String $queryString
	* @return void
	*/
	protected function tokenize($queryString){
		$queryString = trim($queryString);
		$specialChars = array (" ", "\t", "\r", "\n", ",", "(", ")","{","}",'"',"'",";","[","]");
		$len = strlen($queryString);
		$this->tokens[0]='';
		$n = 0;
		for ($i=0; $i<$len; ++$i) {
			if (!in_array($queryString{$i}, $specialChars))
			$this->tokens[$n] .= $queryString{$i};
			else {
				if ($this->tokens[$n] != '')
				++$n;
				$this->tokens[$n] = $queryString{$i};
				$this->tokens[++$n] = '';
			}
		}
	}


	/**
	* Removes comments in the query string. Comments are
	* indicated by '#'.
	*
	* @param  String $queryString
	* @return String The uncommented query string
	*/
	protected function uncomment($queryString){
		// php appears to escape quotes, so unescape them
  		$queryString = str_replace('\"',"'",$queryString);
  		$queryString = str_replace("\'",'"',$queryString);
		
		$regex ="/((\"[^\"]*\")|(\'[^\']*\')|(\<[^\>]*\>))|(#.*)/";
		return preg_replace($regex,'\1',$queryString);
	}

	/**
	* Starts parsing the tokenized SPARQL Query.
	*
	* @return void
	*/
	protected function parseQuery() {
		do{
			switch(strtolower(current($this->tokens))){
				case "base":
				$this->parseBase();
				break;
				case "prefix":
				$this->parsePrefix();
				break;
				case "select":
				$this->parseSelect();
				break;
				case "describe":
				$this->parseDescribe();
				break;
				case "ask":
				$this->parseAsk();
				break;
				case "from":
				$this->parseFrom();
				break;
				case "construct":
				$this->parseConstruct();
				break;
				case "where":
				$this->parseWhere();
				$this->parseModifier();
				break;
				case "{":
				prev($this->tokens);
				$this->parseWhere();
				$this->parseModifier();
				break;
			}
		}while(next($this->tokens));

	}

	/**
	* Parses the BASE part of the query.
	* 
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseBase(){
		$this->_fastForward();
		if($this->iriCheck(current($this->tokens))){
			$this->query->setBase(current($this->tokens));
		}else{
			$msg = current($this->tokens);
			$msg = preg_replace('/</', '&lt;', $msg);
			throw new SparqlParserException("IRI expected ",null,key($this->tokens));
		}
	}

	/**
	* Adds a new namespace prefix to the query object.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parsePrefix(){
		$this->_fastForward();
		$prefix = substr(current($this->tokens),0,-1);
		$this->_fastForward();
		if($this->iriCheck(current($this->tokens))){
			$uri = substr(current($this->tokens),1,-1);
			$this->query->addPrefix($prefix,$uri);
		}else{
			$msg = current($this->tokens);
			$msg = preg_replace('/</', '&lt;', $msg);
			throw new SparqlParserException("IRI expected ",null,key($this->tokens));
		}

	}

	/**
	* Parses the SELECT part of a query.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseSelect(){
		while(strtolower(current($this->tokens))!='from'& strtolower(current($this->tokens))!='where'& strtolower(current($this->tokens))!="{"){
			$this->_fastForward();
			if($this->varCheck(current($this->tokens))|strtolower(current($this->tokens))=='*'){
				$this->query->addVariable(current($this->tokens));
				if(!$this->query->getResultForm())
				$this->query->setResultForm('select');
			}else{
				if(strtolower(current($this->tokens))=='distinct'){
					$this->query->setResultForm('select distinct');
					$this->_fastForward();
					if($this->varCheck(current($this->tokens))|strtolower(current($this->tokens))=='*'){
						$this->query->addVariable(current($this->tokens));
					}else{
						throw new SparqlParserException ("Variable or '*' expected.",null,key($this->tokens));
					}
				}
			}

			if(!current($this->tokens)){
				throw new SparqlParserException("Unexpected end of File. ",null,key($this->tokens));
				break;
			}
		}
		prev($this->tokens);
	}


	/**
	* Adds a new variable to the query and sets result form to 'DESCRIBE'.
	*
	* @return void
	*/
	protected function parseDescribe(){
		while(strtolower(current($this->tokens))!='from'& strtolower(current($this->tokens))!='where'){
			$this->_fastForward();
			if($this->varCheck(current($this->tokens))|$this->iriCheck(current($this->tokens))){
				$this->query->addVariable(current($this->tokens));
				if(!$this->query->getResultForm())
					$this->query->setResultForm('describe');
			}
			if(!current($this->tokens))
			break;
		}
		prev($this->tokens);
	}

	/**
	* Sets result form to 'ASK'.
	*
	* @return void
	*/
	protected function parseAsk(){
		$this->query->setResultForm('ask');
		$this->_fastForward();
		if(current($this->tokens)=="{")
			$this->_rewind();
		$this->parseWhere();
		$this->parseModifier();
	}

	/**
	* Parses the FROM clause.
	*
	* @reutrn void
	* @throws SparqlParserException
	*/
	protected function parseFrom(){
		$this->_fastForward();
		if(strtolower(current($this->tokens))!='named'){
			if($this->iriCheck(current($this->tokens))||$this->qnameCheck(current($this->tokens))){
				$this->query->addFrom(new Resource(substr(current($this->tokens),1,-1)));
			}else if($this->varCheck(current($this->tokens))){
				$this->query->addFrom(current($this->tokens));
			}else{
				throw new SparqlParserException("Variable, Iri or qname expected in FROM ",null,key($this->tokens));
			}
			$this->query->addFrom(current($this->tokens));
		}else{
			$this->_fastForward();
			if($this->iriCheck(current($this->tokens))||$this->qnameCheck(current($this->tokens))){
				$this->query->addFromNamed(new Resource(substr(current($this->tokens),1,-1)));
			}else if($this->varCheck(current($this->tokens))){
				$this->query->addFromNamed(current($this->tokens));
			}else{
				throw new SparqlParserException("Variable, Iri or qname expected in FROM NAMED ",null,key($this->tokens));
			}
		}
	}
	
	
	/**
	* Parses the CONSTRUCT clause.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseConstruct(){
		$this->_fastForward();
		$this->query->setResultForm('construct');
		if(current($this->tokens)=="{"){
			$this->parseGraphPattern(false,false,false,true);
		}else{
			throw new SparqlParserException("Unable to parse CONSTRUCT part. '{' expected. ",null,key($this->tokens));
		}
		$this->parseWhere();
		$this->parseModifier();
	}
	

	/**
	* Parses the WHERE clause.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseWhere(){
		$this->_fastForward();
		if(current($this->tokens)=="{"){
			$this->parseGraphPattern();
		}else{
			throw new SparqlParserException("Unable to parse WHERE part. '{' expected in Query. ",null,key($this->tokens));
		}
	}

	

	/**
	* Checks if $token is a variable.
	*
	* @param  String  $token The token
	* @return boolean TRUE if the token is ja variable false if not
	*/
	protected function varCheck($token){
		if($token{0}=='$'|$token{0}=='?'){
			$this->query->addVar($token);
			return true;
		}
		return false;
	}

	/**
	* Checks if $token is an IRI.
	*
	* @param  String  $token The token
	* @return boolean TRUE if the token is an IRI false if not 	
	*/
	protected function iriCheck($token){
		$pattern="/^<[^>]*>\.?$/";
		if(preg_match($pattern,$token)>0)
		return true;
		return false;
	}


	/**
	* Checks if $token is a Blanknode.
	*
	* @param  String  $token The token
	* @return boolean TRUE if the token is BNode false if not 	
	*/
	protected function bNodeCheck($token){
		if($token{0} == "_")
		return true;
		else
		return false;
	}


	/**
	* Checks if $token is a qname.
	*
	* @param  String  $token The token
	* @return boolean TRUE if the token is a qname false if not 	
	* @throws SparqlParserException
	*/
	protected function qnameCheck($token){
		$result = false;
		$pattern="/^([^:^\<]*):([^:]*)$/";
		if(preg_match($pattern,$token,$hits)>0){
			$prefs = $this->query->getPrefixes();
			if(isset($prefs{$hits{1}}))
			return true;
			if($hits{1}=="_")
			return true;
			throw new SparqlParserException("Unbound Prefix: <i>".$hits{1}."</i>",null,key($this->tokens));
		}else{
			$result = false;
		}
		return $result;
	}


	/**
	* Checks if $token is a Literal.
	*
	* @param  String  $token The token
	* @return boolean TRUE if the token is a Literal false if not 	
	*/
	protected function literalCheck($token){
		$pattern="/^[\"\'].*$/";
		if(preg_match($pattern,$token)>0)
		return true;
		return false;
	}

	/**
	* FastForward until next token which is not blank.
	*
	* @return void
	*/
	protected function _fastForward(){
		next($this->tokens);
		while(current($this->tokens)==" "|current($this->tokens)==chr(10)|current($this->tokens)==chr(13)|current($this->tokens)==chr(9)){
			next($this->tokens);
		}
		return;
	}

	/**
	* Rewind until next token which is not blank.
	*
	* @return void
	*/
	protected function _rewind(){
		prev($this->tokens);
		while(current($this->tokens)==" "|current($this->tokens)==chr(10)|current($this->tokens)==chr(13)|current($this->tokens)==chr(9)){
			prev($this->tokens);
		}
		return;
	}

	/**
	* Parses a graph pattern.
	*
	* @param  int     $optional Optional graph pattern
	* @param  int     $union    Union graph pattern
	* @param  string  $graph    Graphname
	* @param  boolean $constr   TRUE if the pattern is a construct pattern
	* @return void
	*/
	protected function parseGraphPattern($optional = false, $union = false, $graph = false,$constr = false, $external = false){
		$pattern = $this->query->getNewPattern($constr);
		if(is_int($optional)){
			$pattern->addOptional($optional);
		}else{
			$this->tmp = $pattern->getId();
		}
		if(is_int($union)){
			$pattern->addUnion($union);
		}
		if($graph != false){
			$pattern->setGraphname($graph);
		}

		$this->_fastForward();

		do{
			switch(strtolower(current($this->tokens))){
				case "graph":
				$this->parseGraph();
				break;
				case "union":
				$this->_fastForward();
				$this->parseGraphPattern(false,$this->tmp);
				break;
				case "optional":
				$this->_fastForward();
				$this->parseGraphPattern($this->tmp,false);
				break;
				case "filter":
				$this->parseConstraint(&$pattern,true);
				$this->_fastForward();
				break;
				case ".":
				$this->_fastForward();
				break;
				case "{":
				$this->parseGraphPattern(false,false);
				break;
				case "}":
				$pattern->open = false;
				break;
				default:
				$this->parseTriplePattern(&$pattern);
				break;
			}
		}while($pattern->open);
		if($external)
			return $pattern;
		$this->_fastForward();
	}
	/**
	* Parses a triple pattern.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	protected function parseTriplePattern($pattern){
		$trp      = Array();
		$prev     = false;
		$prevPred = false;
		$cont     = true;
		$sub      = "";
		$pre      = "";
		$tmp      = "";
		$tmpPred  = "";
		$obj      = "";
		do{
			switch(strtolower(current($this->tokens))){
				case false:
				$cont          = false;
				$pattern->open = false;
				break;
				case "filter":
				$this->parseConstraint(&$pattern,false);
				$this->_fastForward();
				break;
				case "optional":
				$this->_fastForward();
				$this->parseGraphPattern($pattern->getId(),false);
				$cont = false;
				break;
				case ";":
				$prev = true;
				$this->_fastForward();
				break;
				case ".":
				$prev = false;
				$this->_fastForward();
				break;
				case "graph":
				$this->parseGraph();
				break;
				case ",":
				$prev     = true;
				$prevPred = true;
				$this->_fastForward();
				break;
				case "}":
				$prev = false;
				$pattern->open = false;
				$cont = false;
				break;
				case "[":
				$prev = true;
				$tmp  = $this->parseNode($this->query->getBlanknodeLabel());
				$this->_fastForward();
				break;
				case "]":
				$prev = true;
				$this->_fastForward();
				break;
				case "(":
				$prev = true;
				$tmp = $this->parseCollection(&$trp);
				$this->_fastForward();
				break;
				case false:
				$cont = false;
				$pattern->open = false;
				break;
				default:
				if($prev){
					$sub = $tmp;
				}else{
					$sub = $this->parseNode();
					$this->_fastForward();
					$tmp     = $sub;
				}
				if($prevPred){
					$pre = $tmpPred;
				}else{
					$pre = $this->parseNode();
					$this->_fastForward();
					$tmpPred = $pre;
				}
				if(current($this->tokens)=="["){
					$tmp  = $this->parseNode($this->query->getBlanknodeLabel());
					$prev = true;
					$obj = $tmp;
				}else if(current($this->tokens)=="("){
					$obj = $this->parseCollection(&$trp);
					
				}else{
					$obj = $this->parseNode();
				}
				$trp[] = new QueryTriple($sub,$pre,$obj);
				$this->_fastForward();
				break;

			}
		}while($cont);
		if(count($trp)>0){
			$pattern->addTriplePattern($trp);
		}
	}
	
	
	
	
	/**
	* Parses a value constraint.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	protected function parseConstraint($pattern,$outer){
		$constraint = new Constraint();
		$constraint->setOuterFilter($outer);
		$this->_fastForward();
		if(current($this->tokens)=='('){
			$this->parseBrackettedExpression(&$constraint);
		}else{
			$this->parseExpression(&$constraint);
		}
		$pattern->addConstraint(&$constraint);

	}
	/**
	* Parses a bracketted expression.
	*
	* @param  Constraint $constraint
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseBrackettedExpression($constraint){
		$open = 1;
		$exp = "";
		$this->_fastForward();
		while($open !=0 & current($this->tokens)!= false){
			switch(current($this->tokens)){
				case "(":
				$open++;
				$exp = $exp.current($this->tokens);
				break;
				case ")":
				$open--;
				if($open != 0){
					$exp = $exp.current($this->tokens);
				}
				break;
				case false:
				throw new SparqlParserException ("Unexpected end of query.",null,key($this->tokens));
				default:
				$exp = $exp.current($this->tokens);
				break;
			}
			next($this->tokens);
		}
		$constraint->addExpression($exp);
	}


	/**
	* Parses an expression.
	*
	* @param  Constraint  $constrain
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseExpression($constraint){
		$exp = "";
		while(current($this->tokens)!= false && current($this->tokens)!= "}"){
			switch(current($this->tokens)){
				case false:
				throw new SparqlParserException ("Unexpected end of query.",null,key($this->tokens));
				case ".":
				break;
				break;
				default:
				$exp = $exp.current($this->tokens);
				break;
			}
			next($this->tokens);
		}
		$constraint->addExpression($exp);
	}

	/**
	* Parses a GRAPH clause.
	*
	* @param  GraphPattern $pattern
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseGraph(){
		$this->_fastForward();
		$name = current($this->tokens);
		if(!$this->varCheck($name)&!$this->iriCheck($name)&&!$this->qnameCheck($name)){
			$msg = $name;
			$msg = preg_replace('/</', '&lt;', $msg);
			throw new SparqlParserException(" IRI or Var expected. ",null,key($this->tokens));
		}
		$this->_fastForward();
		
		if($this->iriCheck($name)){
			$name = new Resource(substr($name,1,-1));
		}else if($this->qnameCheck($name)){
			$name = new Resource($this->getFN($name));
		}
		$this->parseGraphPattern(false,false,$name);
		if(current($this->tokens)=='.')
		$this->_fastForward();
	}

	/**
	* Parses the solution modifiers of a query.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseModifier(){
		do{
			switch(strtolower(current($this->tokens))){
				case "order":
				$this->_fastForward();
				if(strtolower(current($this->tokens))=='by'){
					$this->_fastForward();
					$this->parseOrderCondition();
				}else{
					throw new SparqlParserException("'BY' expected.",null,key($this->tokens));
				}
				break;
				case "limit":
				$this->_fastForward();
				$val = current($this->tokens);
				$this->query->setSolutionModifier('limit',$val);
				break;
				case "offset":
				$this->_fastForward();
				$val = current($this->tokens);
				$this->query->setSolutionModifier('offset',$val);
				break;
				default:
				break;
			}
		}while(next($this->tokens));
	}

	/**
	* Parses order conditions of a query.
	*
	* @return void
	* @throws SparqlParserException
	*/
	protected function parseOrderCondition(){
		$valList = array();
		$val = array();
		while(strtolower(current($this->tokens))!='limit'
		& strtolower(current($this->tokens))!= false
		& strtolower(current($this->tokens))!= 'offset'){
			switch (strtolower(current($this->tokens))){
				case "desc":
				$this->_fastForward();
				$this->_fastForward();
				if($this->varCheck(current($this->tokens))){
					$val['val'] = current($this->tokens);
				}else{
					throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
				}
				$this->_fastForward();
				if(current($this->tokens)!=')')
				throw new SparqlParserException("missing ')' in ORDER BY clause.",null,key($this->tokens));
				$val['type'] = 'desc';
				$this->_fastForward();
				break;
				case "asc" :
				$this->_fastForward();
				$this->_fastForward();
				if($this->varCheck(current($this->tokens))){
					$val['val'] = current($this->tokens);
				}else{
					throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
				}
				$this->_fastForward();
				if(current($this->tokens)!=')')
				throw new SparqlParserException("missing ')' in ORDER BY clause.",null,key($this->tokens));
				$val['type'] = 'asc';
				$this->_fastForward();
				break;
				default:
				if($this->varCheck(current($this->tokens))){
					$val['val'] = current($this->tokens);
					$val['type'] = 'asc';
				}else{
					throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
				}
				$this->_fastForward();
				break;
			}
			$valList[] = $val;
		}
		prev($this->tokens);
		$this->query->setSolutionModifier('order by',$valList);
	}

	/**
	* Parses a String to an RDF node.
	*
	* @param  String $node 
	* @return Node   The parsed RDF node
	* @throws SparqlParserException
	*/
	protected function parseNode($node = false){
		$eon = false;
		if($node){
			$node = $node;
		}else{
			$node = current($this->tokens);
		}
		if($node{strlen($node)-1} == '.')
			$node = substr($node,0,-1);
		if($this->dtypeCheck(&$node))
		return $node;
		if($this->bNodeCheck(&$node)){
			$node = '?'.$node;
			$this->query->addVar($node);
			return $node;
		}
		if($node == '['){
			$node = '?'.substr($this->query->getBlanknodeLabel(),1);
			$this->query->addVar($node);
			$this->_fastForward();
				if(current($this->tokens)!=']')
					prev($this->tokens);
			return $node;
		}
		if($this->iriCheck($node)){
			$base = $this->query->getBase();			
			if($base!=null)
				$node = new Resource(substr(substr($base,0,-1).substr($node,1),1,-1));
			else
				$node = new Resource(substr($node,1,-1));
			return $node;
		}elseif ($this->qnameCheck($node)){
			$node = $this->getFN($node);
			$node = new Resource($node);
			return $node;
		}elseif ($this->literalCheck($node)){
			do{
				switch(substr($node,0,1)){
					case '"':
					$this->parseLiteral(&$node,'"');
					$eon = true;
					break;
					case "'":
					$this->parseLiteral(&$node,"'");
					$eon = true;
					break;
				}
			}while(!$eon);

		}elseif ($this->varCheck($node)){
			$pos = strpos($node,'.');
			if($pos)
				return substr($node,0,$pos);
			else 
				return $node;
		}else{
			throw new SparqlParserException($node." is neither a valid rdf- node nor a variable.",null,key($this->tokens));
		}
		return $node;
	}
	
	/**
	* Checks if there is a datatype given and appends it to the node.
	*
	* @param  String $node
	* @return void
	*/
	protected function checkDtypeLang($node){
		$this->_fastForward();
		switch(substr(current($this->tokens),0,1)){
			case '^':
			if(substr(current($this->tokens),0,2)=='^^'){
				$node = new Literal(substr($node,1,-1));			
				$node->setDatatype($this->getFN(substr(current($this->tokens),2)));
			}
			break;
			case '@':
			$node = new Literal(substr($node,1,-1),substr(current($this->tokens),1));
			break;
			default:
			prev($this->tokens);
			$node = new Literal(substr($node,1,-1));
			break;

		}

	}

	/**
	* Parses a literal.
	*
	* @param String $node
	* @param String $sep used separator " or '
	* @return void
	*/
	protected function parseLiteral($node,$sep){
		do{
			next($this->tokens);
			$node = $node.current($this->tokens);
		}while(current($this->tokens)!=$sep);
		$this->checkDtypeLang(&$node);
	}

	/**
	* Checks if the Node is a typed Literal.
	*
	* @param String $node
	* @return boolean TRUE if typed FALSE if not
	*/
	protected function dtypeCheck($node){
		$patternInt = "/^-?[0-9]+$/";
		$match = preg_match($patternInt,$node,$hits);
		if($match>0){
			$node = new Literal($hits[0]);
			$node->setDatatype(XML_SCHEMA.'integer');
			return true;
		}
		$patternBool = "/^(true|false)$/";
		$match = preg_match($patternBool,$node,$hits);
		if($match>0){
			$node = new Literal($hits[0]);
			$node->setDatatype(XML_SCHEMA.'boolean');
			return true;
		}
		$patternType = "/^a$/";
		$match = preg_match($patternType,$node,$hits);
		if($match>0){
			$node = new Resource(RDF_NAMESPACE_URI.'type');
			return true;
		}
		$patternDouble = "/^-?[0-9]+.[0-9]+[e|E]?-?[0-9]*/";
		$match = preg_match($patternDouble,$node,$hits);
		if($match>0){
			$node = new Literal($hits[0]);
			$node->setDatatype(XML_SCHEMA.'double');
			return true;
		}
		return false;
	}
	/**
	* Parses an RDF collection.
	*
	* @param  TriplePattern $trp
	* @return Node          The first parsed label
	*/
	protected function parseCollection($trp){
		$tmpLabel = $this->query->getBlanknodeLabel();
		$firstLabel = $this->parseNode($tmpLabel);
		$this->_fastForward();
		$i = 0;
		while(current($this->tokens)!=")"){
			if($i>0)
			$trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#rest"),$this->parseNode($tmpLabel = $this->query->getBlanknodeLabel()));
			$trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#first"),$this->parseNode());
			$this->_fastForward();
			$i++;
		}
		$trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#rest"),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#nil"));
		return $firstLabel;
	}

	/**
	* Error reporting.
	*
	* @param SparqlException $e
	* @return String
	*/
	protected function error($e){

		echo "<b>SPARQL PARSER ERROR: </b>".$e->getMessage()."<br>
	        In Query: <br><pre>";
		if($e->getPointer())
		$end = $e->getPointer();
		else
		$end = count($this->tokens)-1;

		for($i =0;$i<$end;$i++ ){
			$token = preg_replace('/&/', '&amp;', $this->tokens[$i]);
			$token = preg_replace('/</', '&lt;', $token);
			echo $token;
		}
		$token = preg_replace('/&/', '&amp;', $this->tokens[$end]);
		$token = preg_replace('/</', '&lt;', $token);
		echo "-><b>".$token."</b><-";
		"</pre><br>";
	}

	/**
	* Gets the full URI of a qname token.
	*
	* @param  String $token
	* @return String The complete URI of a given token
	*/
	protected function getFN($token){
		$pattern="/^([^:]*):([^:]*)$/";
		if(preg_match($pattern,$token,$hits)>0){
			$prefs = $this->query->getPrefixes();
			$base = $this->query->getBase();
			
			if(isset($prefs{$hits{1}}))
			return substr($base,1,-1).$prefs{$hits{1}}.$hits{2};
			if($hits{1}=='_')
			return "_".$hits{2};
			$base = $this->query->getBase();
			
				
		}
		
		return false;
	}
	
}// end class: SparqlParser.php




?>
