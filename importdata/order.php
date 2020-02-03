<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
    <head>
        <title>Order Export</title>
        <script type="text/javascript" src="jquery.min.js"></script>
    </head>
    <body>
        <img src="loader.gif" id="loader" style="float: left;position: absolute;text-align: center;display:none;" />
        <table>
            <tr>
                <td>Order Export</td>
                <td><input type="button" name="orderexport" id="orderexport" value="Order Products"/></td>
            </tr>
        </table>  
    </body>
    <script type="text/javascript">
        jQuery('#orderexport').live('click',function(){
            jQuery('#loader').show();
            orderExport(); 
        });
        function orderExport(){  
            jQuery.ajax({
                url: "ajaxorder.php",
                type: "post",
                success : function(data){
                    jQuery('#loader').hide();
                    alert("Order Exported Successfully");
                    return; 
                }
            });
        }
    </script>
    <script type="">jQuery('#loader').hide();</script>
</html>
