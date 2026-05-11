<?php
class PdfReport {
    private $pages = [];
    private $current = [];
    private $y = 780;
    private $title = '';

    public function __construct($title) {
        $this->title = $title;
        $this->addPage();
    }

    public function addPage() {
        if (!empty($this->current)) {
            $this->pages[] = implode("\n", $this->current);
        }

        $this->current = [];
        $this->y = 780;
        $this->text(40, $this->y, 18, $this->title);
        $this->y -= 28;
        $this->line(40, $this->y, 555, $this->y);
        $this->y -= 22;
    }

    public function paragraph($text, $size = 10) {
        $this->text(40, $this->y, $size, $text);
        $this->y -= 18;
    }

    public function tableHeader($headers, $widths) {
        $this->ensureSpace(28);
        $x = 40;
        $top = $this->y + 8;
        $bottom = $this->y - 13;
        $this->rect(40, $bottom, array_sum($widths), 21, '0.92');
        foreach ($headers as $index => $header) {
            $this->text($x + 4, $this->y, 9, $header);
            $x += $widths[$index];
        }
        $this->line(40, $bottom, 40 + array_sum($widths), $bottom);
        $this->y -= 22;
    }

    public function tableRow($cells, $widths) {
        $this->ensureSpace(24);
        $x = 40;
        foreach ($cells as $index => $cell) {
            $this->text($x + 4, $this->y, 8, $this->fit($cell, max(8, (int) ($widths[$index] / 4.5))));
            $x += $widths[$index];
        }
        $this->line(40, $this->y - 8, 40 + array_sum($widths), $this->y - 8);
        $this->y -= 18;
    }

    public function output($filename) {
        if (!empty($this->current)) {
            $this->pages[] = implode("\n", $this->current);
            $this->current = [];
        }

        $pageCount = count($this->pages);
        $fontObject = 3 + ($pageCount * 2);
        $objects = [];
        $kids = [];

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObject = 3 + ($i * 2);
            $contentObject = $pageObject + 1;
            $kids[] = $pageObject . ' 0 R';
            $stream = $this->pages[$i];
            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . $fontObject . ' 0 R >> >> /Contents ' . $contentObject . ' 0 R >>';
            $objects[$contentObject] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . $pageCount . ' >>';
        $objects[$fontObject] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (max(array_keys($objects)) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= max(array_keys($objects)); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }
        $pdf .= "trailer\n<< /Size " . (max(array_keys($objects)) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function ensureSpace($height) {
        if ($this->y - $height < 42) {
            $this->addPage();
        }
    }

    private function text($x, $y, $size, $text) {
        $this->current[] = 'BT /F1 ' . (int) $size . ' Tf ' . (int) $x . ' ' . (int) $y . ' Td (' . $this->escape($text) . ') Tj ET';
    }

    private function line($x1, $y1, $x2, $y2) {
        $this->current[] = '0.75 w ' . (int) $x1 . ' ' . (int) $y1 . ' m ' . (int) $x2 . ' ' . (int) $y2 . ' l S';
    }

    private function rect($x, $y, $w, $h, $gray) {
        $this->current[] = $gray . ' g ' . (int) $x . ' ' . (int) $y . ' ' . (int) $w . ' ' . (int) $h . ' re f 0 g';
    }

    private function fit($text, $max) {
        $text = trim((string) $text);
        if (strlen($text) <= $max) {
            return $text;
        }

        return substr($text, 0, max(0, $max - 3)) . '...';
    }

    private function escape($text) {
        $text = str_replace(["\r", "\n"], ' ', (string) $text);
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($encoded === false) {
            $encoded = $text;
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);
    }
}
?>
