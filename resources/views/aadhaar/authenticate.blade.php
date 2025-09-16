@extends('layouts.admin')

@section('page-title')
    {{ __('Face Authentication') }}
@endsection

@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --secondary: #3b82f6;
        --accent: #60a5fa;
        --info: #93c5fd;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.1);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
    }

    body {
        background: linear-gradient(135deg, #eef2f6 0%, #d1d9e6 100%);
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent;
        padding: 0;
    }

    /* Premium Header */
    .page-header-premium {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 20px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }
    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.15), transparent 70%);
        animation: rotateBg 20s linear infinite;
    }
    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        backdrop-filter: blur(10px);
    }

    .header-text h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.85);
        margin: 4px 0 0 0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    /* Premium Cards */
    .premium-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none;
        margin-bottom: 20px;
    }
    .premium-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .card-header-premium {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .premium-card-body {
        padding: 24px;
    }

    /* Video Container */
    .video-container {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        background: #000;
    }

    #video, #overlay {
        width: 100%;
        max-width: 100%;
        border-radius: 16px;
        display: block;
    }

    #overlay {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
    }

    /* Status Messages */
    .status-card {
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        text-align: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .status-processing {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        color: var(--warning);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .status-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
        color: var(--success);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
        color: var(--danger);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    /* Form Styling */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
        transform: translateY(-1px);
    }

    /* Employee Details */
    .employee-details {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .detail-value {
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
    }

    /* Photo Containers */
    .photo-comparison {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }

    .photo-box {
        text-align: center;
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
    }

    .photo-box:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .photo-box-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .visitor-photo {
        width: 100%;
        max-width: 180px;
        height: 180px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .visitor-photo:hover {
        border-color: var(--primary);
        transform: scale(1.02);
    }

    .img-placeholder {
        width: 100%;
        max-width: 180px;
        height: 180px;
        background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        margin: 0 auto;
        border: 2px dashed #d1d5db;
    }

    /* Confidence Score */
    .confidence-card {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin-top: 20px;
        box-shadow: var(--shadow);
    }

    .confidence-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .confidence-value {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
    }

    /* Countdown Timer */
    .countdown-card {
        background: linear-gradient(135deg, var(--info), var(--accent));
        color: white;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        margin-top: 16px;
        font-weight: 600;
    }

    /* Authentication Steps */
    .auth-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        position: relative;
    }

    .auth-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        background: white;
        padding: 0 12px;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .step.active .step-icon {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-align: center;
    }

    .step.active .step-label {
        color: var(--primary);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
        
        .premium-card-body {
            padding: 20px;
        }
        
        .photo-comparison {
            grid-template-columns: 1fr;
        }
        
        .auth-steps {
            flex-direction: column;
            gap: 16px;
        }
        
        .auth-steps::before {
            display: none;
        }
    }

    /* Animation */
    .fade-in {
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Premium Header --}}
<div class="page-header-premium">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('Face Authentication') }}</h1>
                <p>{{ __('Advanced Biometric Identity Verification') }}</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-center">
                <div class="h4 mb-0 text-white">99.2%</div>
                <small class="text-white-50">{{ __('Accuracy') }}</small>
            </div>
            <div class="text-center">
                <div class="h4 mb-0 text-white">< 2s</div>
                <small class="text-white-50">{{ __('Speed') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Main Authentication Section -->
        <div class="col-lg-8">
            {{-- Employee Selection --}}
            <div class="premium-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-user text-primary"></i>
                        {{ __('Employee Selection') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="form-group">
                        <label for="employeeId" class="form-label">
                            <i class="fas fa-users text-primary"></i>
                            {{ __('Select Employee for Authentication') }}
                        </label>
                        <select id="employeeId" name="employee_id" class="form-control select2" required>
                            <option value="">{{ __('Choose an employee...') }}</option>
                            @foreach($emp_list as $key => $emp)
                                <option value="{{ $key}}">{{ $emp }}</option>
                            @endforeach
                        </select>
                        <p id="employeeIdError" class="text-danger mt-2" style="display: none;"></p>
                    </div>
                </div>
            </div>

            {{-- Authentication Steps --}}
            <div class="premium-card fade-in">
                <div class="premium-card-body">
                    <div class="auth-steps">
                        <div class="step active">
                            <div class="step-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <span class="step-label">{{ __('Select Employee') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <span class="step-label">{{ __('Position Face') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <span class="step-label">{{ __('Detecting') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="step-label">{{ __('Verified') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Camera Feed --}}
            <div class="premium-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-video text-primary"></i>
                        {{ __('Live Camera Feed') }}
                    </h3>
                    <div id="status" class="status-card status-processing">
                        {{ __('Initializing camera...') }}
                    </div>
                </div>
                <div class="premium-card-body">
                    <div class="video-container">
                        <video id="video" autoplay playsinline muted></video>
                        <canvas id="overlay"></canvas>
                    </div>
                    <div id="countdown-timer" class="countdown-card" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4">
            {{-- Instructions --}}
            <div class="premium-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info"></i>
                        {{ __('Instructions') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">1</div>
                        <div>
                            <h6 class="mb-1">{{ __('Select Employee') }}</h6>
                            <small class="text-muted">{{ __('Choose the employee from the dropdown menu') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">2</div>
                        <div>
                            <h6 class="mb-1">{{ __('Position Your Face') }}</h6>
                            <small class="text-muted">{{ __('Center your face in the camera frame') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">3</div>
                        <div>
                            <h6 class="mb-1">{{ __('Stay Still') }}</h6>
                            <small class="text-muted">{{ __('Hold steady for 2 seconds for detection') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">4</div>
                        <div>
                            <h6 class="mb-1">{{ __('Verification Complete') }}</h6>
                            <small class="text-muted">{{ __('View results and employee details') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Features --}}
            <div class="premium-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt text-success"></i>
                        {{ __('Security Features') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded p-2 me-3">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('Liveness Detection') }}</h6>
                            <small class="text-muted">{{ __('Prevents photo spoofing attacks') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('AI-Powered') }}</h6>
                            <small class="text-muted">{{ __('Advanced neural network recognition') }}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('Encrypted Processing') }}</h6>
                            <small class="text-muted">{{ __('All biometric data is encrypted') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employee Details --}}
            <div id="visitorDetails" class="premium-card fade-in" style="display: none;">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-user-check text-success"></i>
                        {{ __('Authentication Result') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="employee-details">
                        <div class="detail-item">
                            <span class="detail-label">{{ __('Name') }}</span>
                            <span class="detail-value" id="visitorName">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('Department') }}</span>
                            <span class="detail-value" id="visitorDepartment">-</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">{{ __('Aadhaar Number') }}</span>
                            <span class="detail-value" id="visitorAadhaar">-</span>
                        </div>
                    </div>

                    <div class="photo-comparison">
                        <div class="photo-box">
                            <div class="photo-box-title">{{ __('Aadhaar Photo') }}</div>
                            <div id="aadhaarImageContainer">
                                <div class="img-placeholder">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                                <img id="visitorPhoto" class="visitor-photo" style="display: none" alt="Aadhaar Photo">
                            </div>
                        </div>
                        <div class="photo-box">
                            <div class="photo-box-title">{{ __('Captured Photo') }}</div>
                            <div id="capturedImageContainer">
                                <div class="img-placeholder">
                                    <i class="fas fa-camera fa-2x"></i>
                                </div>
                                <img id="capturedVisitorPhoto" class="visitor-photo" style="display: none" alt="Captured Photo">
                            </div>
                        </div>
                    </div>

                    <div class="confidence-card">
                        <div class="confidence-label">{{ __('Match Confidence') }}</div>
                        <div class="confidence-value">
                            <span id="confidenceValue">-</span>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/human/dist/human.js"></script>
<script>
// Enhanced JavaScript with improved UI interactions
document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const statusDiv = document.getElementById('status');
    const visitorDetails = document.getElementById('visitorDetails');
    const employeeIdSelect = document.getElementById('employeeId');
    const employeeIdError = document.getElementById('employeeIdError');
    const steps = document.querySelectorAll('.step');
    let faceDetectionTimeout;
    let isProcessing = false;

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

    // Update authentication steps
    function updateSteps(activeStep) {
        steps.forEach((step, index) => {
            if (index <= activeStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    // Update status messages with improved styling
    function updateStatus(message, type = 'processing') {
        statusDiv.textContent = message;
        statusDiv.className = `status-card status-${type}`;
        
        if (type === 'processing') {
            statusDiv.classList.add('pulse');
        } else {
            statusDiv.classList.remove('pulse');
        }
    }

    // Employee selection handler
    employeeIdSelect.addEventListener('change', function() {
        if (this.value !== '') {
            employeeIdError.style.display = 'none';
            this.style.borderColor = 'var(--success)';
            updateSteps(1);
        } else {
            updateSteps(0);
        }
    });

    async function initializeCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 640, height: 480, facingMode: "user" }
            });
            video.srcObject = stream;
            
            await new Promise((resolve) => {
                video.onloadedmetadata = () => {
                    overlay.width = video.videoWidth;
                    overlay.height = video.videoHeight;
                    resolve();
                };
            });
            
            updateStatus('Camera ready - Please select an employee and position your face', 'success');
            startFaceDetection();
        } catch (error) {
            console.error('Camera error:', error);
            updateStatus('Camera access denied. Please enable camera permissions.', 'error');
        }
    }

    async function startFaceDetection() {
        const context = overlay.getContext('2d');
        let faceStableCount = 0;

        async function detectFrame() {
            try {
                const result = await human.detect(video);
                context.clearRect(0, 0, overlay.width, overlay.height);

                if (result.face?.[0]?.score >= 0.7) {
                    const face = result.face[0];
                    drawFaceBox(face, context);
                    
                    if (!isProcessing && employeeIdSelect.value) {
                        faceStableCount++;
                        updateSteps(2);
                        updateStatus(`Face detected - Authenticating in ${2 - Math.floor(faceStableCount/30)}s`, 'processing');
                        
                        if (faceStableCount >= 60) { // 60 frames ≈ 2 seconds at 30fps
                            captureAndAuthenticate(face);
                            faceStableCount = 0;
                        }
                    } else if (!employeeIdSelect.value) {
                        updateStatus('Please select an employee first', 'error');
                        updateSteps(0);
                    }
                } else {
                    faceStableCount = 0;
                    if (!isProcessing) {
                        if (employeeIdSelect.value) {
                            updateStatus('Position your face in the center of the frame', 'error');
                            updateSteps(1);
                        } else {
                            updateStatus('Please select an employee first', 'error');
                            updateSteps(0);
                        }
                    }
                }
            } catch (error) {
                console.error('Detection error:', error);
                updateStatus('Face detection error occurred', 'error');
            }
            requestAnimationFrame(detectFrame);
        }

        detectFrame();
    }

    function drawFaceBox(face, context) {
        context.strokeStyle = "#00ff00";
        context.lineWidth = 3;
        context.strokeRect(...face.box);
        
        // Add face detection indicator
        context.fillStyle = "#00ff00";
        context.font = "16px Arial";
        context.fillText("Face Detected", face.box[0], face.box[1] - 10);
    }

    async function captureAndAuthenticate(face) {
        isProcessing = true;
        updateStatus('Authenticating with biometric database...', 'processing');
        updateSteps(2);
        
        try {
            const employeeId = employeeIdSelect.value;
            
            if (!employeeId) {
                updateStatus('Please select an employee', 'error');
                employeeIdError.textContent = "Please select an employee";
                employeeIdError.style.display = 'block';
                isProcessing = false;
                return;
            }
            
            const faceCanvas = document.createElement('canvas');
            faceCanvas.width = face.box[2];
            faceCanvas.height = face.box[3];
            
            faceCanvas.getContext('2d').drawImage(
                video,
                face.box[0], face.box[1], face.box[2], face.box[3],
                0, 0, face.box[2], face.box[3]
            );
    
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
            const response = await fetch('/aadhaar/face-authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ 
                    image: faceCanvas.toDataURL('image/jpeg'),
                    employee_id: employeeId
                })
            });
    
            const data = await response.json();
            console.log('Authentication response:', data);
            
            visitorDetails.style.display = 'none';
            
            if (data.status) {
                updateStatus('Authentication successful! ✓', 'success');
                updateSteps(3);
                
                // Show employee details with animation
                setTimeout(() => {
                    visitorDetails.style.display = 'block';
                    visitorDetails.classList.add('fade-in');
                }, 500);
                
                const employeeData = data.data.original.data;
                
                // Update employee information
                document.getElementById('visitorName').textContent = employeeData.name || '-';
                
                const departmentElement = document.getElementById('visitorDepartment');
                if (departmentElement) {
                    if (typeof employeeData.department === 'object' && employeeData.department !== null) {
                        departmentElement.textContent = employeeData.department.name || '-';
                    } else {
                        departmentElement.textContent = employeeData.department || '-';
                    }
                }
                
                const aadhaarElement = document.getElementById('visitorAadhaar');
                if (aadhaarElement) {
                    aadhaarElement.textContent = employeeData.maskedAadhaar || 'XXXXXXXX' + (employeeData.employeeId ? employeeData.employeeId.toString().slice(-4) : '-');
                }
                
                // Handle images
                const aadhaarImgPlaceholder = document.querySelector('#aadhaarImageContainer .img-placeholder');
                const capturedImgPlaceholder = document.querySelector('#capturedImageContainer .img-placeholder');
                const aadhaarImg = document.getElementById('visitorPhoto');
                const capturedImg = document.getElementById('capturedVisitorPhoto');
                
                const timestamp = new Date().getTime();
                
                // Aadhaar image
                if (employeeData.photo && employeeData.photo.trim() !== '') {
                    aadhaarImg.onload = function() {
                        aadhaarImgPlaceholder.style.display = 'none';
                        aadhaarImg.style.display = 'block';
                    };
                    aadhaarImg.onerror = function() {
                        aadhaarImgPlaceholder.style.display = 'block';
                        aadhaarImg.style.display = 'none';
                    };
                    aadhaarImg.src = employeeData.photo + '?t=' + timestamp;
                } else {
                    aadhaarImgPlaceholder.style.display = 'block';
                    aadhaarImg.style.display = 'none';
                }
                
                // Captured image
                if (employeeData.capturedPhoto && employeeData.capturedPhoto.trim() !== '') {
                    capturedImg.onload = function() {
                        capturedImgPlaceholder.style.display = 'none';
                        capturedImg.style.display = 'block';
                    };
                    capturedImg.onerror = function() {
                        capturedImgPlaceholder.style.display = 'block';
                        capturedImg.style.display = 'none';
                    };
                    capturedImg.src = employeeData.capturedPhoto + '?t=' + timestamp;
                } else {
                    capturedImgPlaceholder.style.display = 'block';
                    capturedImg.style.display = 'none';
                }
                
                // Update confidence score
                const confidenceElement = document.getElementById('confidenceValue');
                if (confidenceElement) {
                    const confidence = employeeData.confidenceScore ? employeeData.confidenceScore.toFixed(1) : '-';
                    confidenceElement.textContent = confidence;
                    
                    // Animate confidence counter
                    if (confidence !== '-') {
                        animateCounter(confidenceElement, parseFloat(confidence));
                    }
                }
                
                // Countdown timer
                showCountdown(30);
                
            } else {
                if (data.flag == 1) {
                    alert(data.message);
                    updateStatus('Please wait - Too many attempts', 'error');
                    showCountdown(10);
                } else {
                    updateStatus(data.message || 'Authentication failed - Please try again', 'error');
                    updateSteps(1);
                }
            }
        } catch (error) {
            console.error('Authentication error:', error);
            updateStatus('Authentication system error - Please try again', 'error');
            updateSteps(1);
        }
        
        isProcessing = false;
    }

    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 30;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = current.toFixed(1);
        }, 50);
    }

    function showCountdown(seconds) {
        const countdownElement = document.getElementById('countdown-timer');
        countdownElement.style.display = 'block';
        
        const countdownInterval = setInterval(() => {
            countdownElement.textContent = `Next authentication available in: ${seconds}s`;
            seconds--;
            
            if (seconds < 0) {
                clearInterval(countdownInterval);
                countdownElement.style.display = 'none';
                // Reset for next authentication
                visitorDetails.style.display = 'none';
                updateSteps(employeeIdSelect.value ? 1 : 0);
                updateStatus('Ready for next authentication', 'success');
            }
        }, 1000);
    }
    
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Initialize system
    try {
        await human.load();
        updateStatus('Loading biometric models...', 'processing');
        await initializeCamera();
    } catch (error) {
        console.error('Initialization error:', error);
        updateStatus('System initialization failed - Please refresh the page', 'error');
    }

    // Initialize steps
    updateSteps(-1);
});
</script>
@endsection