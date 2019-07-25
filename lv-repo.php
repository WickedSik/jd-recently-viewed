<?php

class jrv_lastviewed_repo
{
    private static $pageList = [];

    public static function loadViewedPages()
    {
        if (!empty($_COOKIE['jrv_pages'])) {
            try {
                self::$pageList = json_decode($_COOKIE['jrv_pages']);
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

            self::$pageList[] = $post_id;
            self::$pageList = array_unique(self::$pageList);

            // store a maximum of 25 pages
            while (count(self::$pageList) > 25) {
                array_shift(self::$pageList);
            }

            setcookie('jrv_pages', json_encode(self::$pageList), 0, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

}
