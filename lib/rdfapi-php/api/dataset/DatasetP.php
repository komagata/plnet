<?php
// ----------------------------------------------------------------------------------
// dataset Package
// ----------------------------------------------------------------------------------
//
// Description               : dataset package
//
// Author: Daniel Westphal	<http://www.d-westphal.de>
//
// ----------------------------------------------------------------------------------

// Include ResModel classes
require( RDFAPI_INCLUDE_DIR . 'dataset/Dataset.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/DatasetMem.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/DatasetDb.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphMem.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphDb.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/IteratorAllGraphsMem.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/IteratorAllGraphsDb.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/Quad.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsMem.php');
require( RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsDb.php');
require( RDFAPI_INCLUDE_DIR . 'syntax/TriXParser.php');
require( RDFAPI_INCLUDE_DIR . 'syntax/TriXSerializer.php');
?>