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
 * Abstract base class for strategies to detect code clones.
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @author    Steffen Zeidler <steff.zeidler@googlemail.com>
 * @copyright 2009-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.4.0
 */
abstract class PHPCPD_Detector_Strategy
{

    /**
     * @var array
     */
    protected $hashes = array();

    /**
     * number of tokens, that have to match at least
     * @var integer
     */
    protected $minLines;

    /**
     * number of tokens, that have to match at least
     * @var integer
     */
    protected $minTokens;

    /**
     * clone map
     * @var PHPCPD_CloneMap
     */
    protected $cloneMap;

    /**
     * @var integer[] List of tokens to ignore
     */
    protected $tokensIgnoreList = array(
      T_INLINE_HTML => TRUE,
      T_COMMENT => TRUE,
      T_DOC_COMMENT => TRUE,
      T_OPEN_TAG => TRUE,
      T_OPEN_TAG_WITH_ECHO => TRUE,
      T_CLOSE_TAG => TRUE,
      T_WHITESPACE => TRUE
    );


    /**
     * Copy & Paste Detection (CPD).
     *
     * @param string          $file
     * @param integer         $minLines
     * @param integer         $minTokens
     * @param PHPCPD_CloneMap $result
     */
    abstract public function processFile($file);


    /**
     * Sets minimum lines.
     *
     * @param integer $minLines
     */
    public function setMinLines($minLines)
    {
        $this->minLines = $minLines;
    }


    /**
     * Sets minimum tokens.
     *
     * @param integer $minTokens
     */
    public function setMinTokens($minTokens)
    {
        $this->minTokens = $minTokens;
    }


    public function setCloneMap($cloneMap)
    {
        $this->cloneMap = $cloneMap;
    }


    /**
     * Clears hash map.
     */
    public function clearHashes()
    {
        $this->hashes = array();
    }

}
