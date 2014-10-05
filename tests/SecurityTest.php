<?php

/**
 * This file is part of Laravel Security by Graham Campbell.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at http://bit.ly/UWsjkb.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace GrahamCampbell\Tests\Security;

use GrahamCampbell\Security\Security;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;

/**
 * This is the security test class.
 *
 * @author    Graham Campbell <graham@mineuk.com>
 * @copyright 2013-2014 Graham Campbell
 * @license   <https://github.com/GrahamCampbell/Laravel-Security/blob/master/LICENSE.md> Apache 2.0
 */
class SecurityTest extends AbstractTestBenchTestCase
{
    public function snippetProvider()
    {
        return array(
            array(
                'Hello, try to <script>alert(\'Hack\');</script> this site',
                'Hello, try to [removed]alert&#40;\'Hack\'&#41;;[removed] this site',
            ),
            array(
                '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>',
                '<a >Clickhere</a>',
            ),
            array(
                '&foo should not include a semicolon',
                '&foo should not include a semicolon',
            ),
            array(
                './<!--foo-->',
                './&lt;!--foo--&gt;',
            ),
            array(
                '<div style="color:rgb(\'\'&#0;x:expression(alert(1))"></div>',
                '<div ></div>',
            ),
            array(
                '<img/src=%00 id=confirm(1) onerror=eval(id)',
                '<img/',
            ),
            array(
                '<div id=confirm(1) onmouseover=eval(id)>X</div>',
                '<div id=confirm&#40;1&#41; >X</div>',
            ),
            array(
                '<span/onmouseover=confirm(1)>X</span>',
                '<span >X</span>',
            ),
            array(
                '<svg/contentScriptType=text/vbs><script>Execute(MsgBox(chr(88)&chr(83)&chr(83)))',
                '&lt;svg/contentScriptType=text/vbs&gt;[removed]Execute(MsgBox(chr(88)&chr(83)&chr(83)))',
            ),
            array(
                '<iframe/src="javascript:a=[alert&lpar;1&rpar;,confirm&#40;2&#41;,prompt%283%29];eval(a[0]);">',
                '&lt;iframe/src="[removed]a=[alert&#40;1&#41;,confirm&#40;2&#41;,prompt&#40;3&#41;];eval&#40;a[0]&#41;;"&gt;',
            ),
            array(
                '<div/style=content:url(data:image/svg+xml);visibility:visible onmouseover=alert(1)>x</div>',
                '<div  >x</div>',
            ),
            array(
                '<script>Object.defineProperties(window,{w:{value:{f:function(){return 1}}}});confirm(w.f())</script>',
                '[removed]Object.defineProperties(window,{w:{value:{f:function(){return 1}}}});confirm&#40;w.f(&#41;)[removed]',
            ),
            array(
                '<keygen/onfocus=prompt(1);>',
                '&lt;keygen &gt;',
            ),
            array(
                '<img/src=`%00` id=confirm(1) onerror=eval(id)',
                '<img/',
            ),
            array(
                '<img/src=`%00` onerror=this.onerror=confirm(1)',
                '<img/',
            ),
            array(
                '<iframe/src="data:text/html,<iframe%09onload=confirm(1);>">',
                '&lt;iframe src="data:text/html,&lt;iframe >">',
            ),
            array(
                '<math><a/xlink:href=javascript:prompt(1)>X',
                '&lt;math&gt;&lt;a/>X',
            ),
            array(
                '<input/type="image"/value=""`<span/onmouseover=\'confirm(1)\'>X`</span>',
                '&lt;input type="image"/value=""`&lt;span/>X`</span>',
            ),
            array(
                '<form/action=javascript&#x0003A;eval(setTimeout(confirm(1)))><input/type=submit>',
                '&lt;form/action=javascript&#x0003;A;eval&#40;setTimeout(confirm(1&#41;))&gt;&lt;input/type=submit>',
            ),
            array(
                '<body/onload=this.onload=document.body.innerHTML=alert&lpar;1&rpar;>',
                '&lt;body &gt;',
            ),
            array(
                '<iframe/onload=\'javascript&#58;void&#40;1&#41;&quest;void&#40;1&#41;&#58;confirm&#40;1&#41;\'>',
                '&lt;iframe &gt;',
            ),
            array(
                '<object/type="text/x-scriptlet"/data="data:X,&#60script&#62setInterval&lpar;\'prompt(1)\',10&rpar;&#60/script&#62"></object>',
                '&lt;object/type="text/x-scriptlet"/data="data:X,[removed]setInterval(\'prompt&#40;1&#41;\',10;)[removed]"&gt;&lt;/object>',
            ),
            array(
                '<i<f<r<a<m<e><iframe/onload=confirm(1);></i>f>r>a>m>e>',
                '<i<f<r<a<>&lt;iframe &gt;&lt;/i>f>r>a>m>e>',
            ),
            array(
                'http://www.<script abc>setTimeout(\'confirm(1)\',1)</script .com>',
                'http://www.[removed]setTimeout(\'confirm&#40;1&#41;\',1)[removed]',
            ),
            array(
                '<style/onload    =    !-alert&#x28;1&#x29;>',
                '&lt;style &gt;',
            ),
            array(
                '<svg id=a /><script language=vbs for=a event=onload>alert 1</script>',
                '&lt;svg id=a /&gt;[removed]alert 1[removed]',
            ),
            array(
                '<object/data="data&colon;X&comma;&lt;script&gt;alert&#40;1&#41;%3c&sol;script%3e">',
                '&lt;object/data="data:X,[removed]alert&#40;1&#41;[removed]"&gt;',
            ),
            array(
                '<form/action=javascript&#x3A;void(1)&quest;void(1)&colon;alert(1)><input/type=\'submit\'>',
                '&lt;form/action=[removed]void(1)?void(1):alert&#40;1&#41;&gt;&lt;input/type=\'submit\'>',
            ),
            array(
                '<iframe/srcdoc=\'&lt;iframe&sol;onload&equals;confirm(&sol;&iexcl;&hearts;&xcup;&sol;)&gt;\'>',
                '&lt;iframe srcdoc=\'&lt;iframe/>\'>',
            ),
            array(
                '<meta/http-equiv="refresh"/content="0;url=javascript&Tab;:&Tab;void(alert(0))?0:0,0,prompt(0)">',
                '&lt;meta/http-equiv="refresh"/content="0;url=[removed] void(alert&#40;0&#41;)?0:0,0,prompt&#40;0&#41;"&gt;',
            ),
            array(
                '<script src="h&Tab;t&Tab;t&Tab;p&Tab;s&colon;/&Tab;/&Tab;http://dl.dropbox.com/u/13018058/js.js"></script>',
                '[removed][removed]',
            ),
            array(
                '<style/onload=\'javascript&colon;void(0)?void(0)&colon;confirm(1)\'>',
                '&lt;style &gt;',
            ),
            array(
                '<svg><style>&#x7B;-o-link-source&#x3A;\'<style/onload=confirm(1)>\'&#x7D;',
                '&lt;svg&gt;&lt;style>& x7B;-o-link-source&#x3A;\'&lt;style/&gt;\'&#x7D;',
            ),
            array(
                '<math><solve i.e., x=2+2*2-2/2=? href="data:text/html,<script>prompt(1)</script>">X',
                '&lt;math&gt;&lt;solve i.e., x=2+2*2-2/2=? href="data:text/html,[removed]prompt&#40;1&#41;[removed]">X',
            ),
            array(
                '<iframe/src="j&Tab;AVASCRIP&NewLine;t:\u0061ler\u0074&#x28;1&#x29;">',
                '&lt;iframe/src="[removed]\\\u0061;ler\\\u0074;(1)"&gt;',
            ),
            array(
                '<iframe/src="javascript:void(alert(1))?alert(1):confirm(1),prompt(1)">',
                '&lt;iframe/src="[removed]void(alert&#40;1&#41;)?alert&#40;1&#41;:confirm&#40;1&#41;,prompt&#40;1&#41;"&gt;',
            ),
            array(
                '<embed/src=javascript&colon;\u0061&#x6C;&#101%72t&#x28;1&#x29;>',
                '&lt;embed/src=[removed]\u0061;lert(1)&gt;',
            ),
            array(
                '<img/src=\'http://i.imgur.com/P8mL8.jpg \' onmouseover={confirm(1)}f()>',
                '<img/src=\'http://i.imgur.com/P8mL8.jpg \'>',
            ),
            array(
                '<style/&Tab;/onload=;&Tab;this&Tab;.&Tab;onload=confirm(1)>',
                '&lt;style  / this . &gt;',
            ),
            array(
                '<embed/src=//goo.gl/nlX0P>',
                '&lt;embed/src=//goo.gl/nlX0P&gt;',
            ),
            array(
                '<form><button formaction=javascript:alert(1)>CLICKME',
                '&lt;form&gt;&lt;button >CLICKME',
            ),
            array(
                '<script>x=\'con\';s=\'firm\';S=\'(1)\';setTimeout(x+s+S,0);</script>',
                '[removed]x=\'con\';s=\'firm\';S=\'(1)\';setTimeout(x+s+S,0);[removed]',
            ),
            array(
                '<img/id="confirm&lpar;1&#x29;"/alt="/"src="/"onerror=eval(id&#x29;>',
                '<img/id="confirm&#40;1&#41;"alt="/"src="/">',
            ),
            array(
                '<iframe/src="data&colon;text&sol;html,<s&Tab;cr&Tab;ip&Tab;t>confirm(1)</script>">',
                '&lt;iframe/src="data:text/html,[removed]confirm&#40;1&#41;[removed]"&gt;',
            ),
        );
    }

    /**
     * @dataProvider snippetProvider
     */
    public function testCleanString($input, $output)
    {
        $security = $this->getSecurity();

        $return = $security->clean($input);

        $this->assertSame($output, $return);
    }

    public function testCleanArray()
    {
        $security = $this->getSecurity();

        $return = $security->clean(array('test', '123', array('abc')));

        $this->assertSame(array('test', '123', array('abc')), $return);
    }

    protected function getSecurity()
    {
        return new Security();
    }
}
