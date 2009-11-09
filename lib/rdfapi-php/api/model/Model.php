<?php

// ----------------------------------------------------------------------------------
// Class: Model
// ----------------------------------------------------------------------------------

/**
 * Abstract superclass of MemModel and DbModel. A model is a programming interface to an RDF graph.
 * An RDF graph is a directed labeled graph, as described in http://www.w3.org/TR/rdf-mt/.
 * It can be defined as a set of <S, P, O> triples, where P is a uriref, S is either
 * a uriref or a blank node, and O is either a uriref, a blank node, or a literal.
 *
 *
 * @version  $Id: Model.php,v 1.40 2006/06/13 07:52:13 tgauss Exp $
 * @author Radoslaw Oldakowski <radol@gmx.de>
 * @author Daniel Westphal <mail@d-westphal.de>
 *
 * @package model
 * @access	public
 */

Class Model extends Object {


/**
 * Base URI of the Model.
 * Affects creating of new resources and serialization syntax.
 *
 * @var     string
 * @access	private
 */
 var $baseURI;

/**
 * Number of the last assigned bNode.
 *
 *
 * @var     integer
 * @access	private
 */
 var $bNodeCount;



/**
 * Notice for people who are used to work with older versions of RAP.
 *
 * @throws  PHPError
 * @access	public
 */
 function Model() {

   $errmsg  = 'Since RAP 0.6 the class for manipulating memory models has been renamed to MemModel.';
   $errmsg .= '<br>Sorry for this inconvenience.<br>';

   trigger_error($errmsg, E_USER_ERROR);
 }


/**
 * Return current baseURI.
 *
 * @return  string
 * @access	public
 */
 function getBaseURI()  {

   return $this->baseURI;
 }


/**
 * Load a model from a file containing RDF, N3, N-Triples or a xhtml document containing RDF.
 * This function recognizes the suffix of the filename (.n3 or .rdf) and
 * calls a suitable parser, if no $type is given as string ("rdf" "n3" "nt");
 * If the model is not empty, the contents of the file is added to this DbModel.
 *
 * @param 	string 	$filename
 * @param 	string 	$type
 * @param   boolean $stream
 * @access	public
 */
 function load($filename, $type = NULL, $stream=false) {

  if ((isset($type)) && ($type =='n3') OR ($type =='nt')) {
  // Import Package Syntax
  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
  	$parser = new N3Parser();
  }elseif ((isset($type)) && ($type =='rdf')) {
  // Import Package Syntax
  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
  	$parser = new RdfParser();
  }elseif ((isset($type)) && ($type =='grddl')) {
  // Import Package Syntax
  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_GRDDL);
  	$parser = new GRDDLParser();
  }elseif ((isset($type)) && ($type =='rss')) {
  // Import Package Syntax
  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RSS);
  	$parser = new RssParser();
  }else {
   // create a parser according to the suffix of the filename
   // if there is no suffix assume the file to be XML/RDF
   preg_match("/\.([a-zA-Z0-9_]+)$/", $filename, $suffix);
   if (isset($suffix[1]) && (strtolower($suffix[1]) == 'n3' OR strtolower($suffix[1]) == 'nt')){
  	// Import Package Syntax
  	include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
   	  $parser = new N3Parser();
   }elseif (isset($suffix[1]) && (strtolower($suffix[1]) == 'htm' OR strtolower($suffix[1]) == 'html' OR strtolower($suffix[1]) == 'xhtml')){
   		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_GRDDL);
  		$parser = new GRDDLParser();
   }else{
  	// Import Package Syntax
  	include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
      $parser = new RdfParser();}
   };

   if(($stream && $type=='rdf')||($stream && $type=='n3'))
   		$temp=&$parser->generateModel($filename,false,$this);
   else{
   		$temp=&$parser->generateModel($filename);
   }
   $this->addModel($temp);
   if($this->getBaseURI()== null)
   $this->setBaseURI($temp->getBaseURI());

 }
 
 


