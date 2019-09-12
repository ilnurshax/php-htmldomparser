<?php

namespace Ilnurshax\HtmlDomParser;

use Ilnurshax\HtmlDomParser\Exceptions\ContentMaxLengthLimitExceeded;
use Ilnurshax\HtmlDomParser\SimpleHtmlDom\HtmlDomNode;
use Ilnurshax\HtmlDomParser\SimpleHtmlDom\HtmlDom;

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_TYPE_ROOT',    5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_QUOTE',   2);
define('HDOM_INFO_SPACE',   3);
define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);
define('HDOM_INFO_ENDSPACE',7);
define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', " ");
if (!defined('MAX_FILE_SIZE'))
{
    define('MAX_FILE_SIZE', 3500000);
}

/**
 * Class HtmlDomParser
 * @package Application\Html\DomParser
 * @version 1.5
 */
class HtmlDomParser
{

    public static function parseFromUrl(
        $url,
        $use_include_path = false,
        $context = null,
        $offset = -1,
        $maxLen = -1,
        $lowercase = true,
        $forceTagsClosed = true,
        $target_charset = DEFAULT_TARGET_CHARSET,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT
    ) {
        // We DO force the tags to be terminated.
        $dom = new HtmlDom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText,
            $defaultSpanText);
        // For sourceforge users: uncomment the next line and comment the retreive_url_contents line 2 lines down if it is not already done.
        $contents = file_get_contents($url, $use_include_path, $context, $offset);
        // Paperg - use our own mechanism for getting the contents as we want to control the timeout.
        //$contents = retrieve_url_contents($url);
        if (empty($contents) || strlen($contents) > MAX_FILE_SIZE) {
            return false;
        }
        // The second parameter can force the selectors to all be lowercase.
        $dom->load($contents, $lowercase, $stripRN);

        return $dom;
    }

    public static function parseFromString(
        $str,
        $lowercase = true,
        $forceTagsClosed = true,
        $target_charset = DEFAULT_TARGET_CHARSET,
        $stripRN = true,
        $defaultBRText = DEFAULT_BR_TEXT,
        $defaultSpanText = DEFAULT_SPAN_TEXT
    ) {
        $dom = new HtmlDom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText,
            $defaultSpanText);

        if (empty($str) || ($strlen = strlen($str)) > ($limit = MAX_FILE_SIZE)) {
            $dom->clear();
            throw new ContentMaxLengthLimitExceeded("The given content length is {$strlen} but the limit is {$limit}.");
        }
        $dom->load($str, $lowercase, $stripRN);

        return $dom;
    }

    public static function dump_html_tree(HtmlDomNode $node, $show_attr = true, $deep = 0)
    {
        $node->dump($node);
    }

}
