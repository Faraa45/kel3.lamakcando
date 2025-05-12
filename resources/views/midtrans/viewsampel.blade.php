<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Midtrans Payment</title>

    <!-- MIDTRANS SANDBOX CLIENT -->
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>

    <!-- Debug Snap Token -->
    <script>
      console.log("Snap Token: {{ $snap_token }}");
    </script>
  </head>

  <body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        window.snap.pay('{{ $snap_token }}', {
          onSuccess: function(result){
            console.log("Success:", result);
            alert("Pembayaran berhasil!");
          },
          onPending: function(result){
            console.log("Pending:", result);
            alert("Menunggu pembayaran...");
          },
          onError: function(result){
            console.error("Error:", result);
            alert("Terjadi kesalahan saat pembayaran.");
          },
          onClose: function(){
            console.warn("Popup ditutup tanpa menyelesaikan pembayaran");
            alert("Kamu menutup popup pembayaran.");
          }
        });
      });
    </script>
  </body>
</html>
