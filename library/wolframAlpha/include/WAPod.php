<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Robert Sardinia
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/** 
 *  The Wolfram Alpha Pod Object
 *  @package WolframAlpha
 */
class WAPod {
  // define the sections of a response
  public $attributes = array();
  public $markup = '';
  
  // private accessors
  private $subpods = array();
  private $substitutions = array();
  private $infos = array();
 
  // Constructor
  public function WAPod () {
  }

  /**
   *  Add a subpod to this pod
   *  @param WASubpod $subpod	the subpod to be added
   */
  public function addSubpod( $subpod ) {
    $this->subpods[] = $subpod;
  }

  /**
   *  Add a substitution to this pod
   *  @param WASubstitution $sub   the substitution to be added
   */
  public function addSubstitution( $sub ) {
    $this->substitutions[] = $sub;
  }

  /**
   *  Add an info to this pod
   *  @param WAInfo $info   the info to be added
   */
  public function addInfo( $info ) {
    $this->infos[] = $info;
  }

  /**
   *  Get the subpods associated with this pod
   *  @return array( WASubpod ) 	An array of subpods
   */
  public function getSubpods() {
    return $this->subpods;
  }

  /**
   *  Get the substitutions associated with this pod
   *  @return array( WASubstitution )         An array of substitutions
   */
  public function getSubstitutions() {
    return $this->substitutions;
  }

  /**
   *  Get the infos associated with this pod
   *  @return array( WAInfo )         An array of infos
   */
  public function getInfos() {
    return $this->infos;
  }
}

