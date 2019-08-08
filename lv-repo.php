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

        if(!is_array(self::$pageList)) {
            self::$pageList = [];
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

            // find whether the post id has already been visited
            $matchingPages = array_filter(self::$pageList, function($page) use ($post_id) {
                return $page[0] === $post_id;
            });

            if(count($matchingPages) === 0) {
                self::$pageList[] = [$post_id, time()];
            } else {
                self::$pageList = array_map(function($page) use ($post_id) {
                    return [
                        $page[0], 
                        ($page[0] === $post_id) ? time() : $page[1]
                    ];
                }, self::$pageList);
            }
            
            // sort the visits by date
            usort(self::$pageList, function($a, $b) { 
                return $a[1] > $b[1] ? 1 : ($a[1] < $b[1] ? -1 : 0);
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
