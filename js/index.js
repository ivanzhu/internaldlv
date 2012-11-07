/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
	/**
     * For menu a click
     */
	$('div#left ul#menu a').click(function(event){
		event.preventDefault();
		var $url = $(this).attr('href');
		
		$.ajax({
			url:$url,
			type: 'GET',
			dataType: 'html',
			success: function(data){
				$('div#content').html(data);
			}
			
		})
	})


})


