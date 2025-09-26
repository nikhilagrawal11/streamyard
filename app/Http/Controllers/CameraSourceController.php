<?php

namespace App\Http\Controllers;

use App\Events\CameraSwitched;
use App\Models\CameraSource;
use App\Models\Stream;
use Illuminate\Http\Request;

class CameraSourceController extends Controller
{
    public function store(Request $request, Stream $stream)
    {
        $this->authorize('update', $stream);

        $request->validate([
            'source_name' => 'required|string|max:255',
            'source_type' => 'required|in:webcam,screen_share,uploaded_video,external_rtmp',
            'device_id' => 'nullable|string',
        ]);

        $source = $stream->cameraSources()->create([
            'user_id' => auth()->id(),
            'source_name' => $request->source_name,
            'source_type' => $request->source_type,
            'device_id' => $request->device_id,
            'settings' => [
                'resolution' => $request->resolution ?? '1280x720',
                'fps' => $request->fps ?? 30,
                'quality' => $request->quality ?? 'high',
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Camera source added successfully!',
            'source' => $source
        ]);
    }

    public function switch(CameraSource $source)
    {
        $this->authorize('update', $source->stream);

        $source->activate();

        event(new CameraSwitched($source->stream, $source));

        return response()->json([
            'success' => true,
            'message' => 'Camera switched successfully!',
            'active_source' => $source->fresh()
        ]);
    }

    public function destroy(CameraSource $source)
    {
        $this->authorize('update', $source->stream);

        if ($source->is_active)
        {
            return response()->json(['error' => 'Cannot delete the active camera source'], 422);
        }

        $source->delete();

        return response()->json([
            'success' => true,
            'message' => 'Camera source removed successfully!'
        ]);
    }

    public function updateSettings(Request $request, CameraSource $source)
    {
        $this->authorize('update', $source->stream);

        $request->validate([
            'resolution' => 'nullable|string',
            'fps' => 'nullable|integer|min:15|max:60',
            'quality' => 'nullable|in:low,medium,high,ultra',
        ]);

        $settings = array_merge($source->settings ?? [], [
            'resolution' => $request->resolution ?? '1280x720',
            'fps' => $request->fps ?? 30,
            'quality' => $request->quality ?? 'high',
        ]);

        $source->update(['settings' => $settings]);

        return response()->json([
            'success' => true,
            'message' => 'Camera settings updated successfully!',
            'source' => $source->fresh()
        ]);
    }
}
