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
                        Toastr Examples
                    </h3>
                </div>
                <div class="card-body">

                    <form id="qrcodeForm" name="qrcodeForm" method="POST" action="{{ route('peserta.store') }}">
                        <input autofocus="autofocus" type="hidden" class="qrcode" name="qrcode" id="qrcode" />
                    </form>
                    <select class="form-control" id="Camera" onchange="getCamera(this)">
                        <option value="">Pilih Camera</option>
                        <option value="0">Camera Depan</option>
                        <option value="1">Camera Belakang</option>
                    </select>
                    <br>
                    <div class="well" style="position: relative;display: inline-block;">
                        <video width="100%" height="100%" id="preview"></video>
                        <div class="scanner-laser laser-rightBottom" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-rightTop" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-leftBottom" style="opacity: 0.5;"></div>
                        <div class="scanner-laser laser-leftTop" style="opacity: 0.5;"></div>
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
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('AdminLTE/dist/js/demo.js') }}"></script>

<!-- Qrcode -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script type="text/javascript">
    function insertData() {
        var qrcode = $('#qrcode').val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        if (qrcode != "") {
            //   $("#butsave").attr("disabled", "disabled");
            $.ajax({
                url: "{{ route('peserta.store') }}",
                type: "POST",
                data: {
                    _token: CSRF_TOKEN,
                    type: 1,
                    qrcode: qrcode,
                },
                cache: false,
                success: function(dataResult) {
                    console.log(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    if (dataResult.statusCode == 200) {
                        toastr.success('Absen Berhasil');
                    } 
                },
                error: function(dataResult) {
                        toastr.info('Anda Sudah Absen');
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

    function getCamera(selectObject) {
        var value = selectObject.value;
        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[value]);
            } else {
                console.error('No cameras found.');
            }
        }).catch(function(e) {
            console.error(e);
        });
    }
</script>
@endpush