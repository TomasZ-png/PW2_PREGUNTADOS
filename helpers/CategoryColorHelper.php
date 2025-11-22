<?php

namespace helpers;
class CategoryColorHelper {
    public static function getColorFor($categoria) {
        $colores = [
            'CIENCIA' => '#fc8448',
            'GEOGRAFIA' => '#73bd58',
            'DEPORTES' => '#ba87d1',
            'ARTE' => '#fb4551',
            'HISTORIA' => '#3d9bd0'
        ];
        return $colores[$categoria] ?? '#cccccc'; // color por defecto
    }
}

