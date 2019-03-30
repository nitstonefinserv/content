<?php 

namespace Reflexions\Content\Admin;

/*
 * Using the SweetAlert js library, this overrides the default alert() with a nicer version
 *
 * @link http://t4t5.github.io/sweetalert/ SweetAlert Library
 *
 * @example
 *   Flash::info('title', 'message')
 *   Flash::success('title', 'message')
 *   Flash::error('title', 'message')
 *
 *   // Can be passed an array of messages
 *   Flash::overlay('title', 'message', [info !default, success, error])
 */
class Flash
{
    
    /**
     * Stores flash settings to sessions
     * @param  {string} $title               - title of overlay
     * @param  {string} $message             - message to display
     * @param  {string} $level               - type of message (success, error, overlay, aside)
     * @param  {string} $key [flash_message] - default autocloses, `flash_notice_overlay` has a button that dismisses
     */
    static public function create($title, $message, $level, $key = 'flash_message')
    {
        return session()->flash($key, [
            'title'   => $title,
            'message' => $message,
            'level'   => $level,
        ]);
    }



    /**
     * Creates an info flash notice
     * @param  {string} $title   - title for overlay
     * @param  {string} $message - message
     */
    static public function info($title, $message)
    {
        Flash::create($title, $message, 'info');
    }



    /**
     * Creates a success flash notice
     * @param  {string} $title   - title for overlay
     * @param  {string} $message - message
     */
    static public function success($title, $message)
    {
        Flash::create($title, $message, 'success');
    }



    /**
     * Creates an error flash notice
     * @param  {string} $title   - title for overlay
     * @param  {string} $message - message
     */
    static public function error($title, $message)
    {
        Flash::create($title, $message, 'error');
    }



    /**
     * Creates a flash notice that needs to be dismissed via a button
     * @param  {string}         $title        - title for overlay
     * @param  {string | array} $message      - message
     * @param  {string}         $level [info] - type of overlay
     */
    static public function overlay($title, $message, $level = 'info')
    {
        Flash::create($title, $message, $level, 'flash_message_overlay');
    }
}