<?php

namespace DNX\S3;

abstract class Region {
    public static function sydney() { return new SydneyRegion(); }
    public static function northVirginia() { return new NorthVirginiaRegion(); }

    abstract public function code();

    /** @return Region[] */
    public static function all() {
        return [
            self::sydney(),
            self::northVirginia()
        ];
    }

    /** @return Region|null */
    public static function fromCode(string $code) {
        foreach(self::all() as $region) {
            if($region->code() === $code) {
                return $region;
            }
        }

        return null;
    }
}

class SydneyRegion extends Region {
    public function code() {
        return 'ap-southeast-2';
    }
}

class NorthVirginiaRegion extends Region {
    public function code() {
        return 'ap-southeast-2';
    }
}
