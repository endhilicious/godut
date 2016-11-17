// JavaScript Document

$(function(){
	$('#play').click(function(e) {
        $('.items').cycle('resume');
		return false
    });
	$('#play').click(function(e) {
        $('.items').cycle('pause');
		return false
    });
});

$(document).ready(function(e) {
	
});