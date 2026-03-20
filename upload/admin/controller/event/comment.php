<?php

declare (strict_types=1);
namespace Opencart\Admin\Controller\Event;

/**
 * Class Comment
 *
 * @package Opencart\Admin\Controller\Event
 */
class Comment extends \Opencart\System\Engine\Controller
{
    /*
     * Add Comment
     *
     * Adds task to generate new comment data.
     *
     * Called using admin/model/cms/comment/addComment/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function add_comment(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'comment.' . $args['article_id'], 'action' => 'task/catalog/comment', 'args' => ['article_id' => $args['article_id']]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /*
     * Edit Comment
     *
     * Adds task to generate new comment data.
     *
     * Called using admin/model/cms/comment/editComment/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function edit_comment(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'comment.' . $args['article_id'], 'action' => 'task/catalog/comment', 'args' => ['article_id' => $args['article_id']]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
    /*
     * Delete Comment
     *
     * Adds task to generate new comment data.
     *
     * Called using admin/model/cms/comment/deleteComment/after
     *
     * @param string                $route
     * @param array<string, string> $args
     * @param array<string, string> $output
     *
     * @return void
     */
    public function delete_comment(string &$route, array &$args, &$output): void
    {
        $task_data = ['code' => 'comment.' . $args['article_id'], 'action' => 'task/catalog/comment', 'args' => ['article_id' => $args['article_id']]];
        $this->load->model('setting/task');
        $this->model_setting_task->add_task($task_data);
    }
}