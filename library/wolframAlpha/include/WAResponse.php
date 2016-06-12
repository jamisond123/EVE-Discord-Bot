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
 *  The Wolfram Alpha Reponse Object
 *  @package WolframAlpha
 */
class WAResponse {
  // define the sections of a response
  public $attributes = array();
  public $error = array();
  public $rawXML = '';
  public $script = '';
  public $css = '';
 
  // private accessors
  private $pods = array();
  private $assumptions = array();

  // Constructor
  public function WAResponse () {
  }

  public function isError() {
    if ( $this->attributes['error'] == 'true' ) {
      return true;
    }
    return false;
  }

  /**
   *  Add a pod to this response object
   *  @param WAPod $pod	A WAPod object to be added
   */
  public function addPod( $pod ) {
    $this->pods[] = $pod;
  }

  /**
   *  Add an assumption to this response object
   *  @param WAAssumption $assumption A WAAssumption object to be added
   */
  public function addAssumption( $assumption ) {
    if ( !isset( $this->assumptions[$assumption->type] ) ) {
      $this->assumptions[$assumption->type] = array();
    }

    $this->assumptions[$assumption->type][] = $assumption;
  }

  /**
   *  Get the pods associated with this response
   *  @return array( WAPod )         An array of pods
   */
  public function getPods() {
    return $this->pods;
  }

  /**
   *  Get the assumptions associated with this response
   *  @return array( WAAssumption )         An array of assumptions
   */
  public function getAssumptions() {
    return $this->assumptions;
  }
}
