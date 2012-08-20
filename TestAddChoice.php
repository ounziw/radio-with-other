<?php 

class TestAddChoice extends PHPUnit_Framework_TestCase {


function test_add_choice(){	
	$in = array(
							'var1'=>'var1',
					);
	$expected = array(
							'var1'=>'var1',
							'var2'=>'var2',
					);
	$result = RadioWithOther::add_choice($in,'var2');
        $this->assertEquals($expected, $result);
}
	function test_add_choice_repeat() {

		$dataobject = <<<EOF
a:12:{s:3:"key";s:19:"field_50270cab4a2c9";s:5:"label";s:3:"abb";s:4:"name";s:3:"abb";s:4:"type";s:8:"repeater";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:10:"sub_fields";a:2:{i:0;a:8:{s:3:"key";s:19:"field_50270cab4adf8";s:5:"label";s:1:"c";s:4:"name";s:1:"c";s:4:"type";s:14:"radiowithother";s:7:"choices";a:2:{s:3:"red";s:3:"red";s:4:"blue";s:4:"blue";}s:13:"default_value";s:0:"";s:6:"layout";s:8:"vertical";s:8:"order_no";s:1:"0";}i:1;a:7:{s:3:"key";s:19:"field_50270d279c54e";s:5:"label";s:1:"k";s:4:"name";s:1:"k";s:4:"type";s:4:"text";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:8:"order_no";s:1:"1";}}s:7:"row_min";s:1:"0";s:9:"row_limit";s:0:"";s:6:"layout";s:5:"table";s:12:"button_label";s:7:"Add Row";s:8:"order_no";s:1:"0";}
EOF;
		$dataout = <<<EOF
a:12:{s:3:"key";s:19:"field_50270cab4a2c9";s:5:"label";s:3:"abb";s:4:"name";s:3:"abb";s:4:"type";s:8:"repeater";s:12:"instructions";s:0:"";s:8:"required";s:1:"0";s:10:"sub_fields";a:2:{i:0;a:8:{s:3:"key";s:19:"field_50270cab4adf8";s:5:"label";s:1:"c";s:4:"name";s:1:"c";s:4:"type";s:14:"radiowithother";s:7:"choices";a:3:{s:3:"red";s:3:"red";s:4:"blue";s:4:"blue";s:5:"green";s:5:"green";}s:13:"default_value";s:0:"";s:6:"layout";s:8:"vertical";s:8:"order_no";s:1:"0";}i:1;a:7:{s:3:"key";s:19:"field_50270d279c54e";s:5:"label";s:1:"k";s:4:"name";s:1:"k";s:4:"type";s:4:"text";s:13:"default_value";s:0:"";s:10:"formatting";s:4:"html";s:8:"order_no";s:1:"1";}}s:7:"row_min";s:1:"0";s:9:"row_limit";s:0:"";s:6:"layout";s:5:"table";s:12:"button_label";s:7:"Add Row";s:8:"order_no";s:1:"0";}
EOF;

		$data = unserialize($dataobject);
		$key = 'field_50270cab4adf8';

		$result = RadioWithOther::add_choice_in_repeat($data,'green',$key);

		$expected = unserialize($dataout);
		$this->assertEquals($expected, $result);

	}
}
