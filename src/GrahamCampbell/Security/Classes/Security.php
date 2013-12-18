<?php

/**
 * This file is part of Laravel Security by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Security\Classes;

/**
 * This is the security class.
 *
 * @package    Laravel-Security
 * @author     Graham Campbell
 * @copyright  Copyright 2013 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Security/blob/develop/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Security
 */
class Security
{
    /**
     * XSS clean.
     *
     * @param  string  $string
     * @param  bool    $image
     * @return string
     */
    public function clean($string, $image = false)
    {
        if (is_array($string)) {
            while (list($key) = each($string)) {
                $string[$key] = $this->clean($string[$key]);
            }
            return $string;
        }

        $string = $this->checks($string);

        $converted = $string;

        $string = $this->neverAllowed($string);
        $string = $this->tags($string, $image);
        $string = $this->compact($string);
        $string = $this->links($string);
        $string = $this->evilAttributes($string, $image);
        $string = $this->naughty($string);
        $string = $this->neverAllowed($string);

        if ($image === true) {
            return ($string == $converted) ? true: false;
        }

        return $string;
    }

    /**
     * Run initial checks.
     *
     * @param  string  $string
     * @return string
     */
    protected function checks($string)
    {
        // remove the invisible characters
        $string = $this->removeInvisible($string);

        // validate entities in urls
        $string = $this->validateEntities($string);

        // url decode
        $string = rawurldecode($string);

        // remove HTML Tags
        $string = strip_tags($string);

        // convert character entities to ascii
        $string = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", function ($match) {
            return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
        }, $string);

        // convert character entities to ascii (part 2)
        $string = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", function ($match) {
            return $this->entityDecode($match[0], 'UTF-8');
        }, $string);

        // remove the invisible characters again
        $string = $this->removeInvisible($string);

        // convert all tabs to spaces
        if (strpos($string, "\t") !== false) {
            $string = str_replace("\t", ' ', $string);
        }

        return $string;
    }

    /**
     * Remove invisible characters.
     *
     * @param  string  $string
     * @param  bool    $encoded
     * @return string
     */
    protected function removeInvisible($string, $encoded = true)
    {
        $non_displayables = array();

        if ($encoded) {
            $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';  // url encoded 16-31
        }
        
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $string = preg_replace($non_displayables, '', $string, -1, $count);
        } while ($count);

        return $string;

    }

    /**
     * Validate entities.
     *
     * @param  string  $string
     * @return string
     */
    protected function validateEntities($string)
    {
        $xss_hash = md5(time() + mt_rand(0, 1999999999));

        $string = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $xss_hash."\\1=\\2", $string);
        $string = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $string);
        $string = preg_replace('#(&\#x?)([0-9A-F]+);?#i', "\\1\\2;", $string);
        $string = str_replace($xss_hash, '&', $string);

        return $string;
    }

    /**
     * Entity decode.
     *
     * @param  string  $string
     * @param  string  $charset
     * @return string
     */
    protected function entityDecode($string, $charset = 'UTF-8')
    {
        if (stristr($string, '&') === false) {
            return $string;
        }

        $string = html_entity_decode($string, ENT_COMPAT, $charset);
        $string = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $string);
        return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $string);
    }

    /**
     * Do never allowed.
     *
     * @param  string  $string
     * @return string
     */
    protected function neverAllowed($string)
    {
        $never_allowed_str = array(
            'document.cookie'   => '[removed]',
            'document.write'    => '[removed]',
            '.parentNode'       => '[removed]',
            '.innerHTML'        => '[removed]',
            'window.location'   => '[removed]',
            '-moz-binding'      => '[removed]',
            '<!--'              => '&lt;!--',
            '-->'               => '--&gt;',
            '<![CDATA['         => '&lt;![CDATA[',
            '<comment>'         => '&lt;comment&gt;'
        );

        $never_allowed_regex = array(
            'javascript\s*:',
            'expression\s*(\(|&\#40;)',
            'vbscript\s*:',
            'Redirect\s+302',
            "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?",
        );

        $string = str_replace(array_keys($never_allowed_str), $never_allowed_str, $string);

        foreach ($never_allowed_regex as $regex) {
            $string = preg_replace('#'.$regex.'#is', '[removed]', $string);
        }

        return $string;
    }

    /**
     * Make php tags safe.
     *
     * @param  string  $string
     * @param  bool    $image
     * @return string
     */
    protected function tags($string, $image)
    {
        if ($image === true) {
            $string = preg_replace('/<\?(php)/i', "&lt;?\\1", $string);
        } else {
            $string = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $string);
        }

        return $string;
    }

    /**
     * Compact any exploded words.
     *
     * @param  string  $string
     * @return string
     */
    protected function compact($string)
    {
        $words = array(
            'javascript',
            'expression',
            'vbscript',
            'script',
            'base64',
            'applet',
            'alert',
            'document',
            'write',
            'cookie',
            'window'
        );

        foreach ($words as $word) {
            $temp = '';

            for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
                $temp .= substr($word, $i, 1)."\s*";
            }

            $string = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', function ($matches) {
                return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
            }, $string);
        }

        return $string;
    }

    /**
     * Remove js in links or img tags.
     *
     * @param  string  $string
     * @return string
     */
    protected function links($string)
    {
        do {
            $original = $string;

            if (preg_match("/<a/i", $string)) {
                $string = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", function ($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
                            '',
                            $this->filterAttributes(str_replace(array('<', '>'), '', $match[1]))
                        ),
                        $match[0]
                    );
                }, $string);
            }

            if (preg_match("/<img/i", $string)) {
                $string = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", function ($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                            '',
                            $this->filterAttributes(str_replace(array('<', '>'), '', $match[1]))
                        ),
                        $match[0]
                    );
                }, $string);
            }

            if (preg_match("/script/i", $string) || preg_match("/xss/i", $string)) {
                $string = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $string);
            }
        } while ($original != $string);

        return $string;
    }

    /**
     * Filter attributes.
     *
     * @param  string  $string
     * @return string
     */
    protected function filterAttributes($string)
    {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $string, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }

        return $out;
    }

    /**
     * Remove evil attributes.
     *
     * @param  string  $string
     * @param  bool    $image
     * @return string
     */
    protected function evilAttributes($string, $image)
    {
        $attributes = array('on\w*', 'style', 'xmlns', 'formaction');

        if ($image === true) {
            unset($attributes[array_search('xmlns', $attributes)]);
        }

        do {
            $count = 0;
            $attribs = array();

            // find occurrences of illegal attribute strings without quotes
            preg_match_all('/('.implode('|', $attributes).')\s*=\s*([^\s>]*)/is', $string, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            preg_match_all("/(".implode('|', $attributes).")\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is", $string, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // replace illegal attribute strings that are inside an html tag
            if (count($attribs) > 0) {
                $string = preg_replace("/<(\/?[^><]+?)([^A-Za-z<>\-])(.*?)(".implode('|', $attribs).")(.*?)([\s><])([><]*)/i", '<$1 $3$5$6$7', $string, -1, $count);
            }

        } while ($count);

        return $string;
    }

    /**
     * Sanitize naughty elements.
     *
     * @param  string  $string
     * @return string
     */
    protected function naughty($string)
    {
        // sanitize naughty html elements
        $naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
        $string = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', function ($matches) {
            $string = '&lt;'.$matches[1].$matches[2].$matches[3];
            return $string .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
        }, $string);

        // sanitize naughty scripting elements
        $string = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $string);

        return $string;
    }
}
