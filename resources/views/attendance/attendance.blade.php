@extends('layouts.admin')
@section('page-title')
    {{ __('Attendance With Face Authentication') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';
@endphp


@section('action-button')
@endsection
@section('content')

<h1 class="text-center mb-4">Face Authentication</h1>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/human/dist/human.js"></script>

<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .video-container {
        position: relative;
        margin: 20px 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    #video, #overlay {
        width: 100%;
        max-width: 600px;
        border-radius: 12px;
    }
    #overlay {
        position: absolute;
        top: 0;
        left: 0;
    }
    #status {
        margin: 20px 0;
        padding: 15px;
        border-radius: 8px;
        font-size: 16px;
        text-align: center;
    }
    #visitorDetails {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 12px;
        background-color: #f9f9f9;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
    }
    .col-6 {
        flex: 1;
        min-width: 400px;
        max-width: 600px;
        background-color: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .visitor-photo {
        max-width: 200px;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin: 15px 0;
    }
    .confidence {
        font-size: 24px;
        font-weight: bold;
        color: #4CAF50;
        margin-top: 20px;
    }
    .status-processing {
        background-color: #fcf8e3;
        color: #8a6d3b;
    }
    .status-success {
        background-color: #dff0d8;
        color: #3c763d;
    }
    .status-error {
        background-color: #f2dede;
        color: #a94442;
    }
    .countdown-style {
        color: #666;
        font-size: 0.9em;
        margin-top: 10px;
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-12">
        <div id="status" class="status-processing">Initializing camera...</div>
        <div id="countdown-timer" class="countdown-style"></div>
    </div>
    <div class="col-6">
        <div class="video-container">
            <video id="video" autoplay playsinline></video>
            <canvas id="overlay"></canvas>
        </div>
    </div>
    <div class="col-6">
        <div id="visitorDetails" style="display: none;">
            <h3>Visitor Details</h3>
            <p><strong>Name: </strong> <span id="visitorName">-</span></p>
            <p><strong>Care Of: </strong> <span id="visitorFatherName">-</span></p>
            <p><strong>Aadhar Number: </strong> <span id="visitorAadhar">-</span></p>
            <p><strong>Date of Birth: </strong> <span id="visitorDOB">-</span></p>
            <p><strong>Full Address: </strong> <span id="visitorAddress">-</span></p>
            <p><strong>Pincode: </strong> <span id="visitorPincode">-</span></p>
            <div class="d-flex">
                <div class="col-md-6"><strong>Aadhaar Image</strong><img id="visitorPhoto" class="visitor-photo" style="margin-right: 30px"></div>
                <div class="col-md-6"><strong>Captured Image</strong><img id="caputuredVisitorPhoto" class="visitor-photo"></div>
            </div>
            <div class="confidence">Matched Percent: <span id="confidenceValue">-</span>%</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const statusDiv = document.getElementById('status');
        const visitorDetails = document.getElementById('visitorDetails');
        const countdownElement = document.getElementById('countdown-timer');
        const context = overlay.getContext('2d');
        let isProcessing = false;
        let faceStableCount = 0;
        let countdownInterval = null;
   
        const human = new Human.Human({
            modelBasePath: 'https://cdn.jsdelivr.net/npm/@vladmandic/human/models/',
            backend: 'webgl',
            async: true,
            warmup: true,
            face: {
                enabled: true,
                detector: { maxDetected: 1, minConfidence: 0.7 },
                mesh: false,
                emotion: false,
                description: false,
            }
        });
   
        const updateStatus = (message, type = 'processing') => {
            statusDiv.textContent = message;
            statusDiv.className = `status-${type}`;
        };
   
        const startCountdown = (seconds, messagePrefix) => {
            stopCountdown();
            countdownElement.textContent = `${messagePrefix} : ${seconds}s`;
            countdownInterval = setInterval(() => {
                seconds--;
                if (seconds <= 0) return stopCountdown();
                countdownElement.textContent = `${messagePrefix} : ${seconds}s`;
            }, 1000);
        };
   
        const stopCountdown = () => {
            if (countdownInterval) clearInterval(countdownInterval);
            countdownElement.textContent = '';
        };
   
        const initializeCamera = async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 640, height: 480, facingMode: "user" }
                });
                video.srcObject = stream;
                await new Promise(resolve => video.onloadedmetadata = resolve);
                overlay.width = video.videoWidth;
                overlay.height = video.videoHeight;
                updateStatus('Ready for face detection');
                detectFrame();
            } catch (err) {
                console.error('Camera access error:', err);
                updateStatus('Camera access denied.', 'error');
            }
        };
   
        const detectFrame = async () => {
            const result = await human.detect(video);
            context.clearRect(0, 0, overlay.width, overlay.height);
   
            const face = result.face?.[0];
            if (face?.score >= 0.7) {
                drawFaceBox(face);
                if (!isProcessing && ++faceStableCount >= 60) {
                    faceStableCount = 0;
                    await captureAndAuthenticate(face);
                } else {
                    updateStatus(`Face detected - Capturing in ${2 - Math.floor(faceStableCount / 30)}s`, 'processing');
                }
            } else {
                faceStableCount = 0;
                if (!isProcessing) updateStatus('Position your face in the frame', 'error');
            }
   
            requestAnimationFrame(detectFrame);
        };
   
        const drawFaceBox = (face) => {
            context.strokeStyle = "#00ff00";
            context.lineWidth = 3;
            context.strokeRect(...face.box);
        };
   
        const captureAndAuthenticate = async (face) => {
            isProcessing = true;
            updateStatus('Processing...', 'processing');
            try {
                const faceCanvas = document.createElement('canvas');
                const [x, y, w, h] = face.box;
                faceCanvas.width = w;
                faceCanvas.height = h;
                faceCanvas.getContext('2d').drawImage(video, x, y, w, h, 0, 0, w, h);
   
                const response = await fetch('/attendanceemployee/aadhaar/face-authenticate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ image: faceCanvas.toDataURL('image/jpeg') })
                });
   
                const data = await response.json();
                visitorDetails.style.display = 'none';
   
                if (data.status) {
                    updateStatus(`(${data.data.original.time}) ${data.data.original.message}`, 'success');
                    startCountdown(30, 'Scan Available');
                    await sleep(30000);
                } else {
                    if (data.flag === 1) {
                        alert(data.message);
                        updateStatus('Please wait...', 'processing');
                        startCountdown(10, 'Scan Available');
                        await sleep(10000);
                    } else {
                        updateStatus(data.message || "Face does not match.", 'error');
                    }
                }
            } catch (err) {
                console.error('Authentication error:', err);
                updateStatus("Face does not match.", 'error');
            } finally {
                stopCountdown();
                isProcessing = false;
            }
        };
   
        const sleep = (ms) => new Promise(res => setTimeout(res, ms));
   
        try {
            await human.load();
            await initializeCamera();
        } catch (err) {
            console.error('System init failed:', err);
            updateStatus('System initialization failed', 'error');
        }
    });
