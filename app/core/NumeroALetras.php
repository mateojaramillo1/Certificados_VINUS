<?php

namespace App\Core;

class NumeroALetras
{
    private static $unidades = ['', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
    private static $especiales = ['diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'];
    private static $decenas = ['', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
    private static $centenas = ['', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

    public static function convertir($numero)
    {
        if ($numero == 0) {
            return 'cero pesos';
        }

        $numero = intval($numero);
        
        if ($numero < 0) {
            return 'número negativo';
        }

        // Separar en billones, miles de millones, millones, miles y unidades
        $billones = intval($numero / 1000000000000);
        $numero %= 1000000000000;
        
        $milesMillones = intval($numero / 1000000000);
        $numero %= 1000000000;
        
        $millones = intval($numero / 1000000);
        $numero %= 1000000;
        
        $miles = intval($numero / 1000);
        $numero %= 1000;
        
        $unidades = $numero;

        $resultado = '';

        if ($billones > 0) {
            $resultado .= self::convertirGrupo($billones) . ' billón ';
        }

        if ($milesMillones > 0) {
            if ($milesMillones == 1) {
                $resultado .= 'mil millones ';
            } else {
                $resultado .= self::convertirGrupo($milesMillones) . ' mil millones ';
            }
        }

        if ($millones > 0) {
            if ($millones == 1) {
                $resultado .= 'un millón ';
            } else {
                $resultado .= self::convertirGrupo($millones) . ' millones ';
            }
        }

        if ($miles > 0) {
            if ($miles == 1) {
                $resultado .= 'mil ';
            } else {
                $resultado .= self::convertirGrupo($miles) . ' mil ';
            }
        }

        if ($unidades > 0) {
            $resultado .= self::convertirGrupo($unidades);
        }

        $resultado = trim($resultado);
        
        // Agregar "pesos" al final
        if ($resultado == 'un') {
            $resultado .= ' peso';
        } else {
            $resultado .= ' pesos';
        }

        return $resultado;
    }

    private static function convertirGrupo($numero)
    {
        if ($numero == 0) {
            return '';
        }

        $centena = intval($numero / 100);
        $decena = intval(($numero % 100) / 10);
        $unidad = $numero % 10;

        $resultado = '';

        // Centenas
        if ($centena > 0) {
            if ($numero == 100) {
                $resultado .= 'cien';
            } else {
                $resultado .= self::$centenas[$centena];
            }
        }

        // Decenas y unidades
        $resto = $numero % 100;
        if ($resto >= 10 && $resto <= 19) {
            $resultado .= ($resultado ? ' ' : '') . self::$especiales[$resto - 10];
        } elseif ($decena >= 2) {
            $resultado .= ($resultado ? ' ' : '') . self::$decenas[$decena];
            if ($unidad > 0) {
                if ($decena == 2) {
                    $resultado = substr($resultado, 0, -1); // Quitar la 'e' de 'veinte'
                    $resultado .= 'i' . self::$unidades[$unidad];
                } else {
                    $resultado .= ' y ' . self::$unidades[$unidad];
                }
            }
        } elseif ($unidad > 0) {
            $resultado .= ($resultado ? ' ' : '') . self::$unidades[$unidad];
        }

        return $resultado;
    }

    public static function convertirConCentavos($numero)
    {
        $partes = explode('.', number_format($numero, 2, '.', ''));
        $entero = intval($partes[0]);
        $centavos = isset($partes[1]) ? intval($partes[1]) : 0;

        $resultado = self::convertir($entero);
        
        if ($centavos > 0) {
            $resultado .= ' con ' . $centavos . '/100';
        }

        return $resultado;
    }
    
    /**
     * Convierte un día del mes (1-31) a letras
     * @param int $dia El número del día (1-31)
     * @return string El día en letras
     */
    public static function convertirDia($dia)
    {
        $dia = intval($dia);
        
        if ($dia < 1 || $dia > 31) {
            return 'día inválido';
        }
        
        // Casos especiales para días del 1 al 19
        if ($dia >= 1 && $dia <= 9) {
            return self::$unidades[$dia];
        } elseif ($dia >= 10 && $dia <= 19) {
            return self::$especiales[$dia - 10];
        }
        
        // Para días del 20 al 31
        $decena = intval($dia / 10);
        $unidad = $dia % 10;
        
        if ($decena == 2) {
            if ($unidad == 0) {
                return 'veinte';
            } else {
                return 'veinti' . self::$unidades[$unidad];
            }
        } elseif ($decena == 3) {
            if ($unidad == 0) {
                return 'treinta';
            } else {
                return 'treinta y ' . self::$unidades[$unidad];
            }
        }
        
        return 'día inválido';
    }
}
