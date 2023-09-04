<?php


namespace app\common\library;


class Bitmap
{

    private $bitmap;

    public function __construct($bitmap = 0)
    {
        $this->bitmap = gmp_init($bitmap);
    }

    public function set($index)
    {
        $bit = gmp_pow(2, $index);
        $this->bitmap = gmp_or($this->bitmap, $bit);
        return $this;
    }

    public function clear($index)
    {
        $bit = gmp_pow(2, $index);
        $this->bitmap = gmp_and($this->bitmap, gmp_com($bit));
    }

    public function exist($index): bool
    {
        return gmp_testbit($this->bitmap, $index);
    }

//    public function getAllValues(): array
//    {
//        $index = 0;
//        $values = [];
//        $bit = gmp_init(1);
//        while (gmp_cmp($bit, $this->bitmap) <= 0) {
//            if (gmp_testbit($this->bitmap, $index)) {
//                $values[] = $index;
//            }
//            $bit = gmp_mul($bit, 2);
//            $index++;
//        }
//        return $values;
//    }

    public function getBitmap(): string
    {
        return $this->bitmap;
    }

}