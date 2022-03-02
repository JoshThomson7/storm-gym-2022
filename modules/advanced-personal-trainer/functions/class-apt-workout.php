<?php
/**
 * APT Workout
 *
 * Class in charge of single workout
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_Workout {

    /**
	 * The post ID.
	 *
	 * @since 1.0
	 * @access   private
	 * @var      string
	 */
    protected $id;
    private $duaration = array();
    
    /**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 * @access public
	 * @param int $id
	 */
    public function __construct($id = null) {

        $this->id = $id;

    }

    /**
     * Gets post ID.
     * If not set, use global $post
     */
    public function id() {

        if($this->id) {

            return $this->id;

        } else {

            global $post;
            
            if(isset($post->ID)) {
                return $post->ID;
            }

        }

        return null;

    }

    /**
     * Returns post title
     */
    public function title() {

        return get_the_title($this->id());

    }

    /**
     * Returns permalink
     */
    public function url() {

        return get_permalink($this->id());

    }

    /**
     * Returns profile picture
     *
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @see vt_resize() in modules/wp-image-resize.php
     */
    public function image($width = 1200, $height = 800, $crop = true) {

        $attachment_id = get_field('workout_image', $this->id()) ?? null;

        if($attachment_id) {
            if($width || $height) {
                return vt_resize($attachment_id, '', $width, $height, $crop);
            } else {
                return $attachment_id;
            }
        }

        return false;

    }
    /**
     * Returns the service categories.
     * 
     * @param string $taxonomy
     * @param string $return | Accepts: all | all_with_object_id | ids | tt_ids | slugs | count | id=>parent | id=>name | id=>slug
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
     */
    public function get_terms($taxonomy = '', $return = null) {

        $args = $return ? array('fields' => $return) : array();        
        $post_terms = wp_get_object_terms($this->id(), $taxonomy, $args);

        if(!empty($post_terms) && !is_wp_error($post_terms)) {
            return $post_terms;
        }

        return null;

    }

    /**
     * Returns permalink
     */
    public function get_workout($key = '') {

        $get_workout = get_field('workout', $this->id) ?? array();
        $workout = new stdClass;
        $workout->totalTimeSeconds = 0;
        $workout->totalTimeMinutes = 0;
        $workout->sections = array();

        if(!empty($get_workout)) {
            foreach($get_workout as $workout_section) {

                $section = new stdClass;
                $section->title = $workout_section['section_name'];
                $section->videos = array();
                

                if(!empty($workout_section['videos'])) {
                    foreach($workout_section['videos'] as $workout_section_video) {
                        
                        $workout_video = new stdClass;
                        $workout_video->type = $workout_section_video['type'];
                        $workout_video->getReadyTime = new stdClass;
                        $workout_video->getReadyTime->time = $workout_section_video['get_ready_time'];
                        $workout_video->getReadyTime->timeSeconds = $this->get_seconds_from_time($workout_video->getReadyTime->time);

                        switch ($workout_video->type) {
                            
                            case 'video':

                                $video_id = $workout_section_video['video']['video'] ?? null;

                                $workout_video->platform = null;
                                $workout_video->videoId = null;
                                $workout_video->embedUrl = null;

                                if($video_id) {
                                    $video = new APT_Video($video_id);
                                    $workout_video->platform = $video->platform();
                                    $workout_video->title = $video->title();
                                    $workout_video->videoId = $video->video_id();
                                    $workout_video->voiceover = $workout_section_video['video']['voiceover'] ?? null;
                                    $workout_video->embedUrl = 'https://player.vimeo.com/video/'.$video->video_id();
                                }

                                // Duration
                                $duration = $workout_section_video['video']['duration'] ?? array();

                                if(!empty($duration)) {
                                    $workout_video->duration = new stdClass;
                                    $workout_video->duration->type = $duration['type'] ?? null;
                                    
                                    switch ($workout_video->duration->type) {
                                        case 'time':
                                            $workout_video->duration->time = $duration['time'];
                                            $workout_time_seconds = $this->get_seconds_from_time($duration['time']);
                                            $workout_video->duration->timeSeconds = $workout_time_seconds;
                                            $workout->totalTimeSeconds += $workout_time_seconds;
                                            break;
                                        
                                        case 'reps':
                                            $workout_video->duration->reps = $duration['reps'];

                                            // Reps don't have a time duration, so we just take for granted a rep will last 3 seconds 
                                            $workout_reps_seconds = $duration['reps'] ? $duration['reps'] * 3 : 0;
                                            $workout->totalTimeSeconds += $workout_reps_seconds;
                                            break;
                                        
                                        default:
                                            $workout_video->duration = 'off';
                                            break;
                                    }
                                }

                                break;

                            case 'rest':

                                $duration = $workout_section_video['resting_time'] ?? null;

                                if($duration) {
                                    $workout_video->duration = new stdClass;
                                    $workout_video->duration->time = $workout_section_video['resting_time'];
                                    $workout_video->duration->timeSeconds = $this->get_seconds_from_time($workout_video->duration->time);
                                    $workout->totalTimeSeconds += $workout_video->duration->timeSeconds;
                                }
                                
                                break;
                            
                            default:
                                # code...
                                break;
                        }

                        $workout_video->voice = array();
                        if(!empty($workout_section_video['voice'])) {
                            foreach($workout_section_video['voice'] as $workout_section_video_voice) {
                                $workout_video->voice[$workout_section_video_voice['seconds']] = $workout_section_video_voice['text'];
                            }
                        }

                        $workout->totalTimeMinutes = ceil($workout->totalTimeSeconds/60);

                        array_push($section->videos, $workout_video);
                    }



                    array_push($workout->sections, $section);

                }

            }

        }

        if($key && isset($workout->$key)) {
            return $workout->$key;
        }

        return $workout;
        

    }

    /**
     * Returns the workout duration
     * 
     * @return array
     */
    public function duration() {
        return array(
            'totalTimeMinutes' => $this->get_workout('totalTimeMinutes'),
            'totalTimeSeconds' => $this->get_workout('totalTimeSeconds')
        );
    }

    /**
     * Given a date string, workout the seconds
     * 
     * @param string $time
     */
    private function get_seconds_from_time($time) {
        return $time ? strtotime($time) - strtotime('TODAY') : 0;
    }

    /**
     * Rest API Data output
     * 
     * @return object $data
     */
    public function rest_api_data() {

        $data = new stdClass();

        $data->ID = $this->id();
        $data->title = html_entity_decode($this->title());
        $data->permalink = $this->url();
        $data->image = $this->image();
        $data->workout = $this->get_workout();
        $data->level = $this->get_terms('workout_level');
        $data->intensity = $this->get_terms('workout_intensity');
        $data->good_for = $this->get_terms('workout_good_for');
        $data->equipment = $this->get_terms('workout_equipment');
        
        return $data;

    }

}

