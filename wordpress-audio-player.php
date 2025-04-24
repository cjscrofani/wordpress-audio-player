<?php
/**
 * Plugin Name: WordPress Audio Player
 * Plugin URI: https://golddust.co
 * Description: A simple audio player for WordPress with support for multiple tracks
 * Version: 1.0.2
 * Author: Gold Dust
 * Author URI: https://golddust.co
 * Text Domain: wordpress-audio-player
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Simple_Audio_Player {
    
    public function __construct() {
        // Register shortcode
        add_shortcode('audio_player', array($this, 'audio_player_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Enqueue necessary scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue only if shortcode is used
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'audio_player')) {
            wp_enqueue_style(
                'simple-audio-player-css',
                plugin_dir_url(__FILE__) . 'css/audio-player.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'simple-audio-player-js',
                plugin_dir_url(__FILE__) . 'js/audio-player.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }
    }
    
    /**
     * Get icon for track based on index
     */
    public function get_track_icon($index) {
        // Array of voice over related icons
        $icons = array(
            'dashicons-microphone',
            'dashicons-format-audio',
            'dashicons-megaphone',
            'dashicons-controls-volumeon',
            'dashicons-businessman',
            'dashicons-admin-users'
        );
        
        // Use modulo to cycle through icons if more tracks than icons
        $icon_index = $index % count($icons);
        
        return $icons[$icon_index];
    }
    public function audio_player_shortcode($atts) {
        // Default attributes
        $atts = shortcode_atts(
            array(
                'tracks' => '', // Comma-separated list of track URLs
                'titles' => '', // Comma-separated list of track titles
            ),
            $atts,
            'audio_player'
        );
        
        // Parse tracks and titles
        $track_urls = explode(',', $atts['tracks']);
        $track_titles = explode(',', $atts['titles']);
        
        // Generate unique ID for this player instance
        $player_id = 'audio-player-' . uniqid();
        
        // Start output buffer
        ob_start();
        ?>
        <div id="<?php echo esc_attr($player_id); ?>" class="simple-audio-player">
            <div class="sap-player-container">
                <div class="sap-tracks">
                    <?php 
                    foreach ($track_urls as $index => $url) :
                        $title = isset($track_titles[$index]) ? $track_titles[$index] : 'Track ' . ($index + 1);
                        $track_id = $player_id . '-track-' . $index;
                    ?>
                    <div class="sap-track" data-track-url="<?php echo esc_url(trim($url)); ?>" data-track-id="<?php echo esc_attr($track_id); ?>">
                        <div class="sap-track-info">
                            <div class="sap-track-title-container">
                                <span class="sap-track-icon dashicons <?php echo esc_attr($this->get_track_icon($index)); ?>"></span>
                                <h3 class="sap-track-title"><?php echo esc_html(trim($title)); ?></h3>
                            </div>
                            <div class="sap-time-display">
                                <span class="sap-current-time">0:00</span> / 
                                <span class="sap-duration">0:00</span>
                            </div>
                        </div>
                        <div class="sap-progress-container">
                            <div class="sap-progress-bar">
                                <div class="sap-progress"></div>
                            </div>
                        </div>
                        <div class="sap-controls">
                            <button class="sap-play-button" aria-label="Play">
                                <span class="dashicons dashicons-controls-play"></span>
                            </button>
                        </div>
                        <audio id="<?php echo esc_attr($track_id); ?>" preload="metadata">
                            <source src="<?php echo esc_url(trim($url)); ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        // Return output buffer content
        return ob_get_clean();
    }
}

// Initialize the plugin
new Simple_Audio_Player();

// Create required directories and files on plugin activation
register_activation_hook(__FILE__, 'simple_audio_player_activation');

