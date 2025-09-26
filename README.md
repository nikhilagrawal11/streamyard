# StreamYard Clone - Live Streaming Platform

A full-featured live streaming platform built with Laravel 8.0, PHP 7.3, and MySQL 8.0, inspired by StreamYard. This application provides multi-camera live streaming, video upload & playback, pre-recorded stream scheduling, and a comprehensive host/guest interface.

## Features

### ✅ Core Features (MVP)
- **Multi-Camera Live Streaming**: Stream live video from multiple camera sources with real-time switching
- **Video Upload & Playback**: Upload video clips and play them during live streams
- **Pre-Recorded Stream Scheduling**: Upload and schedule pre-recorded content to go live automatically
- **Host/Guest Interface**: Host can invite multiple guests with grid layout display
- **Basic Control Panel**: Start/stop broadcast, switch cameras, play clips, manage participants

### 🚀 Additional Features
- **Real-time WebRTC Video Communication**: Direct peer-to-peer video/audio streaming
- **Live Chat**: Real-time messaging during streams
- **Stream Analytics**: Viewer count, participant tracking, stream statistics
- **User Authentication**: Secure login/registration with Laravel Breeze
- **Responsive Design**: Works on desktop and mobile devices
- **File Management**: Secure video upload with processing and thumbnail generation
- **Broadcasting**: Real-time notifications using Laravel WebSockets

## Technology Stack

- **Backend**: Laravel 8.0 (PHP 7.3)
- **Database**: MySQL 8.0
- **Authentication**: Laravel Breeze 1.0
- **Real-time**: Laravel WebSockets & Pusher
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Video Processing**: FFmpeg (for production)
- **WebRTC**: Native browser WebRTC APIs
- **Broadcasting**: Laravel Echo with Pusher/WebSockets

## Installation & Setup

### Prerequisites
- PHP 7.3+
- Composer
- Node.js & npm
- MySQL 8.0
- Git

### Step 1: Clone & Install Dependencies

```bash
# Clone the repository
git clone  streamyard-clone
cd streamyard-clone

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure .env File

```env
APP_NAME="StreamYard Clone"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=streamyard_clone
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting Configuration
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

# WebSocket Configuration
LARAVEL_WEBSOCKETS_PORT=6001

# Streaming Configuration
RTMP_HOST=localhost
RTMP_PORT=1935
HLS_HOST=localhost
HLS_PORT=8080

# File Upload Limits
MAX_VIDEO_SIZE=1048576
```

### Step 4: Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE streamyard_clone;
exit

# Run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### Step 5: Storage Setup

```bash
# Create storage directories
mkdir -p storage/app/public/videos
mkdir -p storage/app/public/streams
mkdir -p storage/app/public/thumbnails

# Create symbolic links
php artisan storage:link

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 6: Compile Assets

```bash
# Development
npm run dev

# Production
npm run production

# Watch for changes (development)
npm run watch
```

### Step 7: Start Development Servers

You'll need 4 terminal windows:

**Terminal 1: Laravel Application**
```bash
php artisan serve
```

**Terminal 2: WebSocket Server**
```bash
php artisan websockets:serve
```

**Terminal 3: Asset Watcher**
```bash
npm run watch
```

**Terminal 4: Queue Worker**
```bash
php artisan queue:work
```

## Usage

### Access the Application

- **Main Application**: http://localhost:8000
- **WebSocket Dashboard**: http://localhost:8000/laravel-websockets
- **API Documentation**: http://localhost:8000/api (if implemented)

### Default User Registration

1. Visit http://localhost:8000/register
2. Create an account
3. Login and access the dashboard

### Creating Your First Stream

1. Go to Dashboard → Create Stream
2. Fill in stream details (title, description, type)
3. Configure settings (participants limit, chat, recording)
4. Click "Create Stream"
5. Join the studio to start streaming

### Uploading Videos

1. Navigate to Videos → Upload Video
2. Select video file (supports MP4, AVI, MOV, etc.)
3. Add title and description
4. Upload and wait for processing

### Scheduling Pre-recorded Streams

1. First upload a video
2. Go to Schedules → Schedule Stream
3. Select the uploaded video
4. Set date/time for broadcast
5. Configure auto-start if needed

## API Documentation

### Stream Management

```
GET    /api/streams                 - List user streams
POST   /api/streams                 - Create new stream
GET    /api/streams/{uuid}          - Get stream details
PUT    /api/streams/{uuid}          - Update stream
DELETE /api/streams/{uuid}          - Delete stream

POST   /api/streams/{uuid}/start    - Start stream
POST   /api/streams/{uuid}/stop     - Stop stream
POST   /api/streams/{uuid}/join     - Join stream
POST   /api/streams/{uuid}/leave    - Leave stream
```

### Video Management

```
GET    /api/videos                  - List user videos
POST   /api/videos                  - Upload new video
GET    /api/videos/{uuid}           - Get video details
DELETE /api/videos/{uuid}           - Delete video
POST   /api/videos/{uuid}/play      - Get playback URL
```

### WebRTC Signaling

```
POST   /api/streams/{uuid}/signal       - Send WebRTC signal
POST   /api/streams/{uuid}/offer        - Send WebRTC offer
POST   /api/streams/{uuid}/answer       - Send WebRTC answer
POST   /api/streams/{uuid}/ice-candidate - Send ICE candidate
```

## Architecture Overview

