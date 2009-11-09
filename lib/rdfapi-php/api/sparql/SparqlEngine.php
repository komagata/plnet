<?php

// ----------------------------------------------------------------------------------
// Class: SparqlEngine
// ----------------------------------------------------------------------------------

/**
* This engine executes SPARQL queries against an RDF Datatset.
*
* @version  $Id$
* @author   Tobias Gauß <tobias.gauss@web.de>
*
* @package sparql
*/
 
Class SparqlEngine extends Object{


	/**
	* @var Query The query object.
	*/
	private $query;

	/**
	* @var Dataset The RDF Dataset.
	*/
	private $dataset;



	/**
	* The query engines main method.
	*
	* @param  Dataset       $dataset    the RDF Dataset
	* @param  Query         $query      the parsed SPARQL query
	* @param  String        $resultform the result form. If set to 'xml' the result will be
	*                                   SPARQL Query Results XML Format as described in http://www.w3.org/TR/rdf-sparql-XMLres/ .
	* @return Array/String  Type of the result depends on $resultform.
	*/
	public function queryModel($dataset,$query,$resultform = false){
		$this->query   = $query;
		$this->dataset = $dataset;

		if($this->query->isEmpty){
			$vartable[0]['patternResult'] = null;
			return $this->returnResult($vartable,$resultform);
		}

		$graphlist = $this->preselectGraphs();
		/// match graph patterns against the RDF Dataset
		$patternlist = $this->matchPatterns($graphlist);
		// filter results- apply inner filters
		$patternlist = $this->filterPatterns($patternlist,false);
		// join pattern results
		$vartable = $this->joinResults($patternlist);
		// filter results- apply outer filters
		$vartable = $this->filterPatterns($vartable,true);

		return $this->returnResult($vartable,$resultform);

	}

	/**
	* Matches all graph Patterns against the dataset and generates an array which
	* contains the result sets for every given GraphPattern.
	*
	* @param  Array      $graphlist   the graphlist which contains the names of the named
	*                    graphs which has to be queried.
	* @return Array
	*/
	protected function matchPatterns($graphlist){
		$patternlist = array();
		// get the result part from the query
		$resultPart = $this->query->getResultPart();
		// for each GrapPattern in the result part
		if($resultPart)
		foreach($resultPart as $graphPattern){
			$this->matchPattern($patternlist, $graphlist, $graphPattern);
		}
		return $patternlist;
	}


	/**
	* Finds tuples that match one graph pattern.
	*
	* @param  Array        $patternlist list that contains the graphPatterns
	* @param  array        $graphlist   the graphlist
	* @param  GraphPattern $graphPattern the pattern which has to be matched
	* @return void
	*/
	protected function matchPattern(&$patternlist, $graphlist, &$graphPattern) {
		// generate an empty result set
		$finalRes = null;
		// if the GraphPattern has triple patterns
		if($graphPattern->getTriplePattern()>0){
			// check if the pattern has a GRAPH clause and if this Iri is in $graphlist
			$newGraphList = $this->_checkGraphs($graphPattern,$graphlist);
			if($newGraphList){
				$qt = $graphPattern->getTriplePattern();
				$resultSet = $this->findTuplesMatchingOnePattern($qt[0], $newGraphList);
				for ($i=1; $i<count($qt); $i++) {
					$rs = $this->findTuplesMatchingOnePattern($qt[$i], $newGraphList);
					$resultSet = $this->joinTuples($resultSet, $rs);
					if(!$resultSet)
					break;
				}
				if($finalRes != null){
					$finalRes = $this->joinTuples($finalRes,$resultSet);
				}else{
					$finalRes = $resultSet;
				}
			}
		}
		// dependencies between pattern results
		$patternlist[$graphPattern->getId()]['hasOptional']     = 0;
		$patternlist[$graphPattern->getId()]['hasUnion']        = 0;
		$patternlist[$graphPattern->getId()]['patternResult']   = $finalRes;

		$op = $graphPattern->getOptional();
		$un = $graphPattern->getUnion();

		$patternlist[$graphPattern->getId()]['optionalTo']      = $op;
		if(is_int($op))
		$patternlist[$op]['hasOptional']++;

		$patternlist[$graphPattern->getId()]['unionWith']       = $un;
		if(is_int($un))
		$patternlist[$un]['hasUnion']++;

		$constraint = $graphPattern->getConstraint();
		if($constraint != null){
			foreach($constraint as $constr){
				if($constr->isOuterFilter()){
					$patternlist[$graphPattern->getId()]['outerFilter'][]          = $constr;
					$patternlist[$graphPattern->getId()]['innerFilter'][]          = null;
				}else{
					$patternlist[$graphPattern->getId()]['innerFilter'][]          = $constr;
					$patternlist[$graphPattern->getId()]['outerFilter'][]          = null;
				}
			}
		}else{
			$patternlist[$graphPattern->getId()]['innerFilter']          = null;
			$patternlist[$graphPattern->getId()]['outerFilter']          = null;
		}
	}


	/**
	* Finds Tuples matching one TriplePattern.
	*
	* @param  TriplePattern $pattern
	* @param  Array         $graphlist
	* @return Array
	*/
	protected function findTuplesMatchingOnePattern($pattern, $graphlist){
		$var = null;
		$sub  = $pattern->getSubject();
		$pred = $pattern->getPredicate();
		$obj  = $pattern->getObject();

		if(is_string($sub)||$sub instanceof BlankNode){
			if(is_string($sub))
			$var['sub'] = $sub;
			$sub = null;
		}
		if(is_string($pred)||$pred instanceof BlankNode ){
			if(is_string($pred))
			$var['pred'] = $pred;
			$pred = null;
		}
		if(is_string($obj)||$obj instanceof BlankNode){
			if(is_string($obj))
			$var['obj'] = $obj;
			$obj = null;
		}
		$intBindings = $this->_buildIntBindings($var);
		$k = 0;

		$key = 0;
		// search in named graphs
		if($graphlist['var'][0] != null||$graphlist['list'][0] != null){
			foreach($graphlist['list'] as $key => $graphnode){

				// query the dataset
				$it = $this->dataset->findInNamedGraphs($graphnode,$sub,$pred,$obj,false);
				if($it->valid()){
					// add statements to the result list
					while($it->valid()){	
						if($graphnode == null){
							$element = $it->current()->getStatement();
							$grname  = $it->current()->getGraphname();
						}else{
							if($it->current() instanceof Quad)
								$element = $it->current()->getStatement();
							else 
								$element = $it->current();
						
							$grname  = $graphnode;
						}
						if($this->checkIntBindings($element,$intBindings)){
							$resmodel['trip'][$k]  = $element;
							$resmodel['graph'][$k] = $grname;
						//	$resmodel['graphvar'][$k] = $graphlist['var'][$key];
							$resmodel['graphvar'][$k] = $graphlist['var'][0];
							$k++;

						}
						$it->next();
					}
				}

			}
		}
		// search in the default graph
		if($graphlist['list'][0] == null && $graphlist['var'][0] == null){
			
			
			$gr = $this->dataset->getDefaultGraph();
			
			$res = $gr->find($sub,$pred,$obj);
			
			foreach($res->triples as $innerkey => $element){
				if($this->checkIntBindings($element,$intBindings)){
						$resmodel['trip'][$k]  = $element;
						$resmodel['graph'][$k] = null;
						$resmodel['graphvar'][$k] = $graphlist['var'][$key];
						$k++;
					}
			}		
		}
		if($k == 0)
		return false;
		return $this->_buildResultSet($pattern,$resmodel);
	}

	/**
	* Checks it there are internal bindings between variables.
	*
	* @param  Triple  $trip
	* @param  Array   $intBindings
	* @return boolean
	*/
	protected function checkIntBindings($trip, $intBindings){
		switch($intBindings){
			case -1:
			return true;
			break;
			case 0:
			if($trip->subj != $trip->pred)
			return false;
			break;
			case 1:
			if(is_a($trip->obj,'Literal'))
			return false;
			if($trip->subj != $trip->obj)
			return false;
			break;
			case 2:
			if(is_a($trip->obj,'Literal'))
			return false;
			if($trip->pred != $trip->obj)
			return false;
			break;
			case 3:
			if(is_a($trip->obj,'Literal'))
			return false;
			if($trip->pred != $trip->obj || $trip->pred != $trip->subj )
			return false;
			break;
		}
		return true;
	}


	/**
	* Perform an SQL-like inner join on two resultSets.
	*
	* @param   Array   &$finalRes
	* @param   Array   &$res
	* @return  Array
	*/
	protected function joinTuples(&$finalRes, &$res) {

		if (!$finalRes || !$res)
		return array();

		// find joint variables and new variables to be added to $finalRes
		$jointVars = array();
		$newVars = array();
		$k = key($res);

		foreach ($res[$k] as $varname => $node) {
			if (array_key_exists($varname, $finalRes[0]))
			$jointVars[] = $varname;
			else
			$newVars[] = $varname;
		}

		// eliminate rows of $finalRes in which the values of $jointVars do not have
		// a corresponding row in $res.
		foreach ($finalRes as $n => $fRes) {
			foreach ($res as $i => $r) {
				$ok = TRUE;
				foreach ($jointVars as $j_varname)
				if ($r[$j_varname] != $fRes[$j_varname]) {
					$ok = FALSE;
					break;
				}
				if ($ok)
				break;
			}
			if (!$ok)
			unset($finalRes[$n]);
		}

		// join $res and $finalRes
		$joinedRes = array();
		foreach ($res as $r) {
			foreach ($finalRes as $n => $fRes) {
				$ok = TRUE;
				foreach ($jointVars as $j_varname)
				if ($r[$j_varname] != $fRes[$j_varname]) {
					$ok = FALSE;
					break;
				}
				if ($ok) {
					$joinedRow = $finalRes[$n];
					foreach($newVars as $n_varname)
					$joinedRow[$n_varname] = $r[$n_varname];
					$joinedRes[] = $joinedRow;
				}
			}
		}
		return $joinedRes;
	}


	/**
	* Joins OPTIONAL pattern results.
	*
	* @param   Array   &$finalRes
	* @param   Array   &$res
	* @return  Array    the joined Array
	*/
	protected function joinOptionalTuples(&$finalRes, &$res) {

		if(!$finalRes && !$res)
		return array();

		if(!$finalRes)
		return $res;

		if(!$res)
		return $finalRes;

		// find joint variables and new variables to be added to $finalRes
		$jointVars = array();
		$newVars = array();
		$result = array();

		$k = key($res);

		foreach ($res[$k] as $varname => $node) {
			if (array_key_exists($varname, $finalRes[0])){
				$jointVars[] = $varname;
			}else{
				$newVars[] = $varname;
			}
		}
		$joined = array();
		foreach($finalRes as $i =>$fRes){
			foreach($res as $n =>$r){
				$join = false;
				foreach($jointVars as $j_varname){
					if($r[$j_varname]==$fRes[$j_varname]){
						$join = true;
						break;
					}
				}
				if($join){
					$result[$i] = $fRes;
					foreach($newVars as $n_varname)
					$result[$i][$n_varname] = $r[$n_varname];
					$joined[]=$n;
				}

			}
		}

		$count = count($result);
		foreach($res as $k =>$val){
			if(!in_array($k,$joined)){
				$result[$count] = $finalRes[0];
				foreach($result[$count] as $varname => $varVal){
					$result[$count][$varname]='';
				}

				foreach($val as $varname2 => $varVal2){
					$result[$count][$varname2]=$varVal2;
				}
				$count++;
			}
		}
		return $result;
	}



	/**
	* Looks in from and from named part of the query and
	* adds the graphs to the graphlist.
	*
	* @return Array
	*/
	protected function preselectGraphs(){
		$fromNamed = $this->query->getFromNamedPart();
		if($fromNamed == null)
		$fromNamed[] = null;
		return $fromNamed;
	}


	/**
	* Evaluates the GRPAH clause if there is one. Checks if
	* the GRAPH clause contains an IRI, variable or nothing.
	* Returns an array which contains the graphs that has to be matched.
	*
	* @param  GraphPattern $pattern
	* @param  Array        $graphlist
	* @return Array
	*/
	protected function _checkGraphs(&$pattern,$graphlist){

		$gr = $pattern->getGraphname();
		if($gr instanceof Resource ){
			if($graphlist[0]==null || in_array($gr,$graphlist)){
				$newGraphList['list'][] = $gr;
				$newGraphList['var'][]  = null;
			}else{
				return false;
			}
		}elseif (is_string($gr)){
			$newGraphList['list'] = $graphlist;
			$newGraphList['var'][]  = $gr;
		}else{
			$newGraphList['list'] = $graphlist;
			$newGraphList['var'][]  = null;
		}
		return $newGraphList;
	}

	/**
	* Marks triples with internal bindings.
	* int bindings -1 :none 0:sub=pred 1:sub=obj 2:pred=obj 3:sub=pred=obj.
	*
	* @param  Array $var
	* @return Array
	*/
	protected function _buildIntBindings($var){
		$intBindings = -1;
		if(!$var)
		return $intBindings;

		if(isset($var['sub'])){
			if(isset($var['pred']))
			if($var['sub'] == $var['pred'])
			$intBindings = 0;
			if(isset($var['obj']))
			if($var['sub'] == $var['obj']){
				if( $intBindings == 0){
					$intBindings = 3;
				}else{
					$intBindings = 1;
				}
			}
		}
		if(isset($var['pred'])){
			if(isset($var['obj']))
			if($var['pred']==$var['obj']&&$intBindings!=3)
			$intBindings = 2;
		}
		return $intBindings;
	}

	/**
	* Builds the resultset.
	*
	* @param  GraphPattern $pattern
	* @param  Array        $resmodel
	* @return Array
	*/
	protected function _buildResultSet($pattern,$resmodel){
		// determine variables and their corresponding values
		$result = null;
		if(is_string($pattern->getSubject())){
			$n = 0;
			foreach($resmodel['trip'] as $key => $triple){
				if(isset($resmodel['graphvar'][$key]))
				$result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
				$result[$n++][$pattern->getSubject()] = $triple->subj;
			}
		}
		if(is_string($pattern->getPredicate())){
			$n = 0;
			foreach($resmodel['trip'] as $key => $triple){
				if(isset($resmodel['graphvar'][$key]))
				$result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
				$result[$n++][$pattern->getPredicate()] = $triple->pred;
			}
		}
		if(is_string($pattern->getObject())){
			$n = 0;
			foreach($resmodel['trip'] as $key => $triple){
				if(isset($resmodel['graphvar'][$key]))
				$result[$n][$resmodel['graphvar'][$key]] = $resmodel['graph'][$key];
				$result[$n++][$pattern->getObject()] = $triple->obj;
			}
		}
		return $result;
	}

	/**
	* Selects the result variables and builds a result table.
	*
	* @param  Array  $table the result table
	* @param  Array  $vars the result variables
	* @return Array
	*/
	protected function selectVars($table,$vars){
		if($vars[0]=='*')
		$vars = $this->query->getAllVars();
		$resTable = array();
		$hits = 0;
		foreach($table as $val){
			foreach($vars as $var){
				if(isset($val[$var])){
					$resTable[$hits][$var]=$val[$var];
				}else{
					$resTable[$hits][$var]="";
				}
			}
			$hits++;
		}
		return $resTable;
	}

	/**
	* Joins the results of the different Graphpatterns.
	*
	* @param  Array $patternlist
	* @return Array
	*/
	protected function joinResults($patternlist){
		$joined[0]['patternResult'] = null;
		$joined[0]['outerFilter'] = null;

		while(count($patternlist)>0){
			foreach($patternlist as $key => $pattern){
				if($pattern['hasOptional'] == 0 && $pattern['hasUnion'] == 0){
					if(is_int($pattern['optionalTo'])){
						$patternlist[$pattern['optionalTo']]['hasOptional']--;
						$patternlist[$pattern['optionalTo']]['patternResult'] = $this->joinOptionalTuples($pattern['patternResult'],$patternlist[$pattern['optionalTo']]['patternResult']);
						unset($patternlist[$key]);
						break;
					}
					else if(is_int($pattern['unionWith'])){
						$patternlist[$pattern['unionWith']]['hasUnion']--;
						foreach($pattern['patternResult'] as $value)
						array_push($patternlist[$pattern['unionWith']]['patternResult'],$value);
						unset($patternlist[$key]);
						break;
					}else{
						if($joined[0]['patternResult'] == null){
							$joined[0]['patternResult'] = $pattern['patternResult'];
							if($joined[0]['outerFilter'] == null )
							$joined[0]['outerFilter']  = $pattern['outerFilter'];
							unset($patternlist[$key]);
							break;
						}
					//	if($pattern['patternResult'] !=null ){
							$joined[0]['patternResult'] = $this->joinTuples($joined[0]['patternResult'],$pattern['patternResult']);
							$joined[0]['outerFilter']   = $pattern['outerFilter'];
							unset($patternlist[$key]);
							break;
					//	}	
					}
				}
			}
		}
		return $joined;
	}

	/**
	* Filters the pattern results.
	*
	* @param  Array   $patternlist list containing the results of the GraphPatterns
	* @param  boolean $outer TRUE if its an outer filter FALSE if not
	* @return Array   the filtered patternlist
	*/
	protected function filterPatterns($patternlist,$outer){
		if($outer)
		$filter = 'outerFilter';
		else
		$filter = 'innerFilter';
		foreach($patternlist as $patkey => $pattern){
			// get constraints
			$constraint = $pattern[$filter];

			if(count($constraint)>0){
				foreach($constraint as $constr){
					if($constr != null){
						// extract Vars and function calls
						$evalString = $constr->getExpression();
						preg_match_all("/\?.[^\s\)\,]*/",$evalString,$vars);
						preg_match_all("/bound\((.[^\)]*)\)/i",$evalString,$boundcalls);
						preg_match_all("/isuri\((.[^\)]*)\)/i",$evalString,$isUricalls);
						preg_match_all("/isblank\((.[^\)]*)\)/i",$evalString,$isBlankcalls);
						preg_match_all("/isLiteral\((.[^\)]*)\)/i",$evalString,$isLiteralcalls);
						preg_match_all("/lang\((.[^\)]*)\)/i",$evalString,$langcalls);
						preg_match_all("/datatype\((.[^\)]*)\)/i",$evalString,$datatypecalls);
						preg_match_all("/str\((.[^\)]*)\)/i",$evalString,$stringcalls);

						// is Bound
						if(count($boundcalls[1])>0)
						$function['bound'] = $boundcalls[1];
						else
						$function['bound'] = false;

						// is URI
						if(count($isUricalls[1])>0)
						$function['isUri'] = $isUricalls[1];
						else
						$function['isUri'] = false;

						// is Blank
						if(count($isBlankcalls[1])>0)
						$function['isBlank'] = $isBlankcalls[1];
						else
						$function['isBlank'] = false;

						// is Literal
						if(count($isLiteralcalls[1])>0)
						$function['isLiteral'] = $isLiteralcalls[1];
						else
						$function['isLiteral'] = false;

						// lang
						if(count($langcalls[1])>0)
						$function['lang'] = $langcalls[1];
						else
						$function['lang'] = false;

						// datatype
						if(count($datatypecalls[1])>0)
						$function['datatype'] = $datatypecalls[1];
						else
						$function['datatype'] = false;

						// string
						if(count($stringcalls[1])>0)
						$function['string'] = $stringcalls[1];
						else
						$function['string'] = false;


						foreach($pattern['patternResult'] as $key => $res){
							$result = false;
							$evalString = $this->fillConstraintString($vars,$res,$constr,$function);
							$evalString = '$result =('.$evalString.');';
							// evaluate Constraint
							@eval($evalString);

							if(!$result)
							unset($patternlist[$patkey]['patternResult'][$key]);

						}
					}
				}
			}
		}
		return $patternlist;
	}

	/**
	* Builds an evaluation string to determine wether the result passes
	* the filter or not. This string is evaluatet by the php buildin eval() function
	*
	* @param  Array      $vars a list which contains the used variables
	* @param  Array      $res  the result part which have to be evaluated
	* @param  Constraint $constraint the Constrain object
	* @param  Array      $function an Array which contains the used functions
	* @return String
	*/

	protected function fillConstraintString($vars,$res,$constraint,$function){

		$boundExpr = false;
		$evalString = $constraint->getExpression();

		// extract Literals
		$pattern1 = "/\".[^\"]*\"[^\^\@]/";
		$pattern2 = "/\'.[^\']*\'[^\^\@]/";
		preg_match_all($pattern1,$evalString,$hits1);
		preg_match_all($pattern2,$evalString,$hits2);

		foreach($hits1[0] as $k => $val){
			$evalString = preg_replace('/\".[^\"]*\"[^\^]/','_REPLACED1_'.$k++,$evalString,1);
		}
		foreach($hits2[0] as $k => $val){
			$evalString = preg_replace('/\".[^\"]*\"[^\^]/','_REPLACED2_'.$k++,$evalString,1);
		}

		// replace namespaces
		$prefs = $this->query->getPrefixes();
		foreach($prefs as $key => $val){
			if($key == '')
			$key = ' ';
			$evalString = preg_replace("/^(".$key."\:)(.[^\s]*)|([\s\(]?[^\^])(".$key."\:)(.[^\s\)]*)([\s\)]?)/","$3'<".$val."$2$5>'$6",$evalString);

			$evalString = preg_replace("/(\^)(".$key."\:)(.[^\s]*)/","$1<".$val."$3>",$evalString);
		}

		$xsd = "http\:\/\/www.w3.org\/2001\/XMLSchema\#";

		// evaluate bound calls
		if($function['bound']){
			$boundExpr = true;
			foreach($function['bound'] as $var){
				if(isset($res[$var]) && $res[$var]!="")
				$replacement = 'true';
				else
				$replacement = 'false';
				$evalString = preg_replace("/bound\(\\".$var."\)/i",$replacement,$evalString);
			}

		}
		// evaluate isBlank calls
		if($function['isBlank']){
			foreach($function['isBlank'] as $var){
				if(isset($res[$var]) && $res[$var]!="" && $res[$var] instanceof BlankNode )
				$replacement = 'true';
				else
				$replacement = 'false';
				$evalString = preg_replace("/isBlank\(\\".$var."\)/i",$replacement,$evalString);
			}

		}
		// evaluate isLiteral calls
		if($function['isLiteral']){
			foreach($function['isLiteral'] as $var){
				if(isset($res[$var]) && $res[$var]!="" && $res[$var] instanceof Literal  )
				$replacement = 'true';
				else
				$replacement = 'false';
				$evalString = preg_replace("/isLiteral\(\\".$var."\)/i",$replacement,$evalString);
			}

		}
		// evaluate isUri calls
		if($function['isUri']){
			foreach($function['isUri'] as $var){
				if(isset($res[$var]) && $res[$var]!="" && $res[$var] instanceof Resource && $res[$var]->getUri() && !$res[$var] instanceof BlankNode )
				$replacement = 'true';
				else
				$replacement = 'false';
				$evalString = preg_replace("/isUri\(\\".$var."\)/i",$replacement,$evalString);
			}
		}
		// evaluate lang calls
		if($function['lang']){
			foreach($function['lang'] as $var){
				if(isset($res[$var]) && $res[$var]!="" && $res[$var] instanceof Literal && $res[$var]->getLanguage() )
				$replacement = '"'.$res[$var]->getLanguage().'"';
				else
				$replacement = 'null';
				$evalString = preg_replace("/lang\(\\".$var."\)/i",$replacement,$evalString);
			}
		}
		// evaluate datatype calls
		if($function['datatype']){
			foreach($function['datatype'] as $var){
				if(isset($res[$var]) && $res[$var]!="" && $res[$var] instanceof Literal && $res[$var]->getDatatype() )
				$replacement = '\'<'.$res[$var]->getDatatype().'>\'';
				else
				$replacement = 'false';
				$evalString = preg_replace("/datatype\(\\".$var."\)/i",$replacement,$evalString);
			}
		}
		// evaluate string calls
		if($function['string']){
			foreach($function['string'] as $var){
				if($var{0}=='?' || $var{0}=='$'){
					if(isset($res[$var]) && $res[$var]!=""){
						$replacement = "'str_".$res[$var]->getLabel()."'";
						if($res[$var] instanceof BlankNode)
						$replacement = "''";
					}else{
						$replacement = 'false';
					}
					$evalString = preg_replace("/str\(\\".$var."\)/i",$replacement,$evalString);
				}else{
					if($var{0}=='<'){
						$evalString = preg_replace("/str\(\s*\<(.[^\>]*)\>\s*\)/i","'str_$1'",$evalString);
					}
					if($var{0}=='"'){
						$evalString = preg_replace("/str\(\s*\"(.[^\>]*)\"\@[a-z]*\s*\)/i","'str_$1'",$evalString);
					}
				}

			}
		}
		// evaluate VARS
		foreach($vars[0] as $var){
			if(isset($res[$var])&&$res[$var]!= ""){
				//$replacement = "'".$res[$var]->getLabel()."'";
				$replacement = '" "';
				if($res[$var] instanceof Literal){
					if($res[$var]->getDatatype()!= null){
						if($res[$var]->getDatatype() == XML_SCHEMA.'boolean')
						$replacement = $res[$var]->getLabel();
						if($res[$var]->getDatatype() == XML_SCHEMA.'double')
						$replacement = $res[$var]->getLabel();
						if($res[$var]->getDatatype() == XML_SCHEMA.'integer')
						$replacement = $res[$var]->getLabel();
						if($res[$var]->getDatatype() == XML_SCHEMA.'dateTime')
						$replacement = strtotime($res[$var]->getLabel());
					}else{
						if($res[$var]->getLabel()=="")
						$replacement = 'false';
						else
						$replacement = "'str_".$res[$var]->getLabel()."'";
					}
				}else{
					if($res[$var] instanceof Resource){
						$replacement = "'<".$res[$var]->getLabel().">'";
					}
				}
				$evalString = preg_replace("/\\".$var."/",$replacement,$evalString);
			}

			// problem with PHP: false < 13 is true
			if(isset($res[$var])){
				if($res[$var] == ""){
					if($boundExpr)
					$evalString = preg_replace("/\\".$var."/","false",$evalString);
					else
					$evalString = 'false';
				}
			}else{
				$evalString = preg_replace("/\\".$var."/","false",$evalString);
			}

		}

		// replace '=' with '=='
		$evalString = preg_replace("/(.[^\=])(\=)(.[^\=])/","$1==$3",$evalString);


		// rewrite Literals
		foreach($hits1[0] as $k => $val){
			$pattern = '/_REPLACED1_'.$k.'/';
			$evalString = preg_replace($pattern,$hits1[0][$k],$evalString,1);
		}

		foreach($hits2[0] as $k => $val){
			$pattern = '/_REPLACED2_'.$k.'/';
			$evalString = preg_replace($pattern,$hits2[0][$k],$evalString,1);
		}

		// replace xsd:boolean expressions
		$pattern = $pattern = '/\"\s?true\s?\"\^\^\<'.$xsd.'boolean\>|\'\s?true\s?\'\^\^xsd:boolean/';
		$evalString = preg_replace($pattern,"true",$evalString);

		$pattern = $pattern = '/\"\s?false\s?\"\^\^\<'.$xsd.'boolean\>|\'\s?false\s?\'\^\^xsd:boolean/';
		$evalString = preg_replace($pattern,"false",$evalString);

		// replace xsd:date expressions
		$pattern = "/\"(.[^\"]*)\"\^\^".$xsd."dateTime/";
		preg_match_all($pattern,$evalString,$hits);

		foreach($hits[1] as $dummy)
		$evalString = preg_replace("/\".[^\"]*\"\^\^".$xsd."dateTime/",strtotime($dummy),$evalString,1);


		$evalString = preg_replace("/(\'\<".$xsd."dateTime\()(.[^\)]*\))\>\'/","dateTime($2",$evalString);

		$evalString = preg_replace("/(\'\<".$xsd."integer\()(.[^\)]*\))\>\'/","integer($2",$evalString);

		// tag plain literals
		$evalString = preg_replace("/\"(.[^\"]*)\"([^\^])|\"(.[^\"]*)\"$/","'str_$1$3'$2",$evalString);

		return $evalString;
	}

	/**
	* Sorts the results.
	*
	* @param  Array  $vartable List containing the unsorted result vars
	* @return Array  List containing the sorted result vars
	*/
	protected function sortVars($vartable){
		$newTable = array();
		$mod = $this->query->getSolutionModifier();
		// if no ORDER BY solution modifier return vartable
		if($mod['order by']!= null){
			$order = $mod['order by'];
			$map = $this->buildVarmap($order,$vartable);
			foreach($map as $val){
				$newTable[] = $vartable[$val];
			}
		}else{
			$newTable = $vartable;
		}

		if($mod['offset'] != null){
			$newTable = array_slice ($newTable, $mod['offset']);
		}
		if($mod['limit'] != null){
			$newTable = array_slice($newTable,0,$mod['limit']);
		}

		return $newTable;
	}

	/**
	* Sorts the result table.
	*
	* @param  String $order (ASC/DESC)
	* @param  Array  $vartable the vartable
	* @return Array  A map that contains the new order of the result vars
	*/
	protected function buildVarmap($order, $vartable){
		$n= 0;
		$result = array();
		$num_var = array();
		foreach($order as $variable)
		$num_var[$variable['val']] = 0;

		foreach($vartable as $k => $x){
			foreach($order as $value){
				// if the value is a typed Literal try to determine if it
				// a numeric datatype
				if($x[$value['val']] instanceof Literal){
					$dtype = $x[$value['val']]->getDatatype();
					if($dtype){
						switch($dtype){
							case XML_SCHEMA."integer":
							$num_var[$value['val']]++;
							break;
							case XML_SCHEMA."double":
							$num_var[$value['val']]++;
							break;

						}
					}
				}
				if($x[$value['val']]){
					if($x[$value['val']]instanceof Literal){
						$pref = "2";
					}
					if($x[$value['val']]instanceof Resource){
						$pref = "1";
					}
					if($x[$value['val']]instanceof BlankNode){
						$pref = "0";
					}
					$result[$value['val']][$n] = $pref.$x[$value['val']]->getLabel();
				}else{
					$result[$value['val']][$n] = "";
				}
			}
			$result['oldKey'][$n] = $k;
			$n++;
		}
		$sortString = "";
		foreach($order as $value){
			if($num_var[$value['val']] == $n)
			$sort = SORT_NUMERIC;
			else
			$sort = SORT_STRING;

			if($value['type'] == 'asc')
			$type = SORT_ASC;
			else
			$type = SORT_DESC;

			$sortString = $sortString.'$result["'.$value['val'].'"],'.$type.','.$sort.',';
		}
		$sortString = "array_multisort(".$sortString.'$result["oldKey"]);';

		@eval($sortString);
		return $result['oldKey'];
	}


	/**
	* Constructs a result graph.
	*
	* @param  Array         $vartable a table containing the result vars and their bindings
	* @param  GraphPattern  $constructPattern the CONSTRUCT pattern
	* @return MemModel      the result graph which matches the CONSTRUCT pattern
	*/
	protected function constructGraph($vartable,$constructPattern){

		$resultGraph = new MemModel();

		if(!$vartable)
		return $resultGraph;

		$tp = $constructPattern->getTriplePattern();

		$bnode = 0;
		foreach($vartable as $value){
			foreach($tp as $triple){
				$sub  = $triple->getSubject();
				$pred = $triple->getPredicate();
				$obj  = $triple->getObject();

				if(is_string($sub) && $sub{1}=='_' )
				$sub  = new BlankNode("_bN".$bnode);
				if(is_string($pred) && $pred{1}=='_')
				$pred = new BlankNode("_bN".$bnode);
				if(is_string($obj)  && $obj{1}=='_')
				$obj  = new BlankNode("_bN".$bnode);


				if(is_string($sub))
				$sub  = $value[$sub];
				if(is_string($pred))
				$pred = $value[$pred];
				if(is_string($obj))
				$obj  = $value[$obj];

				if($sub != "" && $pred != "" && $obj != "")
				$resultGraph->add(new Statement($sub,$pred,$obj));

			}
			$bnode++;
		}
		return $resultGraph;
	}

	/**
	* Builds a describing named graph. To define an attribute list for a
	* several rdf:type look at constants.php
	*
	* @param  Array      $vartable
	* @return MemModel
	*/
	protected function describeGraph($vartable){
		// build empty named graph
		$resultGraph = new MemModel();
		// if no where clause fill $vartable
		$vars = $this->query->getResultVars();
		if($vartable == null){
			if($vars){
				$vartable[0] = array('?x' => new Resource(substr($vars[0],1,-1)));
				$vars[0] = '?x';
			}
		}
		// fetch attribute list from constants.php
		global $sparql_describe;
		// for each resultset
		foreach($vartable as $resultset){
			foreach($vars as $varname){
				$varvalue = $resultset[$varname];
				// try to determine rdf:type of the variable
				$type = $this->_determineType($varvalue,$resultGraph);
				// search attribute list defined in constants.php
				$list = null;
				if($type){
					if(isset($sparql_describe[strtolower($type->getUri())]))
					$list = $sparql_describe[strtolower($type->getUri())] ;
				}
				// search in dataset
				$this->_getAttributes($list, $resultGraph, $varvalue);
			}
		}

		return $resultGraph;
	}

	/**
	* Tries to determine the rdf:type of the variable.
	*
	* @param  Node       $var The variable
	* @param  MemModel   $resultGraph The result graph which describes the Resource
	* @return String     Uri of the rdf:type
	*/
	protected function _determineType($var ,$resultGraph ){
		$type = null;
		// find in namedGraphs
		if(!$var instanceof Literal){
			$iter = $this->dataset->findInNamedGraphs(null,$var,new Resource(RDF_NAMESPACE_URI.'type'),null,true);
			while($iter->valid()){
				$statement = $iter->current();
				$type = $statement->getObject();
				$resultGraph->add($iter->current());
				break;
			}
		}
		// if no type information found find in default graph
		if(!$type){
			if(!$var instanceof Literal){
				$iter1 = $this->dataset->findInDefaultGraph($var,new Resource(RDF_NAMESPACE_URI.'type'),null);
				$type = null;
				while($iter1->valid()){
					$statement = $iter1->current();
					$type = $statement->getObject();
					$resultGraph->add($iter1->current());
					break;
				}
			}
		}
		return $type;
	}

	/**
	* Search the attributes listed in $list in the dataset.
	*
	* @param Array      $list List containing the attributes
	* @param MemModel   $resultGraph The result graph which describes the Resource
	* @return void
	*/
	protected function _getAttributes($list,$resultGraph, $varvalue){
		if($list){
			foreach($list as $attribute){
				if(!$varvalue instanceof Literal){
					$iter2 = $this->dataset->findInNamedGraphs(null,$varvalue,new Resource($attribute),null,true);
					while($iter2->valid()){
						$resultGraph->add($iter2->current());
						$iter2->next();
					}
					$iter3 = $this->dataset->findInDefaultGraph($varvalue,new Resource($attribute),null);
					while($iter3->valid()){
						$resultGraph->add($iter3->current());
						$iter3->next();
					}
				}
			}
		}


	}

	/**
	* Eliminates duplicate results.
	*
	* @param  Array  $vartable a table that contains the result vars and their bindings
	* @return Array the result table without duplicate results
	*/
	protected function distinct($vartable){
		$index = array();
		foreach($vartable as $key => $value){
			$key_index="";
			foreach($value as $k => $v)
			if($v instanceof Object)
			    $key_index = $key_index.$k.$v->toString();
			if(isset($index[$key_index]))
			unset($vartable[$key]);
			else
			$index[$key_index]= 1;
		}
		return $vartable;
	}

	/**
	* Generates the result object.
	*
	* @param Array          $vartable The result table
	* @param String/boolean $resultform If set to 'xml' the result will be
	*                       SPARQL Query Results XML Format as described in http://www.w3.org/TR/rdf-sparql-XMLres/
	* @return Array/String  The result
	*/
	protected function returnResult($vartable,$resultform = false){
		$result = "false";
		if($vartable[0]['patternResult']!=null){
			// sort vars (ORDER BY, LIMIT, OFFSET)
			$vartable = $this->sortVars($vartable[0]['patternResult']);

			// CONSTRUCT, ASK, DESCRIBE, SELECT
			if( strtolower($this->query->getResultForm()) == 'ask'){
				if(count($vartable)>0)
				$result = "true";
				else
				$result = "false";
			}else if(strtolower($this->query->getResultForm()) == 'construct'){
				$result = $this->constructGraph($vartable,$this->query->getConstructPattern());
			}else if(strtolower($this->query->getResultForm()) == 'describe'){
				$result = $this->describeGraph($vartable);
			}else{
				// get result vars
				$vars = $this->query->getResultVars();
				// select result vars and return a result table
				$vartable = $this->selectVars($vartable,$vars);
				if($this->query->getResultForm()=='select distinct')
				$result = $this->distinct($vartable);
				else
				$result = $vartable;
			}
		}else if(strtolower($this->query->getResultForm()) == 'describe'){
			$result = $this->describeGraph(null);
		}else if(strtolower($this->query->getResultForm()) == 'construct'){
			$result = $this->constructGraph(false,$this->query->getConstructPattern());
		}
		if($resultform == 'xml' && $this->query->getResultForm()!='construct' && $this->query->getResultForm()!='describe')
		$result = $this->buildXmlResult($result);

		return $result;
	}

	/**
	* Generates an xml string from a given result table.
	*
	* @param  $vartable The result table
	* @return String    The xml result string
	*/
	protected function buildXmlResult($vartable){

		if($vartable instanceof NamedGraphMem )
		return $vartable->writeRdfToString();

		$result = '<sparql xmlns="http://www.w3.org/2005/sparql-results#">';
		$header = '<head>';

		// build header
		if(is_array($vartable)){
			$vars = $this->query->getResultVars();
			$header = '<head>';
			foreach($vars as $value){
				$header = $header.'<variable name="'.substr($value,1).'"/>';
			}
			$header = $header.'</head>';

			// build results
			$solm = $this->query->getSolutionModifier();
			$sel  = $this->query->getResultForm();

			$distinct = 'false';
			if($sel == 'select distinct')
			$distinct = 'true';

			$ordered = 'false';
			if($solm['order by'] != 0)
			$ordered = 'true';

			$results = '<results ordered="'.$ordered.'" distinct="'.$distinct.'">';
			foreach($vartable as $value){
				$results = $results.'<result>';
				foreach($value as $varname => $varvalue)
				$results = $results.$this->_getBindingString(substr($varname,1),$varvalue);
				$results = $results.'</result>';
			}
			$results = $results.'</results>';
		}else{
			$results = '</head><boolean>'.$vartable.'</boolean>';
		}
		$result = $result.$header.$results.'</sparql>';
		$result = simplexml_load_string($result);
		return $result->asXML();

	}

	/**
	* Helper Function for function buildXmlResult($vartable). Generates
	* an xml string for a single variable an their corresponding value.
	*
	* @param  String  $varname The variables name
	* @param  Node    $varvalue The value of the variable
	* @return String  The xml string
	*/
	protected function _getBindingString($varname,$varvalue){
		$binding = '<binding name="'.$varname.'">';
		$value = '<unbound/>';

		if($varvalue instanceof BlankNode ){
			$value = '<bnode>'.$varvalue->getLabel().'</bnode>';
		}elseif ($varvalue instanceof Resource){
			$value = '<uri>'.$varvalue->getUri().'</uri>';
		}elseif ($varvalue instanceof Literal){
			$label = htmlentities($varvalue->getLabel());
			$value = '<literal>'.$label.'</literal>';
			if($varvalue->getDatatype() != null)
			$value = '<literal datatype="'.$varvalue->getDatatype().'">'.$label.'</literal>';
			if($varvalue->getLanguage() != null)
			$value = '<literal xml:lang="'.$varvalue->getLanguage().'">'.$label.'</literal>';
		}
		$binding = $binding.$value.'</binding>';

		return $binding;
	}


	/**
	* Prints a query result as HTML table.
	* You can change the colors in the configuration file.
	*
	* @param array $queryResult [][?VARNAME] = object Node
	* @return void
	*/
	public function writeQueryResultAsHtmlTable($queryResult) {
		// Import Package Utility
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

		if ( $queryResult[0] == null) {
			echo 'no match<br>';
			return;
		}
		if ( $queryResult == 'false') {
			echo 'boolean: false<br>';
			return;
		}
		if ( $queryResult == 'true') {
			echo 'boolean: true<br>';
			return;
		}


		echo '<table border="1" cellpadding="3" cellspacing="0"><tr><td><b>No.</b></td>';
		foreach ($queryResult[0] as $varName => $value)
		echo "<td align='center'><b>$varName</b></td>";
		echo '</tr>';

		foreach ($queryResult as $n => $var) {


			echo '<tr><td width="20" align="right">' .($n + 1) .'.</td>';
			foreach ($var as $varName => $value) {
				if($value !=''){
					echo INDENTATION . INDENTATION . '<td bgcolor="';
					echo RDFUtil::chooseColor($value);
					echo '">';
					echo '<p>';

					$lang  = NULL;
					$dtype = NULL;
					if (is_a($value, 'Literal')) {
						if ($value->getLanguage() != NULL)
						$lang = ' <b>(xml:lang="' . $value->getLanguage() . '") </b> ';
						if ($value->getDatatype() != NULL)
						$dtype = ' <b>(rdf:datatype="' . $value->getDatatype() . '") </b> ';
					}
					echo  RDFUtil::getNodeTypeName($value) .$value->getLabel() . $lang . $dtype .'</p>';
				}else{
					echo "<td bgcolor='white'>unbound";
				}
			}
			echo '</tr>';
		}
		echo '</table>';
	}

} // end: Class SparqlEngine

?>