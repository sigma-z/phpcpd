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
 * @since     File available since Release 1.4.0
 */

/**
 * Default strategy for detecting code clones.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @author    Steffen Zeidler <steff.zeidler@googlemail.com>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.4.0
 */
class PHPCPD_Detector_Strategy_Default extends PHPCPD_Detector_Strategy
{

    /**
     * @var array
     */
    private $tokens;
    /**
     * signature calculated from relevant tokens
     * @var string
     */
    private $signature;
    /**
     * signature calculated from relevant anonymized tokens
     * @var string
     */
    private $signatureForSimilarCompare;
    /**
     * @var array
     */
    private $tokenPositions;


    /**
     * Initialize file processing.
     */
    protected function init()
    {
        $this->tokens = array();
        $this->signature = '';
        $this->signatureForSimilarCompare = '';
        $this->tokenPositions = array();
    }


    /**
     * Parses files.
     *
     * @param string $file
     */
    private function parseFile($file)
    {
        $buffer = file_get_contents($file);
        $this->tokens = token_get_all($buffer);

        $this->cloneMap->setNumLines(
            $this->cloneMap->getNumLines() + substr_count($buffer, "\n")
        );
    }


    /**
     * Generates signatures.
     */
    private function generateSignatures()
    {
        $line = 1;
        $tokenIndex = 0;

        foreach (array_keys($this->tokens) as $key) {
            $token = $this->tokens[$key];

            if (is_string($token)) {
                $line += substr_count($token, "\n");
            }
            else {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    $this->tokenPositions[$tokenIndex++] = $line;
                    $this->signature .= chr($token[0] & 255) . pack('N*', crc32($token[1]));
                }

                $line += substr_count($token[1], "\n");
            }
        }
    }


    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  string          $file
     * @author Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
     */
    public function processFile($file)
    {
        $this->init();
        $this->parseFile($file);
        $this->generateSignatures();
        $this->detectCopyPaste($file);
    }


    /**
     * Detects copy paste.
     *
     * @param string $file
     */
    private function detectCopyPaste($file)
    {
        $count     = count($this->tokenPositions);
        $firstLine = 0;
        $found     = FALSE;
        $tokenIndex = 0;

        if ($count > 0) {
            do {
                $line    = $this->tokenPositions[$tokenIndex];
                $chunk   = substr($this->signature, $tokenIndex * 5, $this->minTokens * 5);
                $md5Hash = md5($chunk, TRUE);
                $hash    = substr($md5Hash, 0, 8);

                if (isset($this->hashes[$hash])) {
                    $found = TRUE;

                    if ($firstLine === 0) {
                        $firstLine  = $line;
                        $firstHash  = $hash;
                        $firstToken = $tokenIndex;
                    }
                }
                else {
                    $this->hashes[$hash] = array($file, $line);

                    if ($found) {
                        $this->addClone($firstHash, $hash, $firstLine, $firstToken, $tokenIndex);

                        $found     = FALSE;
                        $firstLine = 0;
                    }
                }

                $tokenIndex++;
            }
            while ($tokenIndex <= $count - $this->minTokens + 1);
        }

        if ($found) {
            $this->addClone($firstHash, $hash, $firstLine, $firstToken, $tokenIndex);
        }

        $this->hashes = $this->hashes;
    }


    /**
     * Adds clone if clone size is equal minimum number of lines at least and does not match itself.
     *
     * @param string  $firstHash
     * @param string  $hash
     * @param integer $firstLine
     * @param integer $firstToken
     * @param integer $tokenIndex
     */
    private function addClone($firstHash, $hash, $firstLine, $firstToken, $tokenIndex)
    {
        $fileA      = $this->hashes[$firstHash][0];
        $firstLineA = $this->hashes[$firstHash][1];
        $file       = $this->hashes[$hash][0];
        $line       = $this->hashes[$hash][1];
        $lineSize   = $line + 1 - $firstLine;
        $tokenSize  = $tokenIndex + 1 - $firstToken;

        if ($lineSize > $this->minLines && ($fileA != $file || $firstLineA != $firstLine)) {
            $clone = new PHPCPD_Clone(
                    $fileA,
                    $firstLineA,
                    $file,
                    $firstLine,
                    $lineSize,
                    $tokenSize
            );
            $this->cloneMap->addClone($clone);
        }
    }

}