### MVC Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── StreamController.php
│   │   ├── VideoUploadController.php
│   │   ├── StreamScheduleController.php
│   │   └── Api/
│   └── Requests/
├── Models/
│   ├── Stream.php
│   ├── StreamParticipant.php
│   ├── VideoUpload.php
│   └── StreamSchedule.php
├── Events/
│   ├── StreamStarted.php
│   ├── StreamEnded.php
│   └── ParticipantJoined.php
├── Jobs/
│   ├── ProcessVideoUpload.php
│   └── ScheduleStreamBroadcast.php
└── Policies/
    ├── StreamPolicy.php
    └── VideoUploadPolicy.php
```

### Database Schema

**Key Tables:**
- `streams` - Stream metadata and configuration
- `stream_participants` - Users participating in streams
- `video_uploads` - Uploaded video files and metadata
- `stream_schedules` - Scheduled broadcast information
- `camera_sources` - Camera/video source management

### Real-time Communication

- **Laravel Echo** for client-side event listening
- **Laravel WebSockets** for server-side broadcasting
- **WebRTC** for peer-to-peer video/audio streaming
- **Pusher Protocol** for real-time notifications

## Key Components

### Stream Studio
The main interface for live streaming with features:
- Multi-participant video grid
- Camera/microphone controls
- Screen sharing capability
- Live chat
- Participant management
- Video playback controls

### Video Processing
Handles uploaded video files:
- File validation and security
- Thumbnail generation
- Metadata extraction
- Background processing with queues
- Storage management

### WebRTC Implementation
Peer-to-peer video communication:
- ICE candidate exchange
- Offer/Answer negotiation
- Media stream management
- Connection state handling

## Production Deployment

### Additional Requirements for Production

1. **Streaming Server**: nginx-rtmp or Node Media Server
2. **FFmpeg**: For video processing and transcoding
3. **Redis**: For broadcasting and caching
4. **Supervisor**: For queue worker management
5. **SSL Certificate**: For WebRTC functionality
6. **CDN**: For video delivery optimization

### Production Configuration

```bash
# Install additional packages
composer require predis/predis
npm install --production

# Configure Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Configure supervisor for queues
sudo nano /etc/supervisor/conf.d/streamyard-worker.conf
```

### Security Considerations

- Enable HTTPS for WebRTC functionality
- Configure CORS for cross-origin requests
- Implement rate limiting for API endpoints
- Set up proper file upload validation
- Use environment variables for sensitive data
- Enable Laravel's built-in security features

## Troubleshooting

### Common Issues

**WebSocket Connection Failed**
- Ensure port 6001 is not blocked
- Check firewall settings
- Verify WebSocket server is running

**Video Upload Failures**
- Check PHP upload_max_filesize setting
- Verify storage directory permissions
- Ensure queue worker is processing jobs

**WebRTC Connection Issues**
- HTTPS required for production
- Check STUN/TURN server configuration
- Verify firewall allows WebRTC ports

**Database Connection Errors**
- Verify MySQL service is running
- Check database credentials in .env
- Ensure database exists

### Performance Optimization

1. **Enable caching**: Use Redis for sessions and cache
2. **Optimize images**: Compress thumbnails and avatars
3. **CDN integration**: Serve static assets from CDN
4. **Database indexing**: Add indexes to frequently queried columns
5. **Queue optimization**: Use Redis for queue backend

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Testing WebRTC

1. Open multiple browser tabs
2. Create a stream in one tab
3. Join the stream from other tabs
4. Test video/audio functionality
5. Verify real-time communication

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For issues and questions:
- Check the troubleshooting section
- Review the GitHub issues
- Create a new issue with detailed information

---

**Demo Video Requirements:**

Please create a video walkthrough demonstrating:

1. **Setup Process**: Show running the installation commands
2. **Stream Creation**: Create a new live stream
3. **Multi-Camera**: Switch between different camera sources
4. **Video Upload**: Upload and play a pre-recorded video
5. **Participant Management**: Invite and manage guests
6. **Scheduling**: Schedule a pre-recorded stream
7. **Live Studio**: Show the studio interface in action
8. **Real-time Features**: Demonstrate chat and participant interactions
9. **Stream Controls**: Start/stop streaming functionality
10. **Any Limitations**: Explain any features not fully implemented

---

## Development Timeline & Limitations

### What's Fully Implemented ✅
- Laravel 8.0 application structure
- User authentication with Laravel Breeze
- Complete database schema and models
- All CRUD operations for streams, videos, schedules
- Real-time broadcasting setup with WebSockets
- Basic WebRTC signaling infrastructure
- Video upload with progress tracking
- Stream scheduling system
- Participant management
- Authorization policies
- API endpoints for frontend integration

### What Requires Additional Setup ⚠️
- **RTMP/HLS Streaming Server**: Requires nginx-rtmp or Node Media Server
- **FFmpeg Integration**: For video processing and thumbnails
- **Production WebRTC**: Needs TURN servers for NAT traversal
- **Video Transcoding**: Background processing for different qualities
- **Advanced Stream Analytics**: View duration, engagement metrics

### Estimated Development Time
- **Core Features**: 16-20 hours ✅ (Completed)
- **WebRTC Implementation**: 4-6 hours (Basic structure provided)
- **Video Processing**: 2-4 hours (Queue job structure provided)
- **Production Deployment**: 4-8 hours
- **Testing & Refinement**: 4-6 hours

### Next Steps for Full Production
1. Set up RTMP streaming server (nginx-rtmp)
2. Implement FFmpeg video processing
3. Configure TURN servers for WebRTC
4. Add comprehensive error handling
5. Implement advanced analytics
6. Set up monitoring and logging
7. Create automated deployment pipeline

This codebase provides a solid foundation for a StreamYard-like platform with all the core features requested in the MVP. The application is ready for development testing and can be extended for production use with additional infrastructure components.