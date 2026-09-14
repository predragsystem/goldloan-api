{{--
    Reusable optional photo widget: file upload OR live camera capture.
    Expects an optional $existingPhotoUrl variable when editing.
    Submits as a normal "photo" file field either way — camera capture
    just writes a captured frame into that same file input via JS.
--}}
<div x-data="photoCapture()" class="space-y-3">
    <label class="block text-sm font-medium text-ink mb-1">Photo (optional)</label>

    <div class="flex items-start gap-4">
        <div class="w-28 h-28 border border-paper-line bg-paper flex items-center justify-center overflow-hidden rounded-sm shrink-0">
            <img x-show="previewUrl" :src="previewUrl" class="w-full h-full object-cover">
            @isset($existingPhotoUrl)
                <img x-show="!previewUrl" src="{{ \Illuminate\Support\Facades\Storage::url($existingPhotoUrl) }}" class="w-full h-full object-cover">
            @else
                <span x-show="!previewUrl" class="text-xs text-ink-soft">No photo</span>
            @endisset
        </div>

        <div class="flex-1 space-y-2">
            <div class="flex gap-2">
                <label class="cursor-pointer border border-ink text-ink px-3 py-1.5 rounded-sm text-sm font-medium hover:bg-ink hover:text-paper transition-colors">
                    Upload file
                    <input type="file" name="photo" accept="image/*" class="hidden" x-ref="fileInput" @change="onFileChosen">
                </label>
                <button type="button" @click="toggleCamera"
                        class="border border-ink text-ink px-3 py-1.5 rounded-sm text-sm font-medium hover:bg-ink hover:text-paper transition-colors">
                    <span x-text="cameraOn ? 'Close camera' : 'Use camera'"></span>
                </button>
                <button type="button" x-show="previewUrl" @click="clearPhoto"
                        class="text-alert text-sm px-2">Remove</button>
            </div>

            <div x-show="cameraOn" class="space-y-2">
                <video x-ref="video" autoplay playsinline class="w-48 border border-paper-line rounded-sm"></video>
                <button type="button" @click="capture"
                        class="bg-ink text-paper px-3 py-1.5 rounded-sm text-sm font-medium hover:bg-brass-dark transition-colors">
                    Capture
                </button>
            </div>
            <canvas x-ref="canvas" class="hidden"></canvas>
        </div>
    </div>
</div>

<script>
function photoCapture() {
    return {
        previewUrl: null,
        cameraOn: false,
        stream: null,

        onFileChosen() {
            const file = this.$refs.fileInput.files[0];
            if (file) this.previewUrl = URL.createObjectURL(file);
        },

        async toggleCamera() {
            if (this.cameraOn) {
                this.stopCamera();
                return;
            }
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                this.$refs.video.srcObject = this.stream;
                this.cameraOn = true;
            } catch (e) {
                alert('Could not access the camera: ' + e.message);
            }
        },

        stopCamera() {
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
            this.cameraOn = false;
        },

        capture() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob((blob) => {
                const file = new File([blob], 'captured-photo.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.fileInput.files = dataTransfer.files;
                this.previewUrl = URL.createObjectURL(file);
                this.stopCamera();
            }, 'image/jpeg', 0.9);
        },

        clearPhoto() {
            this.previewUrl = null;
            this.$refs.fileInput.value = '';
        },
    };
}
</script>
