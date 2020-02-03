require(['jquery','mage/url'],function($,url){
	$('#leasebutton').on('click', function(){
		    $.ajax({
                    	 showLoader: true,
                    	 url: '/Magento230/yeslease/index',
                    	 type: "GET",
                    	 dataType: 'json'
        	 }).done(function (data) {
				 alert(data);
                    	
        	 });
      });

});