</script>

// <script>
// document.addEventListener('DOMContentLoaded', async () => {
//     const video = document.getElementById('video');
//     const overlay = document.getElementById('overlay');
//     const statusDiv = document.getElementById('status');
//     const visitorDetails = document.getElementById('visitorDetails');
//     let faceDetectionTimeout;
//     let isProcessing = false;

//     const human = new Human.Human({
//         modelBasePath: 'https://cdn.jsdelivr.net/npm/@vladmandic/human/models/',
//         backend: 'webgl',
//         async: true,
//         warmup: true,
//         face: {
//             enabled: true,
//             detector: { maxDetected: 1, minConfidence: 0.7 },
//             mesh: false,
//             emotion: false,
//             description: false,
//         }
//     });

//     // Update status messages with styling
//     function updateStatus(message, type = 'processing') {
//         statusDiv.textContent = message;
//         statusDiv.className = `status-${type}`;
//     }

//     async function initializeCamera() {
//         try {
//             const stream = await navigator.mediaDevices.getUserMedia({
//                 video: { width: 640, height: 480, facingMode: "user" }
//             });
//             video.srcObject = stream;

//             await new Promise((resolve) => {
//                 video.onloadedmetadata = () => {
//                     overlay.width = video.videoWidth;
//                     overlay.height = video.videoHeight;
//                     resolve();
//                 };
//             });

//             updateStatus('Ready for face detection');
//             startFaceDetection();
//         } catch (error) {
//             console.error('Camera error:', error);
//             updateStatus('Camera access denied. Please enable permissions.', 'error');
//         }
//     }

//     async function startFaceDetection() {
//         const context = overlay.getContext('2d');
//         let faceStableCount = 0;

