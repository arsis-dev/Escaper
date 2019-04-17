<?php

/**
 * Easy handling of PHP errors with built-in redirect and persistent storage.
 * 
 * Setup Escaper in your configuration file that is included in all PHP pages.
 * Start by creating a new instance of Escaper and passing a default "escape
 * route" or URL where the user and error message will be sent.
 * 
 * Escaper lets you set an error message, redirect the user to a pre-defined URL,
 * then display that message.
 * 
 * To escape (error out) first make sure the route is defined, either by setting
 * it directly or by using your pre-defined default route. Then, call escape(),
 * passing the error message.
 * 
 * On a page where an error message might be shown:
 * Determine if an error exists using hasError(), then get the error string using
 * getError() or override the message() method to create a string containing the
 * error string and any HTML elements.
 *
 * @author JohnsenG
 */
class Escaper {

    const SESSION_KEY_ERROR = 'escaper_error';
    const SESSION_KEY_SUCCESS = 'escaper_success';
    const SESSION_VAR_FILTER = FILTER_SANITIZE_FULL_SPECIAL_CHARS;

    /**
     *
     * @var string The url to redirect to when we escape out
     */
    public $route;

    /**
     *
     * @var string The existing error message
     */
    private $error;

    /**
     *
     * @var string The existing success message
     */
    private $success_message;

    /**
     *
     * @var boolean
     */
    private $close_session = true;

    /**
     * 
     * @param boolean $close_session    Default true, the session will be closed
     *                                  after the constructor finishes and after
     *                                  an error string is saved
     * @param string $default_route     Default URL to use on a redirect
     */
    public function __construct($close_session = true, $default_route = false) {
        if ($default_route) $this->route = $default_route;
        if ($close_session) $this->close_session = $close_session;

        // Start the session
        $this->attemptSessionStart();
        // Ge, then clear our errors/success messages
        $this->getSavedError();
        $this->clearSavedError();
        $this->getSavedSuccess();
        $this->clearSavedSuccess();
        // Close the session
        if ($this->close_session) session_write_close();
    }

    /**
     * Determines if an error message has been recieved by Escaper
     * 
     * @return boolean  Returns true if an error message exists, false otherwise
     */
    public function hasError() {
        return !(!$this->error && !$this->success_message);
    }

    /**
     * Prints an error message if it exists.
     */
    public function message() {
        if ($this->error) echo '<div class="alert alert-warning"><i class="fa fa-warning float-left mr-2 mt-1"></i> ' . htmlspecialchars_decode($this->error) . '</div>';
        if ($this->success_message) echo '<div class="alert alert-success"><i class="fa fa-thumbs-up float-left mr-2 mt-1"></i> ' . htmlspecialchars_decode($this->success_message) . '</div>';
    }

    /**
     * Sets the error message and redirects the user.
     * 
     * @param string $message   The error message
     */
    public function escape($message) {
        $this->setSavedError($message);
        header('Location: ' . $this->route);
        exit();
    }

    /**
     * Sets the success message and redirects the user.
     * 
     * @param string $message   The success message
     */
    public function success($message = 'Success!') {
        $this->setSavedSuccess($message);
        header('Location: ' . $this->route);
        exit();
    }

    /**
     * Gets any error message in the session and sets it to $this->error.
     */
    private function getSavedError() {
        $this->error = $this->getSaved(self::SESSION_KEY_ERROR);
    }

    /**
     * Gets any success message in the session and sets it to $this->success_message.
     */
    private function getSavedSuccess() {
        $this->success_message = $this->getSaved(self::SESSION_KEY_SUCCESS);
    }

    /**
     * Gets a session variable using the provided key.
     * 
     * @param string $key
     * @return string
     */
    private function getSaved($key) {
        $this->attemptSessionStart();
        $str = filter_var($_SESSION[$key], self::SESSION_VAR_FILTER);
        if ($str) return $str;
        else return false;
    }

    /**
     * Clears the saved error message in the session.
     */
    private function clearSavedError() {
        $this->clearSaved(self::SESSION_KEY_ERROR);
    }

    /**
     * Clears the saved success message in the session.
     */
    private function clearSavedSuccess() {
        $this->clearSaved(self::SESSION_KEY_SUCCESS);
    }

    /**
     * Clears a session variable using the provided key.
     * 
     * @param string $key   The session variable key
     */
    private function clearSaved($key) {
        $this->attemptSessionStart();
        unset($_SESSION[$key]);
    }

    /**
     * Saves an error message in the session.
     * 
     * @param string $error The error message
     */
    private function setSavedError($error) {
        $this->error = $error;
        $this->setSaved(self::SESSION_KEY_ERROR, $error);
    }

    /**
     * Saves a success message in the session.
     * 
     * @param string $success The success message
     */
    private function setSavedSuccess($success) {
        $this->success_message = $success;
        $this->setSaved(self::SESSION_KEY_SUCCESS, $success);
    }

    /**
     * Sets a session variable using the provided key/value pair.
     * 
     * @param string $key   The session variable key
     * @param string $value The value of the session variable
     */
    private function setSaved($key, $value) {
        $this->attemptSessionStart();
        $_SESSION[$key] = $value;
        if ($this->close_session) session_write_close();
    }

    /**
     * Attempts to start a session if it isn't already started.
     */
    private function attemptSessionStart() {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

}
