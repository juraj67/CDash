<?php
namespace CDash\Messaging\Notification\Email\Decorator;

class SummaryDecorator extends Decorator
{
    private $template = [
        'Project: {{ project_name }}',
        'Site: {{ site_name }}',
        'Build Name: {{ build_name }}',
        'Build Time: {{ build_time }}',
        'Type: {{ build_group }}',
        'Total {{ summary_description }}: {{ summary_count }}',
        '',
    ];

    /**
     * @return string
     */
    protected function getTemplate()
    {
        $template = implode("\n", $this->template);
        return $template;
    }

    public function setTemplateData($data)
    {
        parent::setTemplateData($data); // TODO: Change the autogenerated stub
    }
}