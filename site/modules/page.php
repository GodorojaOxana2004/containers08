<?php
class Page {
    private $template;

    public function __construct($template) {
        if (!file_exists($template)) {
            throw new Exception("Template file not found: $template");
        }
        $this->template = $template;
    }

    public function Render($data) {
        $content = file_get_contents($this->template);
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }
        return $content;
    }
}
?>