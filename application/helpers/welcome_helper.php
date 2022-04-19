<?php

function checkEmpty($index) {
	return (empty($index) || $index == null)? 0 : $index;
}
