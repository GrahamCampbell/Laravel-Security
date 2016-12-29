<?php

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Security;

/**
 * This is the security class.
 *
 * Some code in this class it taken from CodeIgniter 3.
 * See the original here: http://bit.ly/1oQnpjn.
 *
 * @author Andrey Andreev <narf@bofh.bg>
 * @author Derek Jones <derek.jones@ellislab.com>
 * @author Graham Campbell <graham@alt-three.com>
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
     * The evil attributes.
     *
     * @var string[]
     */
    protected $evil;

    /**
     * The entities to decode.
     *
     * @var string[]
     */
    protected $entities;

    /**
     * Create a new security instance.
     *
     * @param string[]|null $evil
     *
     * @return void
     */
    public function __construct(array $evil = null)
    {
        $this->evil = $evil ?: ['(?<!\w)on\w*', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'];
        $this->entities = array_map('strtolower', get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML5));
    }

    /**
     * XSS clean.
     *
     * @param string|string[] $str
     *
     * @return string
     */
    public function clean($str)
    {
        if (is_array($str)) {
            while (list($key) = each($str)) {
                $str[$key] = $this->clean($str[$key]);
            }

            return $str;
        }

        $i = 0;
        do {
            $i++;
            $processed = $this->process($str);
        } while ($i < 3 && $processed !== $str);

        return $processed;
    }

    /**
     * Process a string for cleaning.
     *
     * @param string $str
     *
     * @return string
     */
    protected function process($str)
    {
        $str = $this->removeInvisibleCharacters($str);

        if (stripos($str, '%') !== false) {
            do {
                $original = $str;
                $str = preg_replace_callback(
                    '#%(?:\s*[0-9a-f]){2,}#i',
                    [$this, 'urlDecodeSpaces'],
                    rawurldecode($str)
                );
            } while ($original !== $str);

            unset($original);
        }

        $str = preg_replace_callback(
            "/[^a-z0-9>]+[a-z0-9]+=([\'\"]).*?\\1/si",
            [$this, 'convertAttribute'],
            $str
        );

        $str = preg_replace_callback(
            '/<\w+.*?(?=>|<|$)/si',
            [$this, 'decodeEntity'],
            $str
        );

        $str = $this->removeInvisibleCharacters($str);

        $str = str_replace("\t", ' ', $str);

        $str = $this->doNeverAllowed($str);

        $str = str_replace(['<?', '?'.'>'], ['&lt;?', '?&gt;'], $str);

        $words = [
            'javascript', 'expression', 'vbscript', 'jscript', 'wscript',
            'vbs', 'script', 'base64', 'applet', 'alert', 'document',
            'write', 'cookie', 'window', 'confirm', 'prompt', 'eval',
        ];

        foreach ($words as $word) {
            $word = implode('\s*', str_split($word)).'\s*';
            $str = preg_replace_callback(
                '#('.substr($word, 0, -3).')(\W)#is',
                [$this, 'compactExplodedWords'],
                $str
            );
        }

        do {
            $original = $str;

            if (preg_match('/<a/i', $str)) {
                $str = preg_replace_callback(
                    '#<a[^a-z0-9>]+([^>]*?)(?:>|$)#si',
                    [$this, 'jsLinkRemoval'],
                    $str
                );
            }

            if (preg_match('/<img/i', $str)) {
                $str = preg_replace_callback(
                    '#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|$)#si',
                    [$this, 'jsImgRemoval'],
                    $str
                );
            }

            if (preg_match('/script|xss/i', $str)) {
                $str = preg_replace('#</*(?:script|xss).*?>#si', '[removed]', $str);
            }
        } while ($original !== $str);

        unset($original);

        $pattern = '#'
            .'<((?<slash>/*\s*)(?<tagName>[a-z0-9]+)(?=[^a-z0-9]|$)'
            .'[^\s\042\047a-z0-9>/=]*'
            .'(?<attributes>(?:[\s\042\047/=]*'
            .'[^\s\042\047>/=]+'
                .'(?:\s*='
                    .'(?:[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*))'
                .')?'
            .')*)'
            .'[^>]*)(?<closeTag>\>)?#isS';

        do {
            $original = $str;
            $str = preg_replace_callback($pattern, [$this, 'sanitizeNaughtyHtml'], $str);
        } while ($original !== $str);

        unset($original);

        $str = preg_replace(
            '#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            '\\1\\2&#40;\\3&#41;',
            $str
        );

        return $this->doNeverAllowed($str);
    }

    /**
     * Generates the XSS hash if needed and returns it.
     *
     * @return string
     */
    protected function xssHash()
    {
        if (!$this->xssHash) {
            $this->xssHash = str_random(40);
        }

        return $this->xssHash;
    }

    /**
     * Removes invisible characters.
     *
     * @param string $str
     * @param bool   $urlEncoded
     *
     * @return string
     */
    protected function removeInvisibleCharacters($str, $urlEncoded = true)
    {
        $nonDisplayables = [];

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
     * @param string $str
     *
     * @return string
     */
    protected function entityDecode($str)
    {
        if (strpos($str, '&') === false) {
            return $str;
        }

        do {
            $original = $str;

            if (preg_match_all('/&[a-z]{2,}(?![a-z;])/i', $str, $matches)) {
                $replace = [];
                $matches = array_unique(array_map('strtolower', $matches[0]));
                foreach ($matches as &$match) {
                    if (($char = array_search($match.';', $this->entities, true)) !== false) {
                        $replace[$match] = $char;
                    }
                }
                $str = str_replace(array_keys($replace), array_values($replace), $str);
            }

            $str = html_entity_decode(
                preg_replace('/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\d{2,4}(?![0-9;]))))/iS', '$1;', $str),
                ENT_COMPAT | ENT_HTML5
            );
        } while ($original !== $str);

        return $str;
    }

    /**
     * URL decode taking spaces into account.
     *
     * @param array $matches
     *
     * @return string
     */
    protected function urlDecodeSpaces($matches)
    {
        $input = $matches[0];
        $nospaces = preg_replace('#\s+#', '', $input);

        return $nospaces === $input ? $input : rawurldecode($nospaces);
    }

    /**
     * Compact exploded words.
     *
     * @param array $matches
     *
     * @return string
     */
    protected function compactExplodedWords($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
    }

    /**
     * Sanitize naughty html.
     *
     * @param array $matches
     *
     * @return string
     */
    protected function sanitizeNaughtyHtml($matches)
    {
        static $tags = [
            'alert', 'area', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
            'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
            'iframe', 'input', 'button', 'select', 'isindex', 'layer', 'link', 'meta', 'keygen', 'object',
            'plaintext', 'style', 'script', 'textarea', 'title', 'math', 'video', 'svg', 'xml', 'xss',
        ];

        static $evilAttributes = [
            'on\w+', 'style', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime',
        ];

        if (empty($matches['closeTag'])) {
            return '&lt;'.$matches[1];
        }

        if (in_array(strtolower($matches['tagName']), $tags, true)) {
            return '&lt;'.$matches[1].'&gt;';
        }

        if (isset($matches['attributes'])) {
            $attributes = [];

            $pattern = '#'
                .'(?<name>[^\s\042\047>/=]+)'
                .'(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))'
                .'#i';

            $isEvil = '#^('.implode('|', $evilAttributes).')$#i';

            do {
                $matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

                if (!preg_match($pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE)) {
                    break;
                }

                if (preg_match($isEvil, $attribute['name'][0]) || trim($attribute['value'][0]) === '') {
                    $attributes[] = 'xss=removed';
                } else {
                    $attributes[] = $attribute[0][0];
                }

                $matches['attributes'] = substr(
                    $matches['attributes'],
                    $attribute[0][1] + strlen($attribute[0][0])
                );
            } while ($matches['attributes'] !== '');

            $attributes = empty($attributes) ? '' : ' '.implode(' ', $attributes);

            return '<'.$matches['slash'].$matches['tagName'].$attributes.'>';
        }

        return $matches[0];
    }

    /**
     * JS link removal.
     *
     * @param array $match
     *
     * @return string
     */
    protected function jsLinkRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#href=.*?(?:(?:alert|prompt|confirm)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|d\s*a\s*t\s*a\s*:)#si',
                '',
                $this->filterAttributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * JS image removal.
     *
     * @param array $match
     *
     * @return string
     */
    protected function jsImgRemoval($match)
    {
        return str_replace(
            $match[1],
            preg_replace(
                '#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\(|&\#40;)|javascript:|livescript:|mocha:|charset=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                '',
                $this->filterAttributes($match[1])
            ),
            $match[0]
        );
    }

    /**
     * Attribute conversion.
     *
     * @param array $match
     *
     * @return string
     */
    protected function convertAttribute($match)
    {
        return str_replace(['>', '<', '\\'], ['&gt;', '&lt;', '\\\\'], $match[0]);
    }

    /**
     * Attribute filtering.
     *
     * @param string $str
     *
     * @return string
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
     * @param array $match
     *
     * @return string
     */
    protected function decodeEntity($match)
    {
        $hash = $this->xssHash();

        $match = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-/]+)|i', $hash.'\\1=\\2', $match[0]);

        return str_replace($hash, '&', $this->entityDecode($match));
    }

    /**
     * Do never allowed.
     *
     * @param string $str
     *
     * @return string
     */
    protected function doNeverAllowed($str)
    {
        $never = [
            'document.cookie' => '[removed]',
            'document.write'  => '[removed]',
            '.parentNode'     => '[removed]',
            '.innerHTML'      => '[removed]',
            '-moz-binding'    => '[removed]',
            '<!--'            => '&lt;!--',
            '-->'             => '--&gt;',
            '<![CDATA['       => '&lt;![CDATA[',
            '<comment>'       => '&lt;comment&gt;',
            '<%'              => '&lt;&#37;',
        ];

        $str = str_replace(array_keys($never), $never, $str);

        $regex = [
            'javascript\s*:',
            '(document|(document\.)?window)\.(location|on\w*)',
            'expression\s*(\(|&\#40;)',
            'vbscript\s*:',
            'wscript\s*:',
            'jscript\s*:',
            'vbs\s*:',
            'Redirect\s+30\d',
            "([\"'])?data\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?",
        ];

        foreach ($regex as $val) {
            $str = preg_replace('#'.$val.'#is', '[removed]', $str);
        }

        return $str;
    }
}
