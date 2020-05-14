<?php
namespace App;

class Belette
{
    private string $text;

    public int $x = 0;

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function revert(): void
    {
        $text = $this->text;
        $count = mb_strlen($text);
        $result = '';
        while($count--) {
            $result .= mb_substr($text, $count, 1);
        }
        $this->text = $result;
    }
}