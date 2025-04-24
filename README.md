# WordPress Audio Player

A lightweight WordPress audio player plugin.

## Features

- Responsive layout that works on all devices
- Progress bar with seeking functionality
- Play/pause controls
- Simple shortcode implementation
- Lightweight with no external dependencies
- Uses WordPress native Dashicons

## Installation

1. Download the plugin files
2. Upload the `simple-audio-player` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## Usage

Use the following shortcode to embed the audio player in any post or page:

```
[audio_player tracks="https://example.com/track1.mp3,https://example.com/track2.mp3" titles="Commercial Demo,Character Reel"]
```

### Parameters

- `tracks`: Comma-separated list of audio file URLs (required)
- `titles`: Comma-separated list of track titles (required)

## Customization

You can customize the player's appearance by modifying the CSS in the `css/audio-player.css` file.

## Requirements

- WordPress 5.0 or higher
- Modern web browser
