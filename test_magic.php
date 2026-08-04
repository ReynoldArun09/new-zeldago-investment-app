<?php
class Test {
    public function __get($name) {
        return null;
    }
}
$t = new Test();
var_dump($t->inv->amount ?? 0);
