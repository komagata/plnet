<?php

// ----------------------------------------------------------------------------------
// Class: Node
// ----------------------------------------------------------------------------------

/**
 * An abstract RDF node. 
 * Can either be resource, literal or blank node. 
 * Node is used in some comparisons like is_a($obj, "Node"), 
 * meaning is $obj a resource, blank node or literal.
 * 
 * 
 * @version $Id: Node.php,v 1.12 2006/06/08 06:25:14 tgauss Exp $
 * @author Chris Bizer <chris@bizer.de>
 * @package model
 * @abstract
 *
 */
 class Node extends Object {
 } // end:RDFNode


?>