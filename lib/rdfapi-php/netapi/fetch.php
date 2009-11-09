<?php

// ----------------------------------------------------------------------------------
// RAP Net API Fetch Operaton
// ----------------------------------------------------------------------------------

/**
 * The fetch operation gets all known information about a ressource.
 *
 * @version  $Id: fetch.php,v 1.7 2006/05/15 05:24:37 tgauss Exp $
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */

function fetch($model,$serializer){
  $uri = $_REQUEST['r'];

  $urir = new Resource($uri);

  $outm = new MemModel();

  getBNodeClosure($urir, $model, $outm);

  echo $serializer->Serialize($outm);
  
  $outm->close();
}

function getBNodeClosure($res,$sourcem, &$outm) { 
  $resourcem = $sourcem->find($res,NULL,NULL);
  $it = $resourcem->getStatementIterator();
  while ($it->hasNext()){
	$stmt = $it->next();
	$outm->add(new Statement($res,$stmt->predicate(), $stmt->object()));
	if (is_a($stmt->object(),'BlankNode')){
	  getBNodeClosure($stmt->object(),$sourcem,$outm);
	}
  }  
}

?>