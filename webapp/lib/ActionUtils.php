<?php
require_once 'Pager/Pager.php';

class ActionUtils
{
    function pager($itemData, $perPage = 10, $delta = 5, $mode = 'Jumping',
    $urlVar = 'page', $prevImg = '&lt;', $nextImg = '&gt;', $separator = ' |')
    {
        list($path, $query) = split("\?", $_SERVER['REQUEST_URI']);

        $options = array(
            'itemData' => $itemData,
            'mode' => $mode,
            'path' => $path,
            'append' => false,
            'fileName' => '?'.$urlVar.'=%d',
            'perPage' => $perPage,
            'delta' => $delta,
            'urlVar' => $urlVar,
            'prevImg' => $prevImg,
            'nextImg' => $nextImg,
            'firstPageText' => '&lt;&lt;',
            'lastPageText' => '&gt;&gt;',
            'firstPagePre' => '',
            'firstPagePost' => '',
            'lastPagePre' => '',
            'lastPagePost' => '',
            'separator' => $separator
        );
        $pager =& Pager::factory($options);
        $links = $pager->getLinks();

        $link_pager = $links['all'];
        if (!$pager->isFirstPage()) {
            $link_pager = $links['first'].' '.$link_pager;
        }

        if (!$pager->isLastPage()) {
            $link_pager = $link_pager.' '.$links['last'];
        }
        $pager->slider = $link_pager;
        return $pager;
    }
}
?>