/**
 * Adds a statement from another model to this model.
 * If the statement to be added contains a blankNode with an identifier
 * already existing in this model, a new blankNode is generated.
 *
 * @param 	Object Statement   $statement
 * @access	private
 */
 function _addStatementFromAnotherModel($statement, &$blankNodes_tmp) {

   $subject = $statement->getSubject();
   $object = $statement->getObject();

   if (is_a($subject, "BlankNode")) {
      $label = $subject->getLabel();
   	  if (!array_key_exists($label, $blankNodes_tmp))
      {
        if ($this->findFirstMatchingStatement($subject, NULL, NULL)
           || $this->findFirstMatchingStatement(NULL, NULL, $subject))
        {
   	       $blankNodes_tmp[$label] = new BlankNode($this);
   	       $statement->subj = $blankNodes_tmp[$label];
        } else {
           $blankNodes_tmp[$label] = $subject;
        }
      } else
  		 $statement->subj = $blankNodes_tmp[$label];
   }

   if (is_a($object, "BlankNode")) {
      $label = $object->getLabel();
      if (!array_key_exists($label, $blankNodes_tmp))
      {
        if ($this->findFirstMatchingStatement($object, NULL, NULL)
           || $this->findFirstMatchingStatement(NULL, NULL, $object))
        {
   	       $blankNodes_tmp[$label] = new BlankNode($this);
   	       $statement->obj = $blankNodes_tmp[$label];
        } else {
           $blankNodes_tmp[$label] = $object;
        }
      } else
  		   $statement->obj = $blankNodes_tmp[$label];
   }
   $this->add($statement);
 }



 /**
 * Internal method, that returns a resource URI that is unique for the Model.
 * URIs are generated using the base_uri of the DbModel, the prefix and a unique number.
 * If no prefix is defined, the bNode prefix, defined in constants.php, is used.
 *
 * @param	string	$prefix
 * @return	string
 * @access	private
 */
 function getUniqueResourceURI($prefix = false)
 {
 	static $bNodeCount;
 	if(!$bNodeCount)
 		$bNodeCount = 0;

 	if(!$prefix)
 		$prefix=BNODE_PREFIX;

	return $prefix.++$bNodeCount;
 }

 /**
 * Returns a ResModel with this model as baseModel. This is the same as
 * ModelFactory::getResModelForBaseModel($this).
 *
 * @return	object	ResModel
 * @access	public
 */
 function & getResModel()
 {
 	return ModelFactory::getResModelForBaseModel($this);
 }

 /**
 * Returns an OntModel with this model as baseModel.
 * $vocabulary has to be one of the following constants (currently only one is supported):
 * RDFS_VOCABULARY to select a RDFS Vocabulary.
 *
 * This is the same as ModelFactory::getOntModelForBaseModel($this, $vocabulary).
 *
 * @param   constant  $vocabulary
 * @return	object	OntModel
 * @access	public
 */
 function & getOntModel($vocabulary)
 {
	return ModelFactory::getOntModelForBaseModel($this, $vocabulary);
 }


 /**
 * Searches for triples using find() and tracks forward blank nodes
 * until the final objects in the retrieved subgraphs are all named resources.
 * The method calls itself recursivly until the result is complete.
 * NULL input for subject, predicate or object will match anything.
 * Inputparameters are ignored for recursivly found statements.
 * Returns a new MemModel or adds (without checking for duplicates)
 * the found statements to a given MemModel.
 * Returns an empty MemModel, if nothing is found.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * WARNING: This method can be slow with large models.
 * NOTE:    Blank nodes are not renamed, they keep the same nodeIDs
 *          as in the queried model!
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Anton K�stlbacher <anton1@koestlbacher.de>
 * @param    object Node     $subject
 * @param    object Node     $predicate
 * @param    object Node     $object
 * @param    object MemModel $object
 * @return   object MemModel
 * @access   public
 * @throws   PhpError
 */

 function findForward($subject, $predicate, $object, $newModel = NULL)
 {
     if (!is_a($newModel, "MemModel"))
     {
         $newModel = New MemModel;
     }

     if (is_a($this, "DbModel"))
     {
         $model = $this;
         $res   = $model->find($subject, $predicate, $object);
         $it    = $res->getStatementIterator();
     }
     elseif (is_a($this, "MemModel")) {
         $model = $this;
         $it    = $model->findAsIterator($subject, $predicate, $object);
     }
     elseif (is_a($this, "ResModel")) {
         $model = $this->model;
         $it    = $model->findAsIterator($subject, $predicate, $object);
     }

     while ($it->hasNext())
     {
         $statement = $it->next();
         $newModel->add($statement);
         if (is_a($statement->object(),'BlankNode'))
         {
             $model->findForward($statement->object(), NULL, NULL,&$newModel);
         }
     }
     return $newModel;
 }


