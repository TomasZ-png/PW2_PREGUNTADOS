<?php
//
//namespace helpers;
//class CategoryColorHelper {
//    public static function getColorFor($categoria) {
//        $colores = [
//            'CIENCIA' => '#fc8448',
//            'GEOGRAFIA' => '#73bd58',
//            'DEPORTES' => '#ba87d1',
//            'ARTE' => '#fb4551',
//            'HISTORIA' => '#3d9bd0'
//        ];
//        return $colores[$categoria] ?? '#cccccc'; // color por defecto
//    }
//}
//


namespace helpers;

class CategoryColorHelper
{

    private static $file = __DIR__ . "/categorias_colores.json";

    public static function getColorFor($categoria)
    {
        $cat = strtoupper(trim($categoria));

        $colors = self::cargarColores();

        if (isset($colors[$cat])) {
            return $colors[$cat];
        }

        // Si no existe color → generar uno único
        $new = self::generarColorUnico($colors);

        $colors[$cat] = $new;
        self::guardarColores($colors);

        return $new;
    }

    public static function asignarColor($categoria, $color = null)
    {
        $cat = strtoupper(trim($categoria));
        $colors = self::cargarColores();

        if ($color === null || $color === "" || $color === "#000000") {
            $color = self::generarColorUnico($colors);
        }

        $colors[$cat] = $color;

        self::guardarColores($colors);
    }

    private static function cargarColores()
    {
        if (!file_exists(self::$file)) return [];
        return json_decode(file_get_contents(self::$file), true) ?? [];
    }

    private static function guardarColores($data)
    {
        file_put_contents(self::$file, json_encode($data, JSON_PRETTY_PRINT));
    }

    private static function generarColorUnico($existingColors)
    {
        do {
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        } while (in_array($color, $existingColors));

        return $color;
    }
}