function simple_audio_player_activation() {
    // Create CSS directory if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'css')) {
        mkdir(plugin_dir_path(__FILE__) . 'css', 0755);
    }
    
    // Create JS directory if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'js')) {
        mkdir(plugin_dir_path(__FILE__) . 'js', 0755);
    }
    
    // Create CSS file if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'css/audio-player.css')) {
        $css_content = '
        .simple-audio-player {
            max-width: 800px;
            margin: 20px auto;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        
        .sap-player-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border: 1px solid #ffecbb;
        }
        
        .sap-title {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 24px;
            color: #1d1d1d;
            font-weight: bold;
        }
        
        .sap-tracks {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .sap-track {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-left: 3px solid #ffecbb;
        }
        
        .sap-track-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .sap-track-title {
            margin: 0;
            font-size: 16px;
            font-weight: 500;
            color: #1d1d1d;
        }
        
        .sap-time-display {
            font-size: 14px;
            color: #7d8c75;
        }
        
        .sap-progress-container {
            margin-bottom: 15px;
        }
        
        .sap-progress-bar {
            height: 6px;
            background-color: #f0f0f0;
            border-radius: 3px;
            cursor: pointer;
            position: relative;
        }
        
        .sap-progress {
            height: 100%;
            background-color: #ffecbb;
            border-radius: 3px;
            width: 0;
        }
        
                .sap-play-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2a6b67;
        }
        
        .sap-play-button:hover {
            color: #ffecbb;
        }
        .sap-controls {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        ';
        file_put_contents(plugin_dir_path(__FILE__) . 'css/audio-player.css', $css_content);
    }
    
    // Create JS file if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'js/audio-player.js')) {
        $js_content = '
        (function($) {
            $(document).ready(function() {
                // Initialize all players on the page
                $(".simple-audio-player").each(function() {
                    initializePlayer($(this));
                });
                
                function initializePlayer($playerContainer) {
                    // Setup tracks
                    $playerContainer.find(".sap-track").each(function() {
                        const $track = $(this);
                        const trackId = $track.data("track-id");
                        const $audio = $track.find("audio")[0];
                        
                        // Play/Pause button
                        $track.find(".sap-play-button").on("click", function() {
                            const $button = $(this);
                            const $icon = $button.find("span");
                            
                            if ($audio.paused) {
                                // Pause all other tracks first
                                $playerContainer.find("audio").each(function() {
                                    if (this !== $audio) {
                                        this.pause();
                                        const $otherTrack = $(this).closest(".sap-track");
                                        $otherTrack.find(".sap-play-button span")
                                            .removeClass("dashicons-controls-pause")
                                            .addClass("dashicons-controls-play");
                                    }
                                });
                                
                                // Play this track
                                $audio.play();
                                $icon.removeClass("dashicons-controls-play")
                                     .addClass("dashicons-controls-pause");
                            } else {
                                // Pause this track
                                $audio.pause();
                                $icon.removeClass("dashicons-controls-pause")
                                     .addClass("dashicons-controls-play");
                            }
                        });
                        
                        // Progress bar
                        $track.find(".sap-progress-bar").on("click", function(e) {
                            const $progressBar = $(this);
                            const position = e.pageX - $progressBar.offset().left;
                            const percentage = position / $progressBar.width();
                            const seekTime = percentage * $audio.duration;
                            
                            $audio.currentTime = seekTime;
                        });
                        
                        // Mute button functionality removed
                        
                        // Set default volume
                        $audio.volume = 0.7;
                        
                        // Audio events
                        $audio.addEventListener("timeupdate", function() {
                            // Update progress bar
                            const percentage = ($audio.currentTime / $audio.duration) * 100;
                            $track.find(".sap-progress").css("width", percentage + "%");
                            
                            // Update current time display
                            $track.find(".sap-current-time").text(formatTime($audio.currentTime));
                        });
                        
                        $audio.addEventListener("loadedmetadata", function() {
                            $track.find(".sap-duration").text(formatTime($audio.duration));
                        });
                        
                        $audio.addEventListener("ended", function() {
                            $track.find(".sap-play-button span")
                                .removeClass("dashicons-controls-pause")
                                .addClass("dashicons-controls-play");
                            $track.find(".sap-progress").css("width", "0%");
                        });
                    });
                }
                
                // Helper function to format time display
                function formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = Math.floor(seconds % 60);
                    return mins + ":" + (secs < 10 ? "0" : "") + secs;
                }
            });
        })(jQuery);
        ';
        file_put_contents(plugin_dir_path(__FILE__) . 'js/audio-player.js', $js_content);
    }
}