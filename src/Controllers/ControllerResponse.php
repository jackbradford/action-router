<?php
/**
 * @file Controllers/ControllerResponse.php
 * This file provides a class that models a response from one of the system's
 * controllers in order to provide them with a uniform and, therefore,
 * predictable output.
 *
 */
namespace JackBradford\Disphatch\Controllers;

class ControllerResponse {

    private $success;
    private $cliMessage;
    private $data;
    private $content;

    /**
     * @method ControllerResponse::__construct
     * Create a response from a controller.
     *
     * @param bool $success
     * Denotes whether or not the operation requested of the controller was
     * successfully completed.
     *
     * @param str $cliMsg
     * A message to send to the user in the case that the action is invoked
     * via the CLI.
     *
     * @param array $data
     * Store data to return to the remote client via JSON. This facilitates the
     * use of the system to serve asynchronous data to web applications.
     *
     * @param str $content
     * Store (e.g) HTML content that should be inserted into the page template
     * assigned to the controller in the config file. This facilitates the
     * implementation of dynamic pages.
     *
     * @return ControllerResponse
     */
    final public function __construct(
        $success,
        $cliMsg=null,
        array $data=[],
        $content=null
    ) {

        try {

            $this->success = $this->validateSuccess($success);
            $this->cliMessage = (is_null($cliMsg))
                ? null
                : $this->validateString($cliMsg);
            $this->data = $data;
            $this->content = (is_null($content))
                ? null
                : $this->validateString($content);
        }
        catch (\Exception $e) {

            throw new \Exception(
                'Could not construct ControllerResponse: ' . $e->getMessage()
            );
        }
    }

    /**
     * @method ControllerResponse::isSuccess
     * Check whether the controller action succeeded in its execution.
     *
     * @return bool
     */
    public function isSuccess() {

        return $this->success;
    }

    /**
     * @method ControllerResponse::getCLIMessage
     * Get the message intended for the CLI user that was returned by the 
     * controller action.
     *
     * @return str
     */
    public function getCLIMessage() {

        return $this->cliMessage;
    }

    /**
     * @method ControllerResponse::getData
     * Get the array intended to be passed to the client application, which was
     * generated by the controller action.
     *
     * @return array
     */
    public function getData() {

        return $this->data;
    }

    /**
     * @method ControllerResponse::getJSONData
     * Get the data to be passed to the client application, converted into JSON
     * format.
     *
     * @return str
     */
    public function getJSONData() {

        return json_encode($this->data);
    }

    /**
     * @method ControllerResponse::getContent
     * Get the content generated by the controller action, intended to be 
     * inserted into the template assigned to the controller in the config
     * file.
     *
     * @return str
     */
    public function getContent() {

        return $this->content;
    }

    /**
     * @method ControllerResponse::validateSuccess
     * Check the validity of the $success parameter passed to the constructor.
     *
     * @return bool
     * Throws Exception on error.
     */
    private function validateSuccess($success) {

        if (!is_bool($success)) {

            throw new \InvalidArgumentException(
                'Boolean expected. ' . gettype($success) . ' given.'
            );
        }

        return $success;
    }

    /**
     * @method ControllerResponse::validateString
     * Check the validity of a string parameter.
     *
     * @return str
     * Throws Exception on error.
     */
    private function validateString($str) {

        if (!is_string($str)) {

            throw new \InvalidArgumentException(
                'String expected. ' . gettype($str) . ' given.'
            );
        }

        return $str;
    }
}

