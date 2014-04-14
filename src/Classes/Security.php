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
 * @copyright  Copyright 2013-2014 Graham Campbell
 * @license    https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md
 * @link       https://github.com/GrahamCampbell/Laravel-Security
 */
class Security
{
    /**
     * A random hash for protecting urls.
     *
     * @var string
     */
    protected $xssHash;

    /**
     * XSS clean.
     *
     * @param  array|string  $str
     * @return string
     */
    public function clean($str)
    {
        $old = $str;

        if (is_array($str)) {
            while (list($key) = each($str)) {
                $str[$key] = $this->clean($str[$key]);
            }

            return $str;
        }

        $str = $this->validateEntities($this->removeInvisibleCharacters($str));

        do {
            $str = rawurldecode($str);
        } while (preg_match('/%[0-9a-f]{2,}/i', $str));

        $str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, 'convertAttribute'), $str);
        $str = preg_replace_callback('/<\w+.*/si', array($this, 'decodeEntity'), $str);

        $str = $this->removeInvisibleCharacters($str);

        $str = str_replace("\t", ' ', $str);

        $str = $this->doNeverAllowed($str);

        $str = str_replace(array('<?', '?'.'>'), array('&lt;?', '?&gt;'), $str);

        $words = array(
            'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
            'vbs', 'script', 'base64', 'applet', 'alert', 'document',
            'write', 'cookie', 'window', 'confirm', 'prompt'
        );

        foreach ($words as $word) {
            $word = implode('\s*', str_split($word)).'\s*';
            $str = preg_replace_callback(
                '#('.substr($word, 0, -3).')(\W)#is',
                array($this, 'compactExplodedWords'),
                $str
            );
        }

        do {
            $original = $str;

            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback(
                    '#<a[^a-z0-9>]+([^>]*?)(?:>|$)#si',
                    array($this, 'jsLinkRemoval'),
                    $str
                );
            }

            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback(
                    '#<img[^a-z0-9]+([^>]*?)(?:\s?/?>|$)#si',
                    array($this, 'jsImgRemoval'),
                    $str
                );
            }

            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            }
        } while ($original !== $str);

        unset($original);

        $str = $this->removeEvilAttributes($str);

        $naughty = 'alert|prompt|confirm|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|button|select|isindex|layer|link|meta|keygen|object|plaintext|style|script|textarea|title|math|video|svg|xml|xss';
        $str = preg_replace_callback(
            '#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is',
            array($this, 'sanitizeNaughtyHtml'),
            $str
        );

        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );


        $str = $this->doNeverAllowed($str);

        if ($str !== $old) {
            return $this->clean($str);
        }

        return $str;
    }

    /**
     * Generates the XSS hash if needed and returns it.
     *
     * @return  string
     */
    protected function xssHash()
    {
        if (!$this->xssHash) {
            $this->xssHash = md5(uniqid(mt_rand(), true));
        }

        return $this->xssHash;
    }

    /**
     * Removes invisible characters.
     *
     * @param   string
     * @param   bool
     * @return  string
     */
    protected function removeInvisibleCharacters($str, $urlEncoded = true)
    {
        $nonDisplayables = array();

        if ($urlEncoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/';
            $nonDisplayables[] = '/%1[0-9a-f]/';
        }

        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

        do {
            $str = preg_replace($nonDisplayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /**
     * HTML entities decode.
     *
     * @param   string  $str
     * @param   string  $charset
     * @return  string
     */
    protected function entityDecode($str, $charset = 'UTF-8')
    {
        if (strpos($str, '&') === false) {
            return $str;
        }

        do {
            $str_compare = $str;

            $str = preg_replace('/(&#x0*[0-9a-f]{2,5})(?![0-9a-f;])/iS', '$1;', $str);
            $str = preg_replace('/(&#0*\d{2,4})(?![0-9;])/S', '$1;', $str);
            $str = html_entity_decode($str, ENT_COMPAT, $charset);
        } while ($str_compare !== $str);

        return $str;
    }

    /**
     * Compact exploded words.
     *
     * @param   array   $matches
     * @return  string
     */
    protected function compactExplodedWords($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
    }

    /**
     * Remove evil html attributes.
     *
     * @param   string  $str
     * @return  string
     */
    protected function removeEvilAttributes($str)
    {
        $evilAttributes = array('on\w*', 'style', 'xmlns', 'formaction', 'form', 'xlink:href');

        do {
            $count = 0;
            $attribs = array();

            preg_match_all(
                '/(?<!\w)('.implode('|', $evilAttributes).')\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is',
                $str,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            preg_match_all(
                '/(?<!\w)('.implode('|', $evilAttributes).')\s*=\s*([^\s>]*)/is',
                $str,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            if (count($attribs) > 0) {
                $str = preg_replace(
                    '/(<?)(\/?[^><]+?)([^A-Za-z<>\-])(.*?)('.implode('|', $attribs).')(.*?)([\s><]?)([><]*)/i',
                    '$1$2 $4$6$7$8',
                    $str,
                    -1,
                    $count
                );
            }
        } while ($count);

        return $str;
    }

    /**
     * Sanitize naughty html.
     *
     * @param   array   $matches
     * @return  string
     */
    protected function sanitizeNaughtyHtml($matches)
    {
        return '&lt;'.$matches[1].$matches[2].$matches[3]
            .str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
    }

    /**
     * JS link removal.
     *
     * @param   array   $match
     * @return  string
     */
    protected function jsLinkRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
                '',
                $this->filterAttributes(str_replace(array('<', '>'), '', $match[1]))
            ),
            $match[0]
        );
    }

    /**
     * JS image removal.
     *
     * @param   array   $match
     * @return  string
     */
    protected function jsImgRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#src=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                '',
                $this->filterAttributes(str_replace(array('<', '>'), '', $match[1]))
            ),
            $match[0]
        );
    }

    /**
     * Attribute conversion.
     *
     * @param   array   $match
     * @return  string
     */
    protected function convertAttribute($match)
    {
        return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
    }

    /**
     * Attribute filtering.
     *
     * @param   string  $str
     * @return  string
     */
    protected function filterAttributes($str)
    {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace('#/\*.*?\*/#s', '', $match);
            }
        }

        return $out;
    }

    /**
     * HTML entity decode callback.
     *
     * @param   array   $match
     * @return  string
     */
    protected function decodeEntity($match)
    {
        $entities = array(
            '&colon;'   => ':',
            '&lpar;'    => '(',
            '&rpar;'    => ')',
            '&newline;' => "\n",
            '&tab;'     => "\t"
        );

        return str_ireplace(
            array_keys($entities),
            array_values($entities),
            $this->entityDecode($match[0])
        );
    }

    /**
     * Validate url entities.
     *
     * @param   string  $str
     * @return  string
     */
    protected function validateEntities($str)
    {
        $str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $this->xssHash().'\\1=\\2', $str);

        $str = preg_replace('/(&#\d{2,4})(?![0-9;])/', '$1;', $str);
        $str = preg_replace('/(&[a-z]{2,})(?![a-z;])/i', '$1;', $str);

        $str = preg_replace('/(&#x0*[0-9a-f]{2,5})(?![0-9a-f;])/i', '$1;', $str);

        return str_replace($this->xssHash(), '&', $str);
    }

    /**
     * Do never allowed.
     *
     * @param   string  $str
     * @return  string
     */
    protected function doNeverAllowed($str)
    {
        $never = array(
            'document.cookie'   => '[removed]',
            'document.write'    => '[removed]',
            '.parentNode'       => '[removed]',
            '.innerHTML'        => '[removed]',
            '-moz-binding'      => '[removed]',
            '<!--'              => '&lt;!--',
            '-->'               => '--&gt;',
            '<![CDATA['         => '&lt;![CDATA[',
            '<comment>'         => '&lt;comment&gt;'
        );

        $str = str_replace(array_keys($never), $never, $str);

        $regex = array(
            'javascript\s*:',
            '(document|(document\.)?window)\.(location|on\w*)',
            'expression\s*(\(|&\#40;)',
            'vbscript\s*:',
            'wscript\s*:',
            'jscript\s*:',
            'vbs\s*:',
            'Redirect\s+30\d',
            "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?"
        );

        foreach ($regex as $val) {
            $str = preg_replace('#'.$val.'#is', '[removed]', $str);
        }

        return $str;
    }
}
