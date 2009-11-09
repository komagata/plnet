<?php
if (!defined('RDFAPI_INCLUDE_DIR')) {
    define('RDFAPI_INCLUDE_DIR', BASE_LIB_DIR.'rdfapi-php/api/');
}
include_once RDFAPI_INCLUDE_DIR.'RdfAPI.php';
include_once RDFAPI_INCLUDE_DIR.'syntax/RdfParser.php';
include_once RDFAPI_INCLUDE_DIR.'vocabulary/FOAF_C.php';
include_once RDFAPI_INCLUDE_DIR.'vocabulary/RDFS_C.php';
include_once 'Cache/Lite.php';

class FOAFParser
{
//  var $foaf;
//  var $caching = true;
//  var $cache_lifetime = 60 * 60 * 24 * 1;

    function FOAFParser($caching = true, $cache_dir = '/tmp', $cache_lifetime = 86400)
    {
        $this->caching = $caching;
        $this->cache_dir = $cache_dir;
        $this->cache_lifetime = $cache_lifetime;
    }

    function parse($uri)
    {
        if ($this->caching) {
            $cache_id = urlencode($uri);
            $cache =& new Cache_Lite(array(
                'cacheDir' => $this->cache_dir,
                'lifeTime' => $this->cache_lifetime,
                'automaticCleaningFactor' => 128,
                'automaticSerialization' => true
            ));

            if ($data = $cache->get($cache_id)) {
                $this->foaf = $data;
                return true;
            } else {
                $model = ModelFactory::getDefaultModel();
                if (!file($uri)) return false;
                $res = $model->load($uri);
                if ($res === false) {
                    $cache->save($model, $cache_id);
                    return false;
                } else {
                    $this->foaf = $model;
                    $cache->save($model, $cache_id);
                    return true;
                }
            }
        }

        $model = ModelFactory::getDefaultModel();
        if (!file($uri)) return false;
        $res = $model->load($uri);
        if ($res === false) {
            return false;
        } else {
            $this->foaf = $model;
            return true;
        }
    }

    function getNick()
    {
        $result = $this->foaf->find(null, FOAF::NICK(), null);
        return isset($result->triples[0]->obj->label) 
            ? $result->triples[0]->obj->label : null;
    }

    function getImg()
    {
        $result = $this->foaf->find(null, FOAF::IMG(), null);
        return isset($result->triples[0]) 
            ? $result->triples[0]->getLabelObject() : null;
    }


    function getBio()
    {
        $old = new Resource('http://purl.org/vocab/bio/0.1/olb');
        $result = $this->foaf->find(null, $old, null);
        return isset($result->triples[0]->obj->label) 
            ? $result->triples[0]->obj->label : null;
    }

    function getWeblog()
    {
        $result = $this->foaf->find(null, FOAF::WEBLOG(), null);
        if (count($result->triples) > 1) {
            $me =& new BlankNode('me');
            $result = $this->foaf->find($me, FOAF::WEBLOG(), null);
        }
        return isset($result->triples[0]->obj->uri) 
            ? $result->triples[0]->obj->uri : null;
    }

    function getKnows() {
        $result = $this->foaf->find(null, FOAF::KNOWS(), null);
        return $result;
    }

    function getKnowsPerson() {
        $knows = $this->getKnows();

        $knowsPersons = array();
        if (count($knows->triples) > 1) {
            foreach ($knows->triples as $triple) {
                $persons = $this->foaf->find($triple->obj, null, null);

                $res = array();
                foreach ($persons->triples as $prop) {
                    if ($prop->pred->equals(FOAF::NICK())) {
                        $res['nick'] = $prop->getLabelObject();
                    } elseif ($prop->pred->equals(RDFS::SEEALSO())) {
                        $res['seeAlso'] = $prop->getLabelObject();
                    } elseif ($prop->pred->equals(FOAF::WEBLOG())) {
                        $res['weblog'] = $prop->getLabelObject();
                    }
                }

                $knowsPersons[] = $res;
            }
        }
        return $knowsPersons;
    }

    function toArray()
    {
        $result = array();
        if (!is_null($nick= $this->getNick())) {
            $result['nick'] = $nick;
        }
        if (!is_null($img = $this->getImg())) {
            $result['img'] = $img;
        }
        if (!is_null($bio = $this->getBio())) {
            $result['bio'] = $bio;
        }
        if (!is_null($weblog = $this->getWeblog())) {
            $result['weblog'] = $weblog;
        }

        return $result;
    }
}
?>
