<!-- modal.blade.php -->
<div class="modal fade" id="musicModal" tabindex="-1" aria-labelledby="musicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="musicModalLabel">Music Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $imageSrc }}" alt="Image" class="img-fluid mb-3">
                <audio controls autoplay id="audioPlayer" class="d-none">
                    <source src="{{ $musicSrc }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        </div>
    </div>
</div>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // JavaScript to open modal automatically on page load
    $(document).ready(function() {
        $('#musicModal').modal('show');
        $('#musicModal').on('shown.bs.modal', function () {
            var audioPlayer = document.getElementById('audioPlayer');
            audioPlayer.play().then(function() {
                // Playback started successfully
                console.log('Audio playback started successfully.');
            }).catch(function(error) {
                // Handle any playback errors
                console.error('Error starting audio playback:', error);
            });
        });
       
    });
</script>
