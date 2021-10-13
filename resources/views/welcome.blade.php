<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Ensures optimal rendering on mobile devices -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> <!-- Optimal Internet Explorer compatibility -->
</head>

<body>
<!-- Include the PayPal JavaScript SDK; replace "test" with your own sandbox Business account app client ID -->
<script src="https://www.paypal.com/sdk/js?client-id={{env("PAYPAL_CLIENT_ID")}}&currency=USD"></script>

<!-- Set up a container element for the button -->
<div id="paypal-button-container"></div>

<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function() {
            return fetch('{{route("paypal.create")}}', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                }
            }).then(function(res) {
                console.log("birzatlara bolyarey");
                console.log(res);
                return res.json();
            }).then(function(data) {
                return data.id; // Use the key sent by your server's response, ex. 'id' or 'token'
            });
        },

        // Finalize the transaction after payer approval
        onApprove: function(data) {
            return fetch('{{route("paypal.execute")}}', {
                method: 'post',
                headers: {
                    'content-type': 'application/json',
                    'accept': 'application/json'
                },
                body: JSON.stringify({
                    orderID: data.orderID
                })
            }).then(function(res) {
                return res.json();
            }).then(function(details) {
                console.log("Boldy oydyan dostum");
                console.log(details.data);
            })
        }
    }).render('#paypal-button-container');

</script>
</body>
</html>
