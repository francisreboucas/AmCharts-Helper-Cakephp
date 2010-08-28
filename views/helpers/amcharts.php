<?php
/* SVN FILE: $Id$ */
/**
 * Amchart Helper class file.
 *
 * Simplifies the use Amchart Flash charts.
 * AmCharts is a set of Flash charts for your websites and Web-based products.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.view.helpers
 * @since         CakePHP(tm) v 1.2
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class AmchartsHelper extends AppHelper {
	
	var $helpers = array('Javascript','Xml');
	
	private $chart = null;
	private $title = '';
	private $config = array();
	private $graphs = array();
	private $labels = array();	
	private $slices = array();
	private $series = array();
	private $axis = array();
	
	public $swfobject = "";
	public $jquery = "";
	public $libpath = 'amchart/';
	public $chartpath = '';
	public $swf = '';
	
	private $id;
	private static $js = false;
	
	/**
	 * Adds a new chart.
	 *
	 * @param	string				$_type
	 * @param	array				$_config
	 * @param	string				$_id
	 * @public
	 */	
	public function chart($_type, array $_config = array(),$_id = null){
		$this->$_type($_config,$_id);
	}
	/**
	 * Adds a new column chart (data line/bar).
	 *
	 * @param	string				$_id
	 * @param	array				$_config
	 * @public	 
	 */	

	public function column(array $_config = array(),$_id = null){
		$this->__setId($_id);
		$this->swf = 'amcolumn.swf';
		$this->chartpath = $this->libpath.'amcolumn/';
		$this->config = $_config;
		$this->chart = 'chart';
		
	}
	/**
	 * Adds a new line chart (data line).
	 *
	 * @param	string				$_id
	 * @param	array				$_config
	 * @public	
	 */	
	
	public function line( array $_config = array(),$_id = null){
		$this->__setId($_id);
		$this->swf = 'amline.swf';
		$this->chartpath = $this->libpath.'amline/';
		$this->config = $_config;
		$this->chart = 'chart';
		
	}
	
	/**
	* Adds a new pie chart 
	*
	* @param	string				$_id
	* @param	array				$_config
	* @public	
	*/
	 
	public function pie(array $_config = array(),$_id = null ){
		$this->__setId($_id);
		$this->swf = 'ampie.swf';
		$this->chartpath = $this->libpath.'ampie/';
		$this->config = $_config;
		$this->chart = 'pie';
	}
	/**
	* Adds a new xy chart 
	*
	* @param	string				$_id
	* @param	array				$_config
	* @public
	*/
	 
	public function xy(array $_config = array(), $_id = null){
		$this->__setId($_id);
		$this->swf = 'amxy.swf';
		$this->chartpath = $this->libpath.'amxy/';
		$this->config = array_merge($_config,array('chart'=>'xy'));
		$this->chart = 'chart';
	}
	/**
	* Adds a new radar chart 
	*
	* @param	string				$_id
	* @param	array				$_config
	* @public
	*/
	 
	public function radar( array $_config = array(),$_id = null){
		$this->__setId($_id);
		$this->swf = 'amradar.swf';
		$this->chartpath = $this->libpath.'amradar/';
		$this->config = $_config;
		$this->chart = 'chart';
	}
	/**
	 * Adds a new graph (data line/bar/xy).
	 *
	 * @param	string				$_id
	 * @param	string				$_title
	 * @param	array				$_data
	 * @param	array				$_config
	 * @public
	 */
	
	public function addGraph($_id, $_title ,array $_data = array(), array $_config = array()){
		
		$this->graphs[$_id] = array(
			'id' => $_id,						
			"title" => $_title,
			"data" => $_data,
			"config" => $_config
		);	
		
		
	}
	
	/**
	 * Adds a new serie (value on the X axis).
	 *
	 * @param	string				$_id
	 * @param	string				$_title
	 * @param	array				$_config
	 * @public	 
	 */
	
	public function addSerie($_id, $_title,array $_data = array(), array $_config = array()){
		
		$this->series[$_id] = array(
			'id' => $_id,
			"title" => $_title,
			"config" => $_config
		);
		
	}
	/**
	 * Adds a new slice to the pie chart.
	 *
	 * @param	string				$_id
	 * @param	string				$_title
	 * @param	mixed				$_value
	 * @param	array				$_config
	 * @public
	 */
	public function addSlice($_id, $_title, $_value, array $_config = array()) {

		$this->slices[$_id] = array(
			'id' => $_id,
			"title" => $_title,
			"value" => $_value,
			"config" => $_config
		);

	}
	/**
	 * Adds a new axis to the radar chart.
	 *
	 * @param	string				$_id
	 * @param	string				$_title
	 * @param	mixed				$_value
	 * @param	array				$_config
	 * @public
	 */
	public function addAxis($_id, $_title) {

		$this->axis[$_id] = array(
			'id' => $_id,
			"title" => $_title
		);

	}	
	/**
	* Returns code for generating the graph. Required SWFObject
	* 
	* @param	string				$_width
	* @param	string				$_height
	* @return string XML	
	* @public
	*/
	
	public function getCode($_width = '600',$_height = '400'){
		$this->swfobject = $this->Javascript->link('swfobject');
		$code = '';
		if(!self::$js) {
		$code .= $this->swfobject;
		self::$js = true;
		}
		$code .= ""
			. "<div class='amChart' id='chart_" . $this->id . "_flash'>" . "\n";
		$code .= $this->Javascript->codeBlock(
		"											  
		var flashvars = {};
		flashvars.path ='".$this->webroot.$this->chartpath."';
		flashvars.chart_id = '" . $this->id . "';
		flashvars.chart_settings = escape('".$this->getXmlSettings()."');
		flashvars.chart_data = escape('".$this->getXmlData()."');
		var params = {};
		swfobject.embedSWF('".$this->webroot.$this->chartpath.$this->swf."', 'chart_" . $this->id . "_flash','".$_width."','".$_height."','8','',flashvars, params, {});
		");		
		$code .= "</div>";
		return $code;
		
	}
	/**
	* Add Title
	*
	* @param	string				$_title
	* @public
	*/
	public function setTitle($_title) {
		$this->labels[0] = array(
		'text' => $_title,
		'x' => 0,
		'y' => 18,
		'config'=>array('align' => 'center')
		);
	}	


	/**
	 * The main function for converting to an XML document.
	 * Pass the configuration with:
	 *	 $data = array(
	 *			'background.alpha'=>100,
	 *			'background.border_alpha'=>20,
	 *			'legend.enabled'=>1,
	 *			'legend.align' => 'center',
	 *			'pie.y'=>'50%',
	 *			'pie.inner_radius'=>30,
	 *			'data_labels.show'=>'{title}: {value}',
	 *			'data_labels.max_width'=>140);	
	 *
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaults to data.
	 * @return SimpleXML Object
	 * @public
	 * 
	 */
	
  	public static function toXml($data, $rootNodeName = 'settings')
    {
      	
		$xml = new SimpleXMLElement("<$rootNodeName />");
		foreach($data as $key => $value){
			
			$keyPath = (array)explode(".", $key);
			$count = count($keyPath);
			$currentXml = $xml;
			for($i = 0; $i < $count - 1; $i++) {
				$nextNode = null;
				foreach($currentXml->children() as $child){
					if($child->getName() == $keyPath[$i]){
						$nextNode = $child;
						break;
					}
				}	
				if($nextNode === null){
						$nextNode = $currentXml->addChild($keyPath[$i]);
				}
				$currentXml = $nextNode;
			}
			if($value === true || $value === false){
					$value = (int)$value;
			}
			$currentXml->addChild($keyPath[count($keyPath) - 1], $value);	
		}
		
		return $xml;
		
    }
	
	/**
	*
	* Returns code for setting the chart.
	* @private
	* @return string XML
	*/
	private function getXmlSettings() {	
		$settings = self::toXml($this->config,'settings');
		
		if(count($this->graphs) > 0) {
			$graphs = $settings->addChild("graphs");
			foreach($this->graphs as $graph) {
				$graphNode = $graphs->addChild("graph");
				$graphNode->addAttribute('gid',$graph['id']);
				$graphNode->addChild("title", $graph['title']);
				if(isset($graph['config'])){
					foreach($graph['config'] as $key => $value) {
						$graphNode->addChild($key, $value);
					}
				}
			}

		}
		
		if(count($this->labels) > 0) {
			$labels = $settings->addChild("labels");
			foreach($this->labels as $label) {
				$labelNode = $labels->addChild("label");
				$labelNode->addChild("text", "<![CDATA[".htmlentities($label['text'])."]]>");
				$labelNode->addChild("x", $label['x']);
				$labelNode->addChild("y", $label['y']);
				if(isset($label['config'])){
					foreach($label['config'] as $key => $value) {
						$labelNode->addChild($key, $value);
					}
				}
			}
		}	
		$settings = $settings->asXml();
		$settings = str_replace('<?xml version="1.0"?>','',$settings);

		return trim(html_entity_decode($settings));
	}
	/**
	*
	* Returns code for data chart.
	* @private
	* @return string XML
	*/
	private function getXmlData() {	
		$name = $this->chart;
		$dataChart = new SimpleXMLElement("<{$name} />");
		
		if(count($this->series) > 0) {
			$series = $dataChart->addChild("series");
			foreach($this->series as $serie) {
				$serieNode = $series->addChild("value",$serie['title']);
				$serieNode->addAttribute('xid',$serie['id']);
				
			}
		}
		if(count($this->axis) > 0) {
			$axes = $dataChart->addChild("axes");
			foreach($this->axis as $axe) {
				$axisNode = $axes->addChild("axis",$axe['title']);
				$axisNode->addAttribute('xid',$axe['id']);
				
			}
		}
		if(count($this->slices) > 0) {
			foreach($this->slices as $slice) {
			$sliceNode =  $dataChart->addChild("slice", $slice['value']);
			$sliceNode->addAttribute('title',$slice['title']);
			}
		}
		if(count($this->graphs) > 0) {
			$graphs = $dataChart->addChild("graphs");
			foreach($this->graphs as $graph) {
				$graphNode = $graphs->addChild("graph");
				$graphNode->addAttribute('gid',$graph['id']);
				if(!isset($this->config['chart']) == 'xy'){
					foreach($graph['data'] as $key => $value) {
						$valueNode = $graphNode->addChild("value", $value);
						$valueNode->addAttribute('xid',$key);
					}
				}else{
					foreach($graph['data'] as $value) {
					$valueNode = $graphNode->addChild("point");
					$valueNode->addAttribute('x',$value['x']);
					$valueNode->addAttribute('y',$value['y']);
					$valueNode->addAttribute('value',$value['value']);
					}
				}

			}
		}
		$dataChart = $dataChart->asXml();
		$dataChart = str_replace('<?xml version="1.0"?>','',$dataChart);
		
		return trim($dataChart);
	}

	/**
	*
	* Set the id for the chart (optional)
	* @private
	* @param	string				$_id
	*/  	
	private function __setid($_id = null) {
		if($_id)
			$this->id = $_id;
		else
			$this->id = substr(md5(uniqid() . microtime()), 3, 5);
	}
}

?>