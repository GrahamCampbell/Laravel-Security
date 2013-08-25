<?php namespace GrahamCampbell\Security\Classes;

class Security {

    /**
     * XSS clean.
     *
     * @param  string  $str
     * @param  bool    $is_image
     * @return string
     */
    public function xss_clean($str, $is_image = FALSE) {
        if (is_array($str)) {
            while (list($key) = each($str)) {
                $str[$key] = $this->xss_clean($str[$key]);
            }
            return $str;
        }

        // remove the invisible characters
        $str = $this->remove_invisible_characters($str);

        // validate entities in urls
        $str = $this->validate_entities($str);

        // url decode
        $str = rawurldecode($str);

        // remove HTML Tags
        $str = strip_tags($str);

        // convert character entities to ascii
        $str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", function($match) {
            return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
        }, $str);

        // convert character entities to ascii (part 2)
        $str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", function($match) {
            return $this->entity_decode($match[0], 'UTF-8');
        }, $str);

        // remove the invisible characters again
        $str = $this->remove_invisible_characters($str);

        // convert all tabs to spaces
        if (strpos($str, "\t") !== FALSE)
        {
            $str = str_replace("\t", ' ', $str);
        }

        // capture the converted string for later comparison
        $converted_string = $str;

        // remove Strings that are never allowed
        $str = $this->do_never_allowed($str);

        // make php tags safe
        if ($is_image === TRUE) {
            $str = preg_replace('/<\?(php)/i', "&lt;?\\1", $str);
        } else {
            $str = str_replace(array('<?', '?'.'>'),  array('&lt;?', '?&gt;'), $str);
        }

        // compact any exploded words
        $words = array(
            'javascript', 'expression', 'vbscript', 'script', 'base64',
            'applet', 'alert', 'document', 'write', 'cookie', 'window'
        );

        // compact any exploded words (part 2)
        foreach ($words as $word) {
            $temp = '';

            for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++) {
                $temp .= substr($word, $i, 1)."\s*";
            }

            $str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', function($matches) {
                return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
            }, $str);
        }

        // remove js in links or img tags
        do {
            $original = $str;

            if (preg_match("/<a/i", $str)) {
                $str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", function($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#href=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|data\s*:)#si',
                            '',
                            $this->filter_attributes(str_replace(array('<', '>'), '', $match[1]))
                        ),
                        $match[0]
                    );
                }, $str);
            }

            if (preg_match("/<img/i", $str)) {
                $str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", function($match) {
                    return str_replace(
                        $match[1],
                        preg_replace(
                            '#src=.*?(alert\(|alert&\#40;|javascript\:|livescript\:|mocha\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si',
                            '',
                            $this->filter_attributes(str_replace(array('<', '>'), '', $match[1]))
                        ),
                        $match[0]
                    );

                }, $str);
            }

            if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str)) {
                $str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
            }
        }

        while($original != $str);

        unset($original);

        // remove evil attributes such as style, onclick and xmlns
        $str = $this->remove_evil_attributes($str, $is_image);

        // sanitize naughty html elements
        $naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
        $str = preg_replace_callback('#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is', function($matches) {
            $str = '&lt;'.$matches[1].$matches[2].$matches[3];
            return $str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
        }, $str);

        // sanitize naughty scripting elements
        $str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);

        // final clean up
        $str = $this->do_never_allowed($str);

        // images are handled in a special way
        if ($is_image === TRUE) {
            return ($str == $converted_string) ? TRUE: FALSE;
        }

        return $str;
    }

    /**
     * Remove invisible characters.
     *
     * @param  string  $str
     * @param  bool    $url_encoded
     * @return string
     */
    public function remove_invisible_characters($str, $url_encoded = TRUE) {
        $non_displayables = array();

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/';  // url encoded 16-31
        }
        
        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }

        while ($count);

        return $str;

    }

    /**
     * Validate entities.
     *
     * @param  string   $str
     * @return string
     */
    public function validate_entities($str) {
        $xss_hash = md5(time() + mt_rand(0, 1999999999));

        $str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $xss_hash."\\1=\\2", $str);

        $str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);

        $str = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);

        $str = str_replace($xss_hash, '&', $str);

        return $str;

    }

    /**
     * Do never allowed.
     *
     * @param  string   $str
     * @return string
     */
    public function do_never_allowed($str) {
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

        $str = str_replace(array_keys($never_allowed_str), $never_allowed_str, $str);

        foreach ($never_allowed_regex as $regex) {
            $str = preg_replace('#'.$regex.'#is', '[removed]', $str);
        }

        return $str;

    }

    /**
     * Remove evil attributes.
     *
     * @param  string  $str
     * @param  bool    $is_image
     * @return string
     */
    public function remove_evil_attributes($str, $is_image) {
        $evil_attributes = array('on\w*', 'style', 'xmlns', 'formaction');

        if ($is_image === TRUE) {
            unset($evil_attributes[array_search('xmlns', $evil_attributes)]);
        }

        do {
            $count = 0;
            $attribs = array();

            // find occurrences of illegal attribute strings without quotes
            preg_match_all('/('.implode('|', $evil_attributes).')\s*=\s*([^\s>]*)/is', $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // find occurrences of illegal attribute strings with quotes (042 and 047 are octal quotes)
            preg_match_all("/(".implode('|', $evil_attributes).")\s*=\s*(\042|\047)([^\\2]*?)(\\2)/is",  $str, $matches, PREG_SET_ORDER);

            foreach ($matches as $attr) {
                $attribs[] = preg_quote($attr[0], '/');
            }

            // replace illegal attribute strings that are inside an html tag
            if (count($attribs) > 0) {
                $str = preg_replace("/<(\/?[^><]+?)([^A-Za-z<>\-])(.*?)(".implode('|', $attribs).")(.*?)([\s><])([><]*)/i", '<$1 $3$5$6$7', $str, -1, $count);
            }

        } while ($count);

        return $str;
    }

    /**
     * Entity decode.
     *
     * @param  string  $str
     * @param  string  $charset
     * @return string
     */
    public function entity_decode($str, $charset='UTF-8') {
        if (stristr($str, '&') === FALSE) {
            return $str;
        }

        $str = html_entity_decode($str, ENT_COMPAT, $charset);
        $str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
        return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
    }

    /**
     * Filter attributes.
     *
     * @param  string  $str
     * @return string
     */
    public function filter_attributes($str) {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }

        return $out;
    }
}
