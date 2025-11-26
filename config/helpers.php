<?php
/**
 * Funciones auxiliares para el sistema
 */

/**
 * Escapar HTML de forma segura, manejando valores NULL
 * @param mixed $value Valor a escapar
 * @param string $default Valor por defecto si es NULL
 * @return string Valor escapado
 */
function safe_html($value, $default = '') {
    if ($value === null) {
        return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear fecha de forma segura
 * @param string|null $date Fecha a formatear
 * @param string $format Formato de salida
 * @return string Fecha formateada o 'N/A'
 */
function safe_date($date, $format = 'd/m/Y H:i:s') {
    if (empty($date)) {
        return 'N/A';
    }
    try {
        return date($format, strtotime($date));
    } catch (Exception $e) {
        return 'N/A';
    }
}

/**
 * Mostrar valor o N/A si está vacío
 * @param mixed $value Valor a mostrar
 * @param string $default Valor por defecto
 * @return string
 */
function display_value($value, $default = 'N/A') {
    return !empty($value) ? safe_html($value) : $default;
}

/**
 * Truncar texto de forma segura
 * @param string|null $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo a agregar
 * @return string
 */
function truncate_text($text, $length = 50, $suffix = '...') {
    if (empty($text)) {
        return 'N/A';
    }
    $text = safe_html($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}
?>
