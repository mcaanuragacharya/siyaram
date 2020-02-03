<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Lang" content="en">
        <meta name="author" content="">
        <meta http-equiv="Reply-to" content="@.com">
        <meta name="generator" content="PhpED 5.8">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="creation-date" content="01/01/2009">
        <meta name="revisit-after" content="15 days">
        <title>Import Category</title>
        <script type="text/javascript" src="jquery.min.js"></script>
    </head>
    <body>
        <img src="loader.gif" id="loader" style="float: left;position: absolute;text-align: center;display:none;" />
        <table>
            <tr>
                <td>Import Categories</td>
                <td><input type="button" name="importcategory" id="importcategory" value="Import Category"/></td>
            </tr>
        </table>  
        <table class="categorygrid" style="display: block;">
            <tr>
                <td>Category Name</td>
                <td>Status</td>
            </tr>
        </table>  
    </body>
    <script type="text/javascript">

        jQuery('#importcategory').live('click',function(){
            jQuery('#loader').show();
            insertCategory(0); 
        });
        function insertCategory(id){  
            if(id>=0){
                jQuery.ajax({
                    url: "categoryajax.php",
                    type: "post",
                    data: {id : id},
                    success : function(data){
                        if(data!=''){
                            var impDData = data.split("@@");
                            txt = '<tr><td>'+impDData[0]+'</td><td>Imported</td></tr>';
                            jQuery('.categorygrid').append(txt);
                            insertCategory(impDData[1]);    
                        } else {
                            jQuery('#loader').hide();
                        }
                    }
                });
            } else {
                return; 
            }
        }
    </script>
    <script type="">jQuery('#loader').hide();</script>
</html>
