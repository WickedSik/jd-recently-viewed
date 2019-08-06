<?php

class jrv_lastviewed_repo
{
    private static $pageList = [];

    public static function loadViewedPages()
    {
        if (!empty($_COOKIE['jrv_pages_dated'])) {
            try {
                self::$pageList = json_decode($_COOKIE['jrv_pages_dated']);
            } catch (Exception $e) {
                print_r($e);

                self::$pageList = [];
            }
        }
    }

    public static function getRecentlyViewed()
    {
        if (empty(self::$pageList)) {
            self::loadViewedPages();
        }
        return self::$pageList;
    }

    public static function addCurrentPage($post_id)
    {
        if (!empty($post_id)) {
            self::loadViewedPages();

            // add new page and reverse order so the first occurance of a page is the last occurance found
            self::$pageList[] = [$post_id, time()];
            self::$Pagelist = array_reverse(self::$pagelist);
            
            // filter out all the duplicate page id's
            self::$pageList = array_filter(self::$pageList, function($page, $index) use (self::$pagelist) {
                return array_search($page[0], array_column(0, self::$pagelist)) === $index;
            });
            
            // sort the visits by date
            usort(self::$pagelist, function($a, $b) { 
                return $a[1] > $b[1] ? 1 : $a[1] < $b[1] ? -1 : 0;
            });

            // store a maximum of 25 pages
            while (count(self::$pageList) > 25) {
                array_shift(self::$pageList);
            }

            // save the cookie
            setcookie('jrv_pages_dated', json_encode(self::$pageList), 0, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

}
