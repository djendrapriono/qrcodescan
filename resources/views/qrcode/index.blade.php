@extends('layouts.app')
@push('styles')
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('AdminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Toastr -->
<link rel="stylesheet" href="{{ asset('AdminLTE/plugins/toastr/toastr.min.css') }}">
<style type="text/css">
    .well {
        height: 100%;
        width: 100%;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }

    .scanner-laser {
        position: absolute;
        margin: 40px;
        height: 30px;
        width: 30px;
        opacity: 0.5;
    }

    .laser-leftTop {
        top: 0;
        left: 0;
        border-top: solid red 3px;
        border-left: solid red 3px;
    }

    .laser-leftBottom {
        bottom: 0;
        left: 0;
        border-bottom: solid red 3px;
        border-left: solid red 3px;
    }

    .laser-rightTop {
        top: 0;
        right: 0;
        border-top: solid red 3px;
        border-right: solid red 3px;
    }

    .laser-rightBottom {
        bottom: 0;
        right: 0;
        border-bottom: solid red 3px;
        border-right: solid red 3px;
    }
</style>
@endpush
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Scan Qrcode
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success" role="alert" id="successMessage" style="display:none">
                        <button type="button" class="close" data-dismiss="alert"></button>
                        <strong>Succes</strong>
                        <p>Anda Berhasil Absen</p>
                    </div>
                    <div class="alert alert-info" role="alert" id="errorMessage" style="display:none">
                        <button type="button" class="close" data-dismiss="alert"></button>
                        <strong>Information</strong>
                        <p>Anda Sudah Absen</p>
                    </div>
                    <form id="qrcodeForm" name="qrcodeForm" method="POST" action="{{ route('peserta.store') }}">
                        <input type="hidden" autofocus="autofocus" type="hidden" class="qrcode" name="qrcode" id="qrcode" />
                    </form>
                    <audio preload="none" id="audio" src="{{ asset('beep.wav') }}" autoplay="false"></audio>
                    <div class="well" style="position: relative;display: inline-block;">
                        <video width="100%" height="100%" id="preview"></video>
                        <div class="scanner-laser laser-rightBottom" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-rightTop" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-leftBottom" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-leftTop" style="opacity: 0.5;"></div>
                    </div>
                    <div class="text-left">
                        <span class="badge bg-warning ">Zoom</span>
                        <input class="form-control" type="range" id="qrCodeZoomSlider" min="0" max="0" />
                    </div>
                    <div class="row justify-content-center">
                        <div class="text-center">
                            <a class="btn btn-app bg-success " onclick="frontCamera()" value="0">
                                <span class="badge bg-warning ">Front</span>
                                <i class="fas fa-qrcode"></i>Camera
                            </a>

                            <a class="btn btn-app bg-info" onclick="backCamera()" value="1">
                                <span class="badge bg-danger ">Back</span>
                                <i class="fas fa-qrcode"></i>Camera
                            </a>
                        </div>
                    </div>
                    <br>

                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- ./row -->
</div><!-- /.container-fluid -->

@stop

@push('scripts')
<!-- SweetAlert2 -->
<script src="{{ asset('AdminLTE/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('AdminLTE/plugins/toastr/toastr.min.js') }}"></script>

<!-- Qrcode -->
<script src="{{ asset('instascan.min.js') }}"></script>

<script type="text/javascript">
    // insert data to data base
    var sound = document.getElementById('audio');
    sound.pause();

    function insertData() {
        var qrcode = $('#qrcode').val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var sound = document.getElementById('audio');
        if (qrcode != "") {
            //   $("#butsave").attr("disabled", "disabled");
            $.ajax({
                url: "{{ route('peserta.store') }}",
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    type: JSON,
                    qrcode: qrcode,
                },
                cache: false,
                success: function(dataResult) {
                    sound.play();
                    console.log(dataResult);
                    toastr.success('Absen Berhasil');
                    $('#errorMessage').show();
                },
                error: function(dataResult) {
                    sound.play();
                    console.log(dataResult);
                    toastr.info('Anda Sudah Absen');
                    $('#errorMessage').show();
                }
            });
        } else {
            alert('Please fill all the field !');
        }
        //toastr.success('Success');
    }
    let scanner = new Instascan.Scanner({
        video: document.getElementById('preview'),
        backgroundScan: true,
        continuous: true,
        mirror: false
    });
    //let scanner = new Instascan.Scanner({ video: document.getElementById('my_camera_qr_video'),backgroundScan:true, continuous: true, mirror:false);
    scanner.addListener('scan', function(content) {
        document.getElementById('qrcode').value = content;
        insertData();
    });

    function frontCamera(selectObject) {
        //var value = selectObject.value;
        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
                var camera_index = 0;
                // Reference to input slider
                var zoomSlider = document.getElementById('qrCodeZoomSlider');
                // Hide the slider until it is ready
                //zoomSlider.style.display = 'none';
                // Timeout needed in Chrome, see https://crbug.com/711524
                // 2-seconds should be way enough, 500-milliseconds might be too
                setTimeout(() => {
                    // Get the vieo track
                    var videoTrack = cameras[camera_index]._stream.getVideoTracks()[0];
                    // No video track found, cancel zoom support
                    if (videoTrack == null) {
                        return;
                    }
                    // Get capabilities from the camera
                    const capabilities = videoTrack.getCapabilities();
                    // No zoom support, cancel
                    if (!capabilities.zoom) {
                        return;
                    }
                    // Set slider properties based on camera capabilities
                    zoomSlider.min = capabilities.zoom.min;
                    zoomSlider.max = capabilities.zoom.max;
                    zoomSlider.step = capabilities.zoom.step;
                    zoomSlider.value = videoTrack.getSettings().zoom;
                    // On slider change, update camera zoom
                    zoomSlider.oninput = function() {
                        videoTrack.applyConstraints({
                            advanced: [{
                                zoom: zoomSlider.value
                            }]
                        });
                    };
                    // Ready to show slider
                    zoomSlider.style.display = '';
                }, 2000);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });
    }

    function backCamera(selectObject) {
        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[1]);
                var camera_index = 1;
                // Reference to input slider
                var zoomSlider = document.getElementById('qrCodeZoomSlider');
                // Hide the slider until it is ready
                //zoomSlider.style.display = 'none';
                // Timeout needed in Chrome, see https://crbug.com/711524
                // 2-seconds should be way enough, 500-milliseconds might be too
                setTimeout(() => {
                    // Get the vieo track
                    var videoTrack = cameras[camera_index]._stream.getVideoTracks()[0];
                    // No video track found, cancel zoom support
                    if (videoTrack == null) {
                        return;
                    }
                    // Get capabilities from the camera
                    const capabilities = videoTrack.getCapabilities();
                    // No zoom support, cancel
                    if (!capabilities.zoom) {
                        return;
                    }
                    // Set slider properties based on camera capabilities
                    zoomSlider.min = capabilities.zoom.min;
                    zoomSlider.max = capabilities.zoom.max;
                    zoomSlider.step = capabilities.zoom.step;
                    zoomSlider.value = videoTrack.getSettings().zoom;
                    // On slider change, update camera zoom
                    zoomSlider.oninput = function() {
                        videoTrack.applyConstraints({
                            advanced: [{
                                zoom: zoomSlider.value
                            }]
                        });
                    };
                    // Ready to show slider
                    zoomSlider.style.display = '';
                }, 2000);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });
    }
</script>
@endpush