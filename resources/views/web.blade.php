@extends('layouts.app')

@section('content')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            var but = document.getElementById("but");
            var video = document.getElementById("vid");
            var mediaDevices = navigator.mediaDevices;
            vid.muted = true;
            but.addEventListener("click", () => {

                // Accessing the user camera and video.
                mediaDevices
                    .getUserMedia({
                        video: true,
                    })
                    .then((stream) => {

                        // Changing the source of video to current stream.
                        video.srcObject = stream;
                        video.addEventListener("loadedmetadata", () => {
                            video.play();
                        });
                    })
                    .catch(alert);
            });

            function stop(e) {
                var stream = video.srcObject;
                var tracks = stream.getTracks();

                for (var i = 0; i < tracks.length; i++) {
                    var track = tracks[i];
                    track.stop();
                }

                video.srcObject = null;
            }
        });
    </script>
    <style>
        #container {
            margin: 0px auto;
            width: 500px;
            height: 375px;
            border: 1px #333 solid;
            background-color: #a2a2a2;
        }
        video {
            width: 500px;
            height: 375px;
        }
    </style>
    <!-- Page Header -->
    <div class="page-header row no-gutters py-4">
        <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
            <h3 class="page-title">Відео</h3>
        </div>
    </div>
    <!-- End Page Header -->
    <!-- Default Light Table -->
    <div class="row">
        <div class="col">
            <div id="container">
                <div>
                    <video id="vid"></video>
                </div>
                <br />
                <button class="btn btn-success" id="but" autoplay>
                    Play
                </button>
            </div>
        </div>
    </div>

    <!-- End Default Light Table -->
@endsection
