<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Studio: {{ $stream->title }}
            </h2>
            <div class="flex items-center space-x-4">
                <span id="stream-status" class="px-3 py-1 rounded-full text-sm font-medium
                    @if($stream->status === 'live') bg-red-100 text-red-800
                    @elseif($stream->status === 'scheduled') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($stream->status) }}
                </span>
                @if($participant->isHost())
                    <button id="toggle-stream"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        {{ $stream->isLive() ? 'Stop Stream' : 'Start Stream' }}
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-900">
        <!-- Main Studio Layout -->
        <div class="flex h-screen">
            <!-- Left Panel - Video Feeds -->
            <div class="flex-1 p-4">
                <!-- Main Video Feed -->
                <div class="bg-black rounded-lg mb-4 relative" style="aspect-ratio: 16/9;">
                    <video id="main-video" class="w-full h-full object-cover rounded-lg" autoplay muted playsinline>
                        <source src="" type="application/x-mpegURL">
                        Your browser does not support the video tag.
                    </video>

                    <!-- Video Controls Overlay -->
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="bg-black bg-opacity-50 rounded-lg p-3 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button id="toggle-camera"
                                        class="p-2 bg-gray-600 hover:bg-gray-700 rounded-full transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <button id="toggle-microphone"
                                        class="p-2 bg-gray-600 hover:bg-gray-700 rounded-full transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                    </svg>
                                </button>
                                <button id="share-screen"
                                        class="p-2 bg-gray-600 hover:bg-gray-700 rounded-full transition-colors">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span class="text-white text-sm">Viewers: <span
                                        id="viewer-count">{{ $stream->viewer_count }}</span></span>
                                @if($stream->isLive())
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></div>
                                        <span class="text-red-500 text-sm font-medium">LIVE</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participant Video Grid -->
                <div id="participants-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($stream->participants as $streamParticipant)
                        @if($streamParticipant->user_id !== auth()->id())
                            <div class="bg-gray-800 rounded-lg p-3" data-participant="{{ $streamParticipant->id }}">
                                <div class="relative bg-black rounded aspect-video mb-2">
                                    <video id="participant-{{ $streamParticipant->id }}"
                                           class="w-full h-full object-cover rounded" autoplay muted
                                           playsinline></video>
                                    <div
                                        class="absolute bottom-2 left-2 bg-black bg-opacity-50 px-2 py-1 rounded text-white text-xs">
                                        {{ $streamParticipant->participant_name }}
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-white">{{ $streamParticipant->participant_name }}</span>
                                    <div class="flex space-x-1">
                                        @if(!$streamParticipant->camera_enabled)
                                            <span class="text-red-400">📹</span>
                                        @endif
                                        @if(!$streamParticipant->microphone_enabled)
                                            <span class="text-red-400">🎤</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Right Panel - Controls & Chat -->
            <div class="w-80 bg-gray-800 p-4 flex flex-col">
                <!-- Stream Controls -->
                @if($participant->isHost())
                    <div class="bg-gray-700 rounded-lg p-4 mb-4">
                        <h3 class="text-white font-medium mb-3">Stream Controls</h3>

                        <!-- Camera Sources -->
                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm mb-2">Camera Source</label>
                            <select id="camera-sources" class="w-full bg-gray-600 text-white rounded px-3 py-2">
                                <option value="">Select camera...</option>
                            </select>
                        </div>

                        <!-- Video Upload Controls -->
                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm mb-2">Play Video</label>
                            <select id="video-uploads" class="w-full bg-gray-600 text-white rounded px-3 py-2 mb-2">
                                <option value="">Select video...</option>
                            </select>
                            <button id="play-video"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded transition-colors">
                                Play Selected Video
                            </button>
                        </div>

                        <!-- Participant Management -->
                        <div class="mb-4">
                            <h4 class="text-gray-300 text-sm mb-2">Invite Participants</h4>
                            <div class="flex space-x-2">
                                <input type="email" id="invite-email" placeholder="Email address"
                                       class="flex-1 bg-gray-600 text-white rounded px-3 py-2 text-sm">
                                <button id="send-invite"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                    Invite
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Participants List -->
                <div class="bg-gray-700 rounded-lg p-4 mb-4 flex-1 overflow-y-auto">
                    <h3 class="text-white font-medium mb-3">Participants ({{ $stream->participants->count() }})</h3>
                    <div id="participants-list" class="space-y-2">
                        @foreach($stream->participants as $streamParticipant)
                            <div class="flex items-center justify-between p-2 bg-gray-600 rounded"
                                 data-participant="{{ $streamParticipant->id }}">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                                        <span
                                            class="text-white text-xs">{{ substr($streamParticipant->participant_name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-white text-sm">{{ $streamParticipant->participant_name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $streamParticipant->role }}</p>
                                    </div>
                                </div>
                                @if($participant->isHost() && !$streamParticipant->isHost())
                                    <button class="kick-participant text-red-400 hover:text-red-300 text-xs"
                                            data-participant="{{ $streamParticipant->id }}">
                                        Remove
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Chat -->
                <div class="bg-gray-700 rounded-lg p-4 flex-1 flex flex-col">
                    <h3 class="text-white font-medium mb-3">Chat</h3>
                    <div id="chat-messages" class="flex-1 overflow-y-auto mb-3 space-y-2">
                        <!-- Chat messages will appear here -->
                    </div>
                    <div class="flex">
                        <input type="text" id="chat-input" placeholder="Type a message..."
                               class="flex-1 bg-gray-600 text-white rounded-l px-3 py-2 text-sm">
                        <button id="send-message"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r transition-colors">
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            class StreamStudio
            {
                constructor()
                {
                    this.stream = @json($stream);
                    this.participant = @json($participant);
                    this.localStream = null;
                    this.peerConnections = new Map();
                    this.isHost = this.participant.role === 'host';

                    this.init();
                }

                async init()
                {
                    // Initialize WebSocket connection
                    this.initializeWebSocket();

                    // Get user media
                    await this.getUserMedia();

                    // Initialize UI event listeners
                    this.initializeEventListeners();

                    // Load camera sources and videos
                    await this.loadCameraSources();
                    await this.loadVideoUploads();

                    // Join WebRTC room
                    this.joinRoom();
                }

                initializeWebSocket()
                {
                    // Using Laravel Echo with Pusher
                    window.Echo.channel(`stream.${this.stream.uuid}`)
                        .listen('StreamStarted', (e) =>
                        {
                            this.handleStreamStarted(e);
                        })
                        .listen('StreamEnded', (e) =>
                        {
                            this.handleStreamEnded(e);
                        })
                        .listen('ParticipantJoined', (e) =>
                        {
                            this.handleParticipantJoined(e);
                        })
                        .listen('ParticipantLeft', (e) =>
                        {
                            this.handleParticipantLeft(e);
                        })
                        .listen('CameraSwitched', (e) =>
                        {
                            this.handleCameraSwitched(e);
                        });

                    // WebRTC signaling
                    window.Echo.private(`user.${this.participant.user_id}`)
                        .listen('WebRTCSignal', (e) =>
                        {
                            this.handleWebRTCSignal(e);
                        });
                }

                async getUserMedia()
                {
                    try
                    {
                        const constraints = {
                            audio: true,
                            video: {
                                width: {ideal: 1280},
                                height: {ideal: 720},
                                frameRate: {ideal: 30}
                            }
                        };

                        this.localStream = await navigator.mediaDevices.getUserMedia(constraints);

                        // Display local video in main feed
                        const mainVideo = document.getElementById('main-video');
                        mainVideo.srcObject = this.localStream;

                    } catch (error)
                    {
                        console.error('Error accessing camera/microphone:', error);
                        this.showError('Unable to access camera or microphone');
                    }
                }

                initializeEventListeners()
                {
                    // Stream controls
                    const toggleStreamBtn = document.getElementById('toggle-stream');
                    if (toggleStreamBtn)
                    {
                        toggleStreamBtn.addEventListener('click', () => this.toggleStream());
                    }

                    // Media controls
                    document.getElementById('toggle-camera').addEventListener('click', () => this.toggleCamera());
                    document.getElementById('toggle-microphone').addEventListener('click', () => this.toggleMicrophone());
                    document.getElementById('share-screen').addEventListener('click', () => this.shareScreen());

                    // Video controls
                    document.getElementById('play-video').addEventListener('click', () => this.playSelectedVideo());

                    // Participant management
                    document.getElementById('send-invite').addEventListener('click', () => this.inviteParticipant());

                    // Chat
                    document.getElementById('send-message').addEventListener('click', () => this.sendChatMessage());
                    document.getElementById('chat-input').addEventListener('keypress', (e) =>
                    {
                        if (e.key === 'Enter') this.sendChatMessage();
                    });

                    // Camera source selection
                    document.getElementById('camera-sources').addEventListener('change', (e) =>
                    {
                        this.switchCameraSource(e.target.value);
                    });

                    // Participant removal
                    document.addEventListener('click', (e) =>
                    {
                        if (e.target.classList.contains('kick-participant'))
                        {
                            this.kickParticipant(e.target.dataset.participant);
                        }
                    });
                }

                async loadCameraSources()
                {
                    try
                    {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        const videoDevices = devices.filter(device => device.kind === 'videoinput');

                        const select = document.getElementById('camera-sources');
                        select.innerHTML = '<option value="">Select camera...</option>';

                        videoDevices.forEach(device =>
                        {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.textContent = device.label || `Camera ${select.children.length}`;
                            select.appendChild(option);
                        });

                    } catch (error)
                    {
                        console.error('Error loading camera sources:', error);
                    }
                }

                async loadVideoUploads()
                {
                    try
                    {
                        const response = await fetch('/api/videos?status=ready');
                        const data = await response.json();

                        const select = document.getElementById('video-uploads');
                        select.innerHTML = '<option value="">Select video...</option>';

                        data.data.forEach(video =>
                        {
                            const option = document.createElement('option');
                            option.value = video.uuid;
                            option.textContent = video.title;
                            select.appendChild(option);
                        });

                    } catch (error)
                    {
                        console.error('Error loading videos:', error);
                    }
                }

                async toggleStream()
                {
                    const btn = document.getElementById('toggle-stream');
                    const isLive = this.stream.status === 'live';

                    try
                    {
                        const endpoint = isLive ? 'stop' : 'start';
                        const response = await fetch(`/api/streams/${this.stream.uuid}/${endpoint}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            this.stream = data.stream;
                            this.updateStreamStatus();
                        }

                    } catch (error)
                    {
                        console.error('Error toggling stream:', error);
                        this.showError('Failed to toggle stream');
                    }
                }

                toggleCamera()
                {
                    if (this.localStream)
                    {
                        const videoTrack = this.localStream.getVideoTracks()[0];
                        if (videoTrack)
                        {
                            videoTrack.enabled = !videoTrack.enabled;
                            this.updateParticipantSettings({camera_enabled: videoTrack.enabled});

                            const btn = document.getElementById('toggle-camera');
                            btn.classList.toggle('bg-red-600', !videoTrack.enabled);
                        }
                    }
                }

                toggleMicrophone()
                {
                    if (this.localStream)
                    {
                        const audioTrack = this.localStream.getAudioTracks()[0];
                        if (audioTrack)
                        {
                            audioTrack.enabled = !audioTrack.enabled;
                            this.updateParticipantSettings({microphone_enabled: audioTrack.enabled});

                            const btn = document.getElementById('toggle-microphone');
                            btn.classList.toggle('bg-red-600', !audioTrack.enabled);
                        }
                    }
                }

                async shareScreen()
                {
                    try
                    {
                        const screenStream = await navigator.mediaDevices.getDisplayMedia({
                            video: true,
                            audio: true
                        });

                        // Replace video track in peer connections
                        const videoTrack = screenStream.getVideoTracks()[0];
                        this.peerConnections.forEach(async (pc) =>
                        {
                            const sender = pc.getSenders().find(s =>
                                s.track && s.track.kind === 'video'
                            );
                            if (sender)
                            {
                                await sender.replaceTrack(videoTrack);
                            }
                        });

                        // Update main video display
                        const mainVideo = document.getElementById('main-video');
                        mainVideo.srcObject = screenStream;

                        // Listen for screen share end
                        videoTrack.addEventListener('ended', () =>
                        {
                            this.stopScreenShare();
                        });

                        this.updateParticipantSettings({screen_sharing: true});

                    } catch (error)
                    {
                        console.error('Error sharing screen:', error);
                    }
                }

                async stopScreenShare()
                {
                    // Switch back to camera
                    await this.getUserMedia();
                    this.updateParticipantSettings({screen_sharing: false});
                }

                async playSelectedVideo()
                {
                    const select = document.getElementById('video-uploads');
                    const videoUuid = select.value;

                    if (!videoUuid)
                    {
                        this.showError('Please select a video to play');
                        return;
                    }

                    try
                    {
                        const response = await fetch(`/api/videos/${videoUuid}/play`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            // Update main video to play the selected video
                            const mainVideo = document.getElementById('main-video');
                            mainVideo.src = data.video_url;
                            mainVideo.play();
                        }

                    } catch (error)
                    {
                        console.error('Error playing video:', error);
                        this.showError('Failed to play video');
                    }
                }

                async inviteParticipant()
                {
                    const emailInput = document.getElementById('invite-email');
                    const email = emailInput.value.trim();

                    if (!email)
                    {
                        this.showError('Please enter an email address');
                        return;
                    }

                    try
                    {
                        const response = await fetch(`/api/streams/${this.stream.uuid}/participants`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                email: email,
                                name: email.split('@')[0],
                                role: 'guest'
                            })
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            emailInput.value = '';
                            this.showSuccess('Invitation sent successfully');
                        }

                    } catch (error)
                    {
                        console.error('Error inviting participant:', error);
                        this.showError('Failed to send invitation');
                    }
                }

                sendChatMessage()
                {
                    const input = document.getElementById('chat-input');
                    const message = input.value.trim();

                    if (!message) return;

                    // Add message to chat
                    this.addChatMessage({
                        user: this.participant.participant_name,
                        message: message,
                        timestamp: new Date()
                    });

                    // Broadcast to other participants
                    window.Echo.channel(`stream.${this.stream.uuid}`)
                        .whisper('chat-message', {
                            user: this.participant.participant_name,
                            message: message,
                            timestamp: new Date()
                        });

                    input.value = '';
                }

                addChatMessage(data)
                {
                    const messagesContainer = document.getElementById('chat-messages');
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'text-sm';

                    const time = new Date(data.timestamp).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
                    messageDiv.innerHTML = `
                    <div class="text-gray-400 text-xs">${time}</div>
                    <div><span class="text-blue-400 font-medium">${data.user}:</span> <span class="text-white">${data.message}</span></div>
                `;

                    messagesContainer.appendChild(messageDiv);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }

                async updateParticipantSettings(settings)
                {
                    try
                    {
                        await fetch(`/api/participants/${this.participant.id}/settings`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(settings)
                        });
                    } catch (error)
                    {
                        console.error('Error updating participant settings:', error);
                    }
                }

                async kickParticipant(participantId)
                {
                    if (!confirm('Are you sure you want to remove this participant?'))
                    {
                        return;
                    }

                    try
                    {
                        const response = await fetch(`/api/participants/${participantId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        if (data.success)
                        {
                            // Remove from UI
                            const participantElement = document.querySelector(`[data-participant="${participantId}"]`);
                            if (participantElement)
                            {
                                participantElement.remove();
                            }
                        }

                    } catch (error)
                    {
                        console.error('Error removing participant:', error);
                        this.showError('Failed to remove participant');
                    }
                }

                // WebRTC methods
                joinRoom()
                {
                    // Initialize peer connections for existing participants
                    this.stream.participants.forEach(participant =>
                    {
                        if (participant.user_id !== this.participant.user_id && participant.status === 'joined')
                        {
                            this.createPeerConnection(participant.user_id);
                        }
                    });
                }

                createPeerConnection(userId)
                {
                    const pc = new RTCPeerConnection({
                        iceServers: [
                            {urls: 'stun:stun.l.google.com:19302'},
                            {urls: 'stun:stun1.l.google.com:19302'}
                        ]
                    });

                    // Add local stream tracks
                    if (this.localStream)
                    {
                        this.localStream.getTracks().forEach(track =>
                        {
                            pc.addTrack(track, this.localStream);
                        });
                    }

                    // Handle remote stream
                    pc.ontrack = (event) =>
                    {
                        const [remoteStream] = event.streams;
                        const participantVideo = document.getElementById(`participant-${userId}`);
                        if (participantVideo)
                        {
                            participantVideo.srcObject = remoteStream;
                        }
                    };

                    // Handle ICE candidates
                    pc.onicecandidate = (event) =>
                    {
                        if (event.candidate)
                        {
                            this.sendSignalingMessage('ice-candidate', event.candidate, userId);
                        }
                    };

                    this.peerConnections.set(userId, pc);
                    return pc;
                }

                async sendSignalingMessage(type, data, targetUserId)
                {
                    try
                    {
                        await fetch(`/api/streams/${this.stream.uuid}/signal`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                type: type,
                                data: data,
                                target_user_id: targetUserId
                            })
                        });
                    } catch (error)
                    {
                        console.error('Error sending signaling message:', error);
                    }
                }

                async handleWebRTCSignal(event)
                {
                    const {signal_type, signal_data, from_user_id} = event;

                    let pc = this.peerConnections.get(from_user_id);
                    if (!pc)
                    {
                        pc = this.createPeerConnection(from_user_id);
                    }

                    switch (signal_type)
                    {
                        case 'offer':
                            await pc.setRemoteDescription(new RTCSessionDescription(signal_data));
                            const answer = await pc.createAnswer();
                            await pc.setLocalDescription(answer);
                            this.sendSignalingMessage('answer', answer, from_user_id);
                            break;

                        case 'answer':
                            await pc.setRemoteDescription(new RTCSessionDescription(signal_data));
                            break;

                        case 'ice-candidate':
                            await pc.addIceCandidate(new RTCIceCandidate(signal_data));
                            break;
                    }
                }

                // Event handlers
                handleStreamStarted(event)
                {
                    this.stream.status = 'live';
                    this.updateStreamStatus();
                }

                handleStreamEnded(event)
                {
                    this.stream.status = 'ended';
                    this.updateStreamStatus();
                }

                handleParticipantJoined(event)
                {
                    // Add new participant to UI
                    this.addParticipantToUI(event.participant);

                    // Create peer connection
                    this.createPeerConnection(event.participant.user_id);
                }

                handleParticipantLeft(event)
                {
                    // Remove participant from UI
                    const participantElement = document.querySelector(`[data-participant="${event.participant.id}"]`);
                    if (participantElement)
                    {
                        participantElement.remove();
                    }

                    // Close peer connection
                    const pc = this.peerConnections.get(event.participant.user_id);
                    if (pc)
                    {
                        pc.close();
                        this.peerConnections.delete(event.participant.user_id);
                    }
                }

                handleCameraSwitched(event)
                {
                    // Update UI to reflect active camera source
                    console.log('Camera switched:', event.camera_source);
                }

                // UI Helper methods
                updateStreamStatus()
                {
                    const statusElement = document.getElementById('stream-status');
                    const toggleBtn = document.getElementById('toggle-stream');

                    statusElement.textContent = this.stream.status.charAt(0).toUpperCase() + this.stream.status.slice(1);
                    statusElement.className = `px-3 py-1 rounded-full text-sm font-medium ${
                        this.stream.status === 'live' ? 'bg-red-100 text-red-800' :
                            this.stream.status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-gray-100 text-gray-800'
                    }`;

                    if (toggleBtn)
                    {
                        toggleBtn.textContent = this.stream.status === 'live' ? 'Stop Stream' : 'Start Stream';
                    }
                }

                addParticipantToUI(participant)
                {
                    // Add to participants list
                    const participantsList = document.getElementById('participants-list');
                    const participantDiv = document.createElement('div');
                    participantDiv.className = 'flex items-center justify-between p-2 bg-gray-600 rounded';
                    participantDiv.dataset.participant = participant.id;
                    participantDiv.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-white text-xs">${participant.participant_name.charAt(0)}</span>
                        </div>
                        <div>
                            <p class="text-white text-sm">${participant.participant_name}</p>
                            <p class="text-gray-400 text-xs">${participant.role}</p>
                        </div>
                    </div>
                    ${this.isHost && participant.role !== 'host' ?
                        `<button class="kick-participant text-red-400 hover:text-red-300 text-xs" data-participant="${participant.id}">Remove</button>` :
                        ''
                    }
                `;
                    participantsList.appendChild(participantDiv);

                    // Add to video grid if not host
                    if (participant.user_id !== this.participant.user_id)
                    {
                        const videoGrid = document.getElementById('participants-grid');
                        const videoDiv = document.createElement('div');
                        videoDiv.className = 'bg-gray-800 rounded-lg p-3';
                        videoDiv.dataset.participant = participant.id;
                        videoDiv.innerHTML = `
                        <div class="relative bg-black rounded aspect-video mb-2">
                            <video id="participant-${participant.id}" class="w-full h-full object-cover rounded" autoplay muted playsinline></video>
                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 px-2 py-1 rounded text-white text-xs">
                                ${participant.participant_name}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white">${participant.participant_name}</span>
                            <div class="flex space-x-1">
                                ${!participant.camera_enabled ? '<span class="text-red-400">📹</span>' : ''}
                                ${!participant.microphone_enabled ? '<span class="text-red-400">🎤</span>' : ''}
                            </div>
                        </div>
                    `;
                        videoGrid.appendChild(videoDiv);
                    }
                }

                showError(message)
                {
                    // Create toast notification for errors
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(() =>
                    {
                        toast.remove();
                    }, 5000);
                }

                showSuccess(message)
                {
                    // Create toast notification for success
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(() =>
                    {
                        toast.remove();
                    }, 5000);
                }
            }

            // Initialize the studio when DOM is loaded
            document.addEventListener('DOMContentLoaded', () =>
            {
                new StreamStudio();
            });

            // Listen for chat messages
            window.Echo.channel(`stream.{{ $stream->uuid }}`)
                .listenForWhisper('chat-message', (e) =>
                {
                    if (window.streamStudio && window.streamStudio.addChatMessage)
                    {
                        window.streamStudio.addChatMessage(e);
                    }
                });
        </script>
    @endpush
</x-app-layout>