//         async function detectFrame() {
//             try {
//                 const result = await human.detect(video);
//                 context.clearRect(0, 0, overlay.width, overlay.height);

//                 if (result.face?.[0]?.score >= 0.7) {
//                     const face = result.face[0];
//                     drawFaceBox(face, context);

//                     if (!isProcessing) {
//                         faceStableCount++;
//                         updateStatus(`Face detected - Capturing & Authenticating in ${2 - Math.floor(faceStableCount/30)}s`, 'processing');

//                         if (faceStableCount >= 60) { // 60 frames ≈ 2 seconds at 30fps
//                             captureAndAuthenticate(face);
//                             faceStableCount = 0;
//                         }
//                     }
//                 } else {
//                     faceStableCount = 0;
//                     if (!isProcessing) updateStatus('Position your face in the frame', 'error');
//                 }
//             } catch (error) {
//                 console.error('Detection error:', error);
//                 updateStatus('Detection error occurred', 'error');
//             }
//             requestAnimationFrame(detectFrame);
//         }

//         detectFrame();
//     }

//     function drawFaceBox(face, context) {
//         context.strokeStyle = "#00ff00";
//         context.lineWidth = 3;
//         context.strokeRect(...face.box);
//     }

//     async function captureAndAuthenticate(face) {
//         isProcessing = true;
//         updateStatus('Processing...', 'processing');

//         try {
//             const faceCanvas = document.createElement('canvas');
//             faceCanvas.width = face.box[2];
//             faceCanvas.height = face.box[3];

//             faceCanvas.getContext('2d').drawImage(
//                 video,
//                 face.box[0], face.box[1], face.box[2], face.box[3],
//                 0, 0, face.box[2], face.box[3]
//             );

//             const response = await fetch('/attendanceemployee/aadhaar/face-authenticate', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//                 },
//                 body: JSON.stringify({ image: faceCanvas.toDataURL('image/jpeg') })
//             });

//             const data = await response.json();
//             if (data.status) {
                

//                 // updateStatus('Authentication successful!', 'success');
//                 updateStatus("("+data.data.original.time+") "+data.data.original.message, 'success');
//                 // showVisitorDetails(data.data.original.message);

//                 // Add countdown timer element updates
//                 let seconds = 30;
//                 const countdownElement = document.getElementById('countdown-timer'); // Add this element in your HTML

//                 // Update counter every second
//                 const countdownInterval = setInterval(() => {
//                     countdownElement.textContent = `Scan Available : ${seconds}s`;
//                     seconds--;
//                 }, 1000);

//                 // Wait 30 seconds
//                 await sleep(30000);

//                 // Cleanup after delay
//                 clearInterval(countdownInterval);
//                 countdownElement.textContent = '';
//                 visitorDetails.style.display = 'none';
//             } else {
//                 if(data.flag==1){
//                     alert(data.message);
//                     let seconds = 10;
//                     const countdownElement = document.getElementById('countdown-timer'); // Add this element in your HTML

//                     // Update counter every second
//                     const countdownInterval = setInterval(() => {
//                         countdownElement.textContent = `Scan Available : ${seconds}s`;
//                         seconds--;
//                     }, 1000);
//                     updateStatus('Please Wait...');
//                     await sleep(10000);
//                     // Cleanup after delay
//                     clearInterval(countdownInterval);
//                     countdownElement.textContent = '';
//                 }
//                 else{
//                     updateStatus(data.message || "Face does not match.", 'error');
//                 }
//                 visitorDetails.style.display = 'none';
//             }
//         } catch (error) {
//             console.error('Error:', error);
//             updateStatus(data.message || "Face does not match.", 'error');
//         }

//         isProcessing = false;
//     }

//     function sleep(ms) {
//         return new Promise(resolve => setTimeout(resolve, ms));
//     }

//     function showVisitorDetails(data) {
//         alert(data);
//     }

//     function formatDate(dateString) {
//         const date = new Date(dateString);
//         return date.toLocaleDateString('en-IN', {
//             day: '2-digit', month: '2-digit', year: 'numeric'
//         });
//     }

//     // Initialize system
//     try {
//         await human.load();
//         await initializeCamera();
//     } catch (error) {
//         console.error('Initialization error:', error);
//         updateStatus('System initialization failed', 'error');
//     }
// });
// </script>



@endsection
@push('script-page')

@endpush

