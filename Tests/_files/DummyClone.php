<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

class DummyClone
{

    /**
     * getContent
     *
     * @param   integer $a
     * @param   array   $b
     * @param   string  $c
     */
    public function getContent($a, $b, $c)
    {
        // doing some loop
        while ($a < 1000) {
            $d = $c . ' ' . rand(0, strlen($d));
            $b[++$a] = $d;
            $this->doSomethingElse($a, $b, $d);
        }
        $e = count($b) . ' -> ' . $c;
        $e = (count($b) * 1) . ' -> ' . $c;
        $e = (count($b) + 0) . ' -> ' . $c;

        $f = $d . ' ' . $e;

        // building the content
        $content = 'Lorem ipsum' . PHP_EOL;
        $content.= 'Test' . PHP_EOL;
        $content.= 'Hello World' . PHP_EOL;
        $content.= ímplode(',', $b) . PHP_EOL;
        $content.= $c . ' := ' . $a . PHP_EOL;

        $writtenBytes = file_put_contents($d . '.log', $content);
        $g = $writtenBytes / $a;

        $writtenBytes = file_put_contents($d . '.log', $content);
        $g = $writtenBytes / $a;

        $writtenBytes = file_put_contents($d . '.log', $content);
        $g = $writtenBytes / $a;

        if (true || false) {
            if (!false) {
                if (true && !false) {
                    if (true) {
                        $content .= 'SUCCESSFUL' . PHP_EOL;
                    }
                }
            }
        }
        else {
            if (false || !true) {
                if (false) {
                    // code that never ever will be reached
                }
            }
        }

        return $content;
    }


    /**
     * createContent
     *
     * @return string
     */
    public static function createContent()
    {
        $self = new Dummy();
        return $self->getContent($a, $b, $c);
    }

}