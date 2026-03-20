<?php

declare (strict_types=1);
/**
 * @package		OpenCart
 *
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 *
 * @see		   https://www.opencart.com
 */
namespace Opencart\System\Library;

/**
 * Class Mail
 *
 * Basic Mail Class
 */
class Mail
{
    /**
     * @var array<string, mixed>
     */
    private object $adaptor;
    /**
     * Constructor
     *
     * @param array<string, mixed> $option
     */
    public function __construct(string $adaptor = 'mail', array $option = [])
    {
        $class = 'Opencart\System\Library\Mail\\' . $adaptor;
        if (!class_exists($class)) {
            throw new \Exception('Error: Could not load mail adaptor ' . $adaptor . '!');
        }
        $this->adaptor = new $class($option);
    }
    /**
     * Set To
     *
     * @param array<string>|string $to
     */
    public function set_to(string|array $to): void
    {
        $this->adaptor->set_to($to);
    }
    /**
     * Set From
     *
     *
     */
    public function set_from(string $from): void
    {
        $this->adaptor->set_from($from);
    }
    /**
     * Set Sender
     *
     *
     */
    public function set_sender(string $sender): void
    {
        $this->adaptor->set_sender($sender);
    }
    /**
     * Set Reply To
     *
     *
     */
    public function set_reply_to(string $reply_to): void
    {
        $this->adaptor->set_reply_to($reply_to);
    }
    /**
     * Set Subject
     *
     *
     */
    public function set_subject(string $subject): void
    {
        $this->adaptor->set_subject($subject);
    }
    /**
     * Set Text
     *
     *
     */
    public function set_text(string $text): void
    {
        $this->adaptor->set_text($text);
    }
    /**
     * Set Html
     *
     *
     */
    public function set_html(string $html): void
    {
        $this->adaptor->set_html($html);
    }
    /**
     * Add Attachment
     *
     *
     */
    public function add_attachment(string $filename): void
    {
        $this->attachments[] = $filename;
    }
    /**
     * Send
     */
    public function send(): bool
    {
        return $this->adaptor->send();
    }
}