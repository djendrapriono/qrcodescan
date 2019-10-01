<!DOCTYPE html>
<html>
<head>
	<title>JQuery HTML5 QR Code Scanner using Instascan JS Example - ItSolutionStuff.com</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>
<body>
  
    <h1>JQuery HTML5 QR Code Scanner using Instascan JS Example - ItSolutionStuff.com</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="qrcodeForm" name="qrcodeForm" class="qrcodeForm" method="POST">
        @csrf
        <input autofocus="autofocus" type="text" class="qrcode" name="qrcode" id="qrcode" />
        <button class="tombol-simpan" >Simpan</button> 
    </form>
    <video id="preview"></video>
    <script type="text/javascript">
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
      scanner.addListener('scan', function (content) {
        document.getElementById('qrcode').value = content;
        $("#qrcodeForm").submit();
      });
      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
            var selectedCam = cameras[0];
            $.each(cameras, (i, c) => {
                if (c.name.indexOf('back') != -1) {
                    selectedCam = c;
                    return false;
                }
            });
            scanner.start(selectedCam);
        } else {
            console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });
    </script>


</body>
</html>