/**
 * Perform an RDQL query on this Model. Should work with all types of models.
 * This method returns a MemModel containing the result statements.
 * If $closure is set to TRUE, the result will additionally contain
 * statements found by the findForward-method for blank nodes.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * WARNING: If called with $closure = TRUE this method
 *          can be slow with large models.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Anton K�stlbacher <anton1@koestlbacher.de>
 * @author   code snippets taken from the RAP Netapi by Phil Dawes and Chris Bizer
 * @access   public
 * @param    string $queryString
 * @param    boolean $closure
 * @return   object MemModel
 *
 */

 function & getMemModelByRDQL($queryString, $closure = FALSE)
 {
     require_once(RDFAPI_INCLUDE_DIR.PACKAGE_RDQL);
     $parser = new RdqlParser();
     $parsedQuery =& $parser->parseQuery($queryString);

     // If there are variables used in the pattern but not
     // in the select clause, add them to the select clause
     foreach ($parsedQuery['patterns'] as $n => $pattern)
     {
         foreach ($pattern as $key => $val_1)
         {
             if ($val_1['value']{0}=='?')
             {
                 if (!in_array($val_1['value'],$parsedQuery['selectVars']))
                 {
                 array_push($parsedQuery['selectVars'],$val_1['value']);
                 }
             }
         }
     }

     if (is_a($this, "DbModel"))
     {
         $engine = new RdqlDbEngine();
         $model  = $this;
     }
     elseif (is_a($this, "MemModel"))
     {
         $engine = new RdqlMemEngine();
         $model  = $this;
     }
     elseif (is_a($this, "ResModel"))
     {
         $engine = new RdqlMemEngine();
         $model  = $this->model;
     }

     $res = $engine->queryModel($model,$parsedQuery,TRUE);
     $rdqlIter = new RdqlResultIterator($res);
     $newModel = new MemModel();

     // Build statements from RdqlResultIterator
    while ($rdqlIter->hasNext()) {
        $result = $rdqlIter->next();
        foreach ($parsedQuery['patterns'] as $n => $pattern)
        {
            if (substr($pattern['subject']['value'], 0, 1) == '?')
            {
                $subj = $result[$pattern['subject']['value']];
            }
            else
            {
                $subj = new Resource($pattern['subject']['value']);
            }
            if (substr($pattern['predicate']['value'], 0, 1) == '?')
            {
                $pred = $result[$pattern['predicate']['value']];
            }
            else
            {
                $pred = new Resource($pattern['predicate']['value']);
            }

            if (substr($pattern['object']['value'], 0, 1) == '?')
            {
                $obj = $result[$pattern['object']['value']];
            }
            else
            {
                 if (isset($pattern['object']['is_literal']))
                 {
                     $obj = new Literal($pattern['object']['value']);
                     $obj->setDatatype($pattern['object']['l_dtype']);
                     $obj->setLanguage($pattern['object']['l_lang']);
                 }
                 else
                 {
                     $obj = new Resource($pattern['object']['value']);
                 }
            }

            $statement = new Statement($subj,$pred,$obj);
            $newModel->add($statement);

            // findForward() Statements containing an eventually given blank node
            // and add them to the result, if closure = true
            if (is_a($statement->object(),'BlankNode') && $closure == True)
            {
                $newModel = $model->findForward($statement->object(),NULL,NULL,&$newModel);
            }
            if (is_a($statement->subject(),'BlankNode') && $closure == True)
            {
                $newModel = $model->findForward($statement->subject(),NULL,NULL,&$newModel);
            }
        }
    }
    return $newModel;
 }

 /**
 * Alias for RDFUtil::visualiseGraph(&$model, $format, $short_prefix)
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Note: See RDFUtil for further Information.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Anton K�stlbacher <anton1@koestlbacher.de>
 * @param    string  $format
 * @param    boolean $short_prefix
 * @return   string, binary
 * @access   public
 * @throws   PhpError
 */

 function visualize($format = "dot", $short_prefix = TRUE)
 {
     return RDFUtil::visualizeGraph($this, $format, $short_prefix);
 }
 

  /**
 * Performs a SPARQL query against a model. The model is converted to
 * an RDF Dataset. The result can be retrived in SPARQL Query Results XML Format or
 * as an array containing the variables an their bindings.
 *
 * @param  String $query      the sparql query string
 * @param  String $resultform the result form ('xml' for SPARQL Query Results XML Format)
 * @return String/array       
 */
 function sparqlQuery($query,$resultform = false){
 	include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SPARQL);
 	include_once(RDFAPI_INCLUDE_DIR.PACKAGE_DATASET); 	
 	$dataset = new DatasetMem();
 	$dataset->setDefaultGraph($this);
 	$parser = new SparqlParser();
 	$q = $parser->parse($query);
 	
 	$eng = new SparqlEngine();
 	return $eng->queryModel($dataset,$q,$resultform);
 }
 
  
 
} // end: Model

?>
