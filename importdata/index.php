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
        <title>Import Products</title>
        <script type="text/javascript" src="jquery.min.js"></script>
    </head>
    <body>
        <img src="loader.gif" id="loader" style="float: left;position: absolute;text-align: center;display:none;" />
        <table>
            <tr>
                <td>Import Product</td>
                <td><input type="button" name="importproducts" id="importproducts" value="Import Products"/></td>
            </tr>
        </table>
        <table class="productgrid" style="display: block;">
            <tr>
                <td>Product Id</td>
                <td>Status</td>
            </tr>
        </table>
    </body>
    <script type="text/javascript">

        jQuery('#importproducts').live('click',function(){
            jQuery('#loader').show();
            insertSimpleProduct(0); 
        });
        function insertSimpleProduct(id){
            if(id>=0){
                jQuery.ajax({
                    url: "ajax.php",
                    type: "post",
                    data: {id : id,ptype:'SIMPLE'},
                    success : function(data){
                        if(data!=''){
                            var impDData = data.split("@@");
                            txt = '<tr><td>'+impDData[0]+'</td><td>Imported</td></tr>';
                            jQuery('.productgrid').append(txt);
                            insertSimpleProduct(impDData[1]);
                        } else {
                            insertConfigProduct(0);
                            jQuery('#loader').hide();
                            return;
                        }
                    }
                });
            } else {
                return;
            }
        }
        function insertConfigProduct(id){
            if(id>=0){
                jQuery.ajax({
                    url: "ajax.php",
                    type: "post",
                    data: {id : id,ptype:'CONFIG'},
                    success : function(data){
                        if(data!=''){
                            var impDData = data.split("@@");
                            txt = '<tr><td>'+impDData[0]+'</td><td>Imported</td></tr>';
                            jQuery('.productgrid').append(txt);
                            insertConfigProduct(impDData[1]);
                        } else {
                            jQuery('#loader').hide();
                            return;
                        }
                    }
                });
            } else {
                jQuery('#loader').hide();
                return;
            }
        }
    </script>
    <script type="">jQuery('#loader').hide();</script>
</html>
