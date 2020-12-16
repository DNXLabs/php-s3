<?php

namespace DNX\S3;

abstract class Region {
    
    public static function ohio()               { return new Ohio(); }
    public static function northVirginia()      { return new NorthVirginia(); }
    public static function northCalifornia()    { return new NorthCalifornia(); }
    public static function oregon()             { return new Oregon(); }
    public static function capeTown()           { return new CapeTown(); }
    public static function hongKong()           { return new HongKong(); }
    public static function mumbai()             { return new Mumbai(); }
    public static function osakaLocal()         { return new OsakaLocal(); }
    public static function seoul()              { return new Seoul(); }
    public static function singapore()          { return new Singapore(); }
    public static function sydney()             { return new Sydney(); }
    public static function tokyo()              { return new Tokyo(); }
    public static function central()            { return new Central(); }
    public static function beijing()            { return new Beijing(); }
    public static function ningxia()            { return new Ningxia(); }
    public static function frankfurt()          { return new Frankfurt(); }
    public static function ireland()            { return new Ireland(); }
    public static function london()             { return new London(); }
    public static function milan()              { return new Milan(); }
    public static function paris()              { return new Paris(); }
    public static function stockholm()          { return new Stockholm(); }
    public static function bahrain()            { return new Bahrain(); }
    public static function saoPaulo()           { return new SaoPaulo(); }
    public static function usEast()             { return new USEast(); }
    public static function us()                 { return new US(); }
    
    abstract public function code();

    /** @return Region[] */
    public static function all() {
        return [
            self::ohio(),
            self::northVirginia(),
            self::northCalifornia(),
            self::oregon(),
            self::capeTown(),
            self::hongKong(),
            self::mumbai(),
            self::osakaLocal(),
            self::seoul(),
            self::singapore(),
            self::sydney(),
            self::tokyo(),
            self::central(),
            self::beijing(),
            self::ningxia(),
            self::frankfurt(),
            self::ireland(),
            self::london(),
            self::milan(),
            self::paris(),
            self::stockholm(),
            self::bahrain(),
            self::saoPaulo(),
            self::usEast(),
            self::us()
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

class Ohio extends Region {
    public function code() {
        return 'us-east-2';
    }
}

class NorthVirginia extends Region {
    public function code() {
        return 'us-east-1';
    }
}

class NorthCalifornia extends Region {
    public function code() {
        return 'us-west-1';
    }
}

class Oregon extends Region {
    public function code() {
        return 'us-west-2';
    }
}

class CapeTown extends Region {
    public function code() {
        return 'af-south-1';
    }
}

class HongKong extends Region {
    public function code() {
        return 'ap-east-1';
    }
}

class Mumbai extends Region {
    public function code() {
        return 'ap-south-1';
    }
}

class OsakaLocal extends Region {
    public function code() {
        return 'ap-northeast-3';
    }
}

class Seoul extends Region {
    public function code() {
        return 'ap-northeast-2';
    }
}

class Singapore extends Region {
    public function code() {
        return 'ap-southeast-1';
    }
}

class Sydney extends Region {
    public function code() {
        return 'ap-southeast-2';
    }
}

class Tokyo extends Region {
    public function code() {
        return 'ap-northeast-1';
    }
}

class Central extends Region {
    public function code() {
        return 'ca-central-1';
    }
}

class Beijing extends Region {
    public function code() {
        return 'cn-north-1';
    }
}

class Ningxia extends Region {
    public function code() {
        return 'cn-northwest-1';
    }
}

class Frankfurt extends Region {
    public function code() {
        return 'eu-central-1';
    }
}

class Ireland extends Region {
    public function code() {
        return 'eu-west-1';
    }
}

class London extends Region {
    public function code() {
        return 'eu-west-2';
    }
}

class Milan extends Region {
    public function code() {
        return 'eu-south-1';
    }
}

class Paris extends Region {
    public function code() {
        return 'eu-west-3';
    }
}

class Stockholm extends Region {
    public function code() {
        return 'eu-north-1';
    }
}

class Bahrain extends Region {
    public function code() {
        return 'me-south-1';
    }
}

class SaoPaulo extends Region {
    public function code() {
        return 'sa-east-1';
    }
}

class USEast extends Region {
    public function code() {
        return 'us-gov-east-1';
    }
}

class US extends Region {
    public function code() {
        return 'us-gov-west-1';
    }
}
