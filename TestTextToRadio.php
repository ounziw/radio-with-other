<?php 

class TestTextToRadio extends PHPUnit_Framework_TestCase {

	function testChange() {

		$oldvalue = array(
				'radio'=> 'red',
				'text'=> 'red',
		);
		$datain = array(
				'radio'=> 'red',
				'text'=> 'green',
		);
		$dataout = array(
				'radio'=> 'green',
				'text'=> 'green',
		);
		$result = RadioWithOther::text_to_radio($datain,$oldvalue);

		$expected = $dataout;
		$this->assertEquals($expected, $result);

	}
	function testnoChange1() {

		$oldvalue = array(
				'radio'=> 'green',
				'text'=> 'green',
		);
		$datain = array(
				'radio'=> 'red',
				'text'=> '',
		);
		$dataout = array(
				'radio'=> 'red',
				'text'=> '',
		);
		$result = RadioWithOther::text_to_radio($datain,$oldvalue);

		$expected = $dataout;
		$this->assertEquals($expected, $result);

	}
	function testnoChange2() {

		$oldvalue = array(
				'radio'=> 'green',
				'text'=> 'green',
		);
		$datain = array(
				'radio'=> 'red',
				'text'=> 'green',
		);
		$dataout = array(
				'radio'=> 'red',
				'text'=> 'green',
		);
		$result = RadioWithOther::text_to_radio($datain,$oldvalue);

		$expected = $dataout;
		$this->assertEquals($expected, $result);

	}
}
