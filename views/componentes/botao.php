<?php
declare(strict_types=1);

function botao(string $texto, string $id = '', string $variante = 'primary', string $tipo = 'button', string $classes = ''): void
{
    $atributoId = $id ? ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"' : '';
    $classe = trim("button button-{$variante} {$classes}");
    echo '<button' . $atributoId . ' class="' . $classe . '" type="' . $tipo . '">' . $texto . '</button>';
}
