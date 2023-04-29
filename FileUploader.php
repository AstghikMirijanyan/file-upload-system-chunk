<?php

class FileUploader
{
    private $filePath;
    private $fileName;
    private $chunk;
    private $chunks;

    public function __construct()
    {
        $this->filePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
        $this->fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
        $this->chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $this->chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
    }

    public function upload()
    {
        if (empty($_FILES) || $_FILES["file"]["error"]) {
            $this->verbose(0, "Failed to move uploaded file.");
        }

        if (!file_exists($this->filePath)) {
            if (!mkdir($this->filePath, 0777, true)) {
                $this->verbose(0, "Failed to create {$this->filePath}");
            }
        }

        $filePath = $this->filePath . DIRECTORY_SEPARATOR . $this->fileName;
        $out = @fopen("{$filePath}.part", $this->chunk == 0 ? "wb" : "ab");

        if (!$out) {
            $this->verbose(0, "Failed to open output stream");
        }

        $in = @fopen($_FILES["file"]["tmp_name"], "rb");

        if (!$in) {
            $this->verbose(0, "Failed to open input stream");
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($in);
        @fclose($out);
        @unlink($_FILES["file"]["tmp_name"]);

        if (!$this->chunks || $this->chunk == $this->chunks - 1) {
            rename("{$filePath}.part", $filePath);
        }

        $this->verbose(1, "Upload OK");
    }

    private function verbose($ok = 1, $info = "")
    {
        if ($ok == 0) {
            http_response_code(400);
        }

        exit(json_encode(["ok" => $ok, "info" => $info]));
    }
}
