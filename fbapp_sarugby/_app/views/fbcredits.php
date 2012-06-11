<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="https://www.facebook.com/2008/fbml">
  <head>
    <title>Facebook Credits Demo</title>
  </head>
  <body>
    <h2>Credits</h2>
    <button onclick="buy(350)">Buy 350</button>
	<button onclick="buy(700)">Buy 700</button>
	<button onclick="buy(1400)">Buy 1400</button>
    <div id="fb-ui-return-data"></div>
    <div id="fb-root"></div>

    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script> 
      FB.init({appId: "342203842518329", status: true, cookie: true});

      // The dialog only opens if you've implemented the
      // Credits Callback payments_get_items.
      function buy($credits) {
        var obj = {
          method: 'pay',
          action: 'buy_item',
          // You can pass any string, but your payments_get_items must
          // be able to process and respond to this data.
          order_info: {'item_id': $credits},
          dev_purchase_params: {'oscif': true}
        };

        FB.ui(obj, js_callback);
      }

      // This JavaScript callback handles FB.ui's return data and differs
      // from the Credits Callbacks.
      var js_callback = function(data) {
        if (data['order_id']) {
          // Facebook only returns an order_id if you've implemented
          // the Credits Callback payments_status_update and settled
          // the user's placed order.

          // Notify the user that the purchased item has been delivered
          // without a complete reload of the game.
          write_callback_data(
                    "<br><b>Transaction Completed!</b> </br></br>"
                    + "Data returned from Facebook: </br>"
                    + "Order ID: " + data['order_id'] + "</br>"
                    + "Status: " + data['status']);
        } else if (data['error_code']) {
          // Appropriately alert the user.
          write_callback_data(
                    "<br><b>Transaction Failed!</b> </br></br>"
                    + "Error message returned from Facebook:</br>"
                    + data['error_code'] + " - "
                    + data['error_message']);
        } else {
          // Appropriately alert the user.
          write_callback_data("<br><b>Transaction failed!</b>");
        }
      };

      function write_callback_data(str) {
        document.getElementById('fb-ui-return-data').innerHTML=str;
      }
    </script>
  </body>
</html